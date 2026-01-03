"""
Inventory Service - Stock calculation and reservation logic

Handles reservation-based inventory where stock is reserved (not removed)
when orders are placed, and only removed when parts are scanned/filled.
"""

from typing import Dict, List
from sqlalchemy import select, func
from sqlalchemy.ext.asyncio import AsyncSession

from app.db.models import Part, OrderItemPart, OrderItem, Order


class InventoryService:
    """
    Service for calculating available inventory considering reservations.
    
    Stock Philosophy:
    -----------------
    1. When an order is placed → parts are RESERVED (not removed from stock)
    2. During fulfillment → parts are scanned/marked as filled → THEN removed from stock
    3. Customer-facing site shows: Available = Physical Stock - Reserved (unfilled orders)
    4. Packing slip only includes items with all parts scanned/filled
    """
    
    def __init__(self, db: AsyncSession):
        self.db = db
    
    async def get_part_availability(self, part_id: int) -> Dict:
        """
        Get comprehensive availability info for a part.
        
        Returns:
            {
                "part_id": int,
                "physical_stock": int,  # Actual stock on hand
                "reserved": int,        # Reserved by unfilled orders
                "available": int,       # physical_stock - reserved
                "pending_orders": int   # Number of orders waiting for this part
            }
        """
        # Get the part
        part_result = await self.db.execute(
            select(Part).where(Part.id == part_id)
        )
        part = part_result.scalar_one_or_none()
        
        if not part:
            return None
        
        # Calculate reserved quantity (parts in unfilled/unscanned order items)
        reserved_query = (
            select(func.sum(OrderItemPart.quantity))
            .join(OrderItem, OrderItemPart.order_item_id == OrderItem.id)
            .join(Order, OrderItem.order_id == Order.id)
            .where(
                OrderItemPart.part_id == part_id,
                OrderItemPart.is_scanned == False,  # Not yet scanned
                Order.status_id.in_([1, 2, 3])  # Pending, Processing, or Processed
            )
        )
        
        reserved_result = await self.db.execute(reserved_query)
        reserved = reserved_result.scalar() or 0
        
        # Count pending orders
        pending_orders_query = (
            select(func.count(func.distinct(Order.id)))
            .join(OrderItem, Order.id == OrderItem.order_id)
            .join(OrderItemPart, OrderItem.id == OrderItemPart.order_item_id)
            .where(
                OrderItemPart.part_id == part_id,
                OrderItemPart.is_scanned == False,
                Order.status_id.in_([1, 2, 3])
            )
        )
        
        pending_result = await self.db.execute(pending_orders_query)
        pending_orders = pending_result.scalar() or 0
        
        physical_stock = part.stock_quantity or 0
        available = max(0, physical_stock - reserved)
        
        return {
            "part_id": part_id,
            "part_number": part.part_number,
            "physical_stock": physical_stock,
            "reserved": reserved,
            "available": available,
            "pending_orders": pending_orders,
            "minimum_stock": part.minimum_stock or 0,
            "needs_reorder": available < (part.minimum_stock or 0)
        }
    
    async def get_all_parts_availability(self) -> List[Dict]:
        """Get availability info for all parts."""
        parts_result = await self.db.execute(
            select(Part.id).where(Part.is_active == True)
        )
        part_ids = [row[0] for row in parts_result.all()]
        
        availability_list = []
        for part_id in part_ids:
            availability = await self.get_part_availability(part_id)
            if availability:
                availability_list.append(availability)
        
        return availability_list
    
    async def check_product_availability(self, product_id: int, quantity: int = 1) -> Dict:
        """
        Check if a product can be fulfilled based on part availability.
        
        Returns:
            {
                "available": bool,
                "max_quantity": int,
                "blocking_parts": [{"part_id": int, "part_number": str, "needed": int, "available": int}]
            }
        """
        from app.db.models import Product, ProductPart, ProductOption
        
        # Get product with all parts (base + options)
        product_result = await self.db.execute(
            select(Product).where(Product.id == product_id)
        )
        product = product_result.scalar_one_or_none()
        
        if not product:
            return {"available": False, "max_quantity": 0, "blocking_parts": []}
        
        # Collect all required parts
        required_parts: Dict[int, int] = {}  # part_id -> quantity
        
        # Add base product parts
        base_parts_result = await self.db.execute(
            select(ProductPart).where(
                ProductPart.product_id == product_id,
                ProductPart.is_optional == False
            )
        )
        base_parts = base_parts_result.scalars().all()
        
        for pp in base_parts:
            required_parts[pp.part_id] = required_parts.get(pp.part_id, 0) + pp.quantity
        
        # Check availability for each required part
        blocking_parts = []
        max_quantity = float('inf')
        
        for part_id, qty_needed in required_parts.items():
            availability = await self.get_part_availability(part_id)
            if availability:
                # How many units of the product can we make with this part?
                part_max = availability["available"] // qty_needed if qty_needed > 0 else float('inf')
                max_quantity = min(max_quantity, part_max)
                
                if availability["available"] < (qty_needed * quantity):
                    blocking_parts.append({
                        "part_id": part_id,
                        "part_number": availability["part_number"],
                        "needed": qty_needed * quantity,
                        "available": availability["available"]
                    })
        
        if max_quantity == float('inf'):
            max_quantity = 999  # Some reasonable maximum
        
        return {
            "available": len(blocking_parts) == 0,
            "max_quantity": int(max_quantity),
            "blocking_parts": blocking_parts
        }
    
    async def reserve_parts_for_order_item(
        self, 
        order_item_id: int, 
        product_id: int,
        selected_options: List[int] = None
    ) -> bool:
        """
        Create OrderItemPart records to reserve parts for an order item.
        Called when an order is placed.
        
        Handles OR Groups:
        - For parts in an OR group, selects the first available alternative
        - Stores all alternative part IDs so any can be scanned during fulfillment
        
        Args:
            order_item_id: The order item to reserve parts for
            product_id: The product being ordered
            selected_options: List of selected option IDs (if any)
        
        Returns:
            bool: True if reservation successful
        """
        from app.db.models import Product, ProductPart, ProductOption, OptionPart
        import json
        
        # Group parts by alternative_group
        # Key: (source, alternative_group or None), Value: List[{part_id, quantity}]
        part_groups: dict = {}
        
        # Base product parts
        base_parts_result = await self.db.execute(
            select(ProductPart).where(
                ProductPart.product_id == product_id,
                ProductPart.is_optional == False
            )
        )
        base_parts = base_parts_result.scalars().all()
        
        for pp in base_parts:
            group_key = ('base', pp.alternative_group)
            if group_key not in part_groups:
                part_groups[group_key] = []
            part_groups[group_key].append({
                'part_id': pp.part_id,
                'quantity': pp.quantity
            })
        
        # Option parts (if any)
        if selected_options:
            option_parts_result = await self.db.execute(
                select(OptionPart)
                .join(ProductOption)
                .where(ProductOption.id.in_(selected_options))
            )
            option_parts = option_parts_result.scalars().all()
            
            for op in option_parts:
                group_key = ('option', op.alternative_group)
                if group_key not in part_groups:
                    part_groups[group_key] = []
                part_groups[group_key].append({
                    'part_id': op.part_id,
                    'quantity': op.quantity
                })
        
        # For each group, select the best available part
        for (source, alt_group), parts in part_groups.items():
            if alt_group is None:
                # No alternatives - reserve each part individually
                for part_info in parts:
                    order_item_part = OrderItemPart(
                        order_item_id=order_item_id,
                        part_id=part_info['part_id'],
                        quantity=part_info['quantity'],
                        alternative_group=None,
                        alternative_part_ids=None,
                        is_scanned=False
                    )
                    self.db.add(order_item_part)
            else:
                # OR Group - pick first available, but allow any alternative during scanning
                # Check availability for each alternative
                best_part = None
                best_availability = -1
                
                for part_info in parts:
                    availability = await self.get_part_availability(part_info['part_id'])
                    if availability and availability['available'] >= part_info['quantity']:
                        if availability['available'] > best_availability:
                            best_availability = availability['available']
                            best_part = part_info
                
                # If no part has enough stock, pick the one with most available
                if not best_part:
                    for part_info in parts:
                        availability = await self.get_part_availability(part_info['part_id'])
                        if availability and availability['available'] > best_availability:
                            best_availability = availability['available']
                            best_part = part_info
                
                # Still nothing? Pick first one
                if not best_part:
                    best_part = parts[0]
                
                # Store all alternative part IDs for fulfillment flexibility
                alternative_ids = [p['part_id'] for p in parts]
                
                order_item_part = OrderItemPart(
                    order_item_id=order_item_id,
                    part_id=best_part['part_id'],  # Reserved part (best available)
                    quantity=best_part['quantity'],
                    alternative_group=alt_group,
                    alternative_part_ids=json.dumps(alternative_ids),  # All acceptable alternatives
                    is_scanned=False
                )
                self.db.add(order_item_part)
        
        await self.db.commit()
        return True
    
    async def scan_part(
        self, 
        order_item_part_id: int,
        scanned_by: str,
        scanned_part_id: int = None
    ) -> bool:
        """
        Mark a part as scanned/filled and remove from physical stock.
        
        For OR Groups:
        - If scanned_part_id provided, validates it's an acceptable alternative
        - Updates actual_part_id to track which alternative was actually used
        - Removes scanned part from stock (not necessarily the reserved part)
        
        Args:
            order_item_part_id: The OrderItemPart record to mark as scanned
            scanned_by: Username or identifier of who scanned it
            scanned_part_id: The actual part ID scanned (if different from reserved)
        
        Returns:
            bool: True if successful
        """
        from datetime import datetime
        import json
        
        # Get the OrderItemPart
        result = await self.db.execute(
            select(OrderItemPart).where(OrderItemPart.id == order_item_part_id)
        )
        oip = result.scalar_one_or_none()
        
        if not oip or oip.is_scanned:
            return False
        
        # Determine which part to remove from stock
        actual_part_id = scanned_part_id or oip.part_id
        
        # If an alternative was scanned, validate it's acceptable
        if scanned_part_id and scanned_part_id != oip.part_id:
            if oip.alternative_part_ids:
                acceptable_ids = json.loads(oip.alternative_part_ids)
                if scanned_part_id not in acceptable_ids:
                    # Scanned part is not a valid alternative
                    return False
            else:
                # No alternatives allowed
                return False
        
        # Mark as scanned
        oip.is_scanned = True
        oip.scanned_at = datetime.utcnow()
        oip.scanned_by = scanned_by
        oip.actual_part_id = actual_part_id if actual_part_id != oip.part_id else None
        
        # Remove from physical stock (the actual part used, not necessarily the reserved one)
        part_result = await self.db.execute(
            select(Part).where(Part.id == actual_part_id)
        )
        part = part_result.scalar_one_or_none()
        
        if part:
            part.stock_quantity = max(0, (part.stock_quantity or 0) - oip.quantity)
        
        await self.db.commit()
        return True

"""
Order Fulfillment API Endpoints

Handles order fulfillment, part scanning, and packing slips.
"""

from typing import List
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.orm import selectinload

from app.db.session import get_db
from app.db.models import Order, OrderItem, OrderItemPart, User
from app.services.inventory_service import InventoryService
from app.api.deps import get_current_active_user, require_role

router = APIRouter(prefix="/fulfillment", tags=["fulfillment"])


@router.get("/orders/pending")
async def get_pending_orders(
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(require_role("admin"))
):
    """Get all orders that need fulfillment (not yet fully filled)."""
    
    query = (
        select(Order)
        .options(
            selectinload(Order.items).selectinload(OrderItem.parts)
        )
        .where(Order.status_id.in_([1, 2, 3]))  # Pending, Processing, Processed
        .order_by(Order.created_at.asc())
    )
    
    result = await db.execute(query)
    orders = result.scalars().all()
    
    # Filter to orders with unfilled items
    pending_orders = []
    for order in orders:
        has_unfilled = any(not item.is_filled for item in order.items)
        if has_unfilled:
            pending_orders.append({
                "id": order.id,
                "order_number": order.order_number,
                "customer_name": f"{order.first_name} {order.last_name}",
                "email": order.email,
                "status": order.status_name,
                "total": float(order.total),
                "created_at": order.created_at.isoformat(),
                "items_total": len(order.items),
                "items_filled": sum(1 for item in order.items if item.is_filled),
                "items_pending": sum(1 for item in order.items if not item.is_filled)
            })
    
    return {"data": pending_orders}


@router.get("/orders/{order_id}/details")
async def get_order_fulfillment_details(
    order_id: int,
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(require_role("admin"))
):
    """Get detailed fulfillment information for an order."""
    
    query = (
        select(Order)
        .options(
            selectinload(Order.items).selectinload(OrderItem.parts),
            selectinload(Order.shipping_address)
        )
        .where(Order.id == order_id)
    )
    
    result = await db.execute(query)
    order = result.scalar_one_or_none()
    
    if not order:
        raise HTTPException(status_code=404, detail="Order not found")
    
    items_detail = []
    for item in order.items:
        parts_detail = []
        for part in item.parts:
            import json
            
            # Get alternative parts if this is an OR group
            alternatives = []
            if part.alternative_part_ids:
                alt_ids = json.loads(part.alternative_part_ids)
                # Fetch alternative part details
                from app.db.models import Part
                alt_parts_result = await db.execute(
                    select(Part).where(Part.id.in_(alt_ids))
                )
                alt_parts = alt_parts_result.scalars().all()
                alternatives = [
                    {
                        "part_id": ap.id,
                        "part_number": ap.part_number,
                        "name": ap.name,
                        "stock_available": ap.stock_quantity
                    }
                    for ap in alt_parts
                ]
            
            parts_detail.append({
                "id": part.id,
                "part_id": part.part_id,
                "part_number": part.part.part_number if part.part else None,
                "part_name": part.part.name if part.part else None,
                "quantity": part.quantity,
                "is_scanned": part.is_scanned,
                "scanned_at": part.scanned_at.isoformat() if part.scanned_at else None,
                "scanned_by": part.scanned_by,
                "alternative_group": part.alternative_group,
                "alternatives": alternatives,  # List of acceptable alternative parts
                "actual_part_used": {
                    "part_id": part.actual_part_id,
                    "part_number": part.actual_part.part_number if part.actual_part else None,
                    "name": part.actual_part.name if part.actual_part else None
                } if part.actual_part_id else None
            })
        
        items_detail.append({
            "id": item.id,
            "sku": item.sku,
            "title": item.title,
            "quantity": item.quantity,
            "price": float(item.price),
            "is_filled": item.is_filled,
            "filled_at": item.filled_at.isoformat() if item.filled_at else None,
            "filled_by": item.filled_by,
            "parts": parts_detail,
            "parts_total": len(parts_detail),
            "parts_scanned": sum(1 for p in parts_detail if p["is_scanned"])
        })
    
    return {
        "data": {
            "id": order.id,
            "order_number": order.order_number,
            "customer_name": f"{order.first_name} {order.last_name}",
            "email": order.email,
            "status": order.status_name,
            "shipping_address": order.shipping_address.full_address if order.shipping_address else None,
            "items": items_detail,
            "created_at": order.created_at.isoformat()
        }
    }


@router.post("/scan-part/{order_item_part_id}")
async def scan_part(
    order_item_part_id: int,
    scanned_part_id: int = None,  # Optional: if scanning an alternative part
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(require_role("admin"))
):
    """
    Scan/mark a part as filled for an order item.
    
    For OR Groups:
    - Can scan any alternative part from the group
    - Provide scanned_part_id to specify which alternative was actually used
    - System validates the alternative is acceptable
    """
    
    inventory_service = InventoryService(db)
    success = await inventory_service.scan_part(
        order_item_part_id=order_item_part_id,
        scanned_by=current_user.username,
        scanned_part_id=scanned_part_id
    )
    
    if not success:
        raise HTTPException(
            status_code=400,
            detail="Part already scanned, not found, or invalid alternative"
        )
    
    # Check if all parts for the order item are now scanned
    result = await db.execute(
        select(OrderItemPart).where(OrderItemPart.id == order_item_part_id)
    )
    scanned_part = result.scalar_one()
    
    # Get all parts for this order item
    all_parts_result = await db.execute(
        select(OrderItemPart).where(
            OrderItemPart.order_item_id == scanned_part.order_item_id
        )
    )
    all_parts = all_parts_result.scalars().all()
    
    # If all parts scanned, mark order item as filled
    if all(p.is_scanned for p in all_parts):
        from datetime import datetime
        item_result = await db.execute(
            select(OrderItem).where(OrderItem.id == scanned_part.order_item_id)
        )
        order_item = item_result.scalar_one()
        order_item.is_filled = True
        order_item.filled_at = datetime.utcnow()
        order_item.filled_by = current_user.username
        await db.commit()
    
    return {"success": True, "message": "Part scanned successfully"}


@router.post("/unscan-part/{order_item_part_id}")
async def unscan_part(
    order_item_part_id: int,
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(require_role("admin"))
):
    """Undo a part scan (returns part to stock)."""
    
    from datetime import datetime
    
    result = await db.execute(
        select(OrderItemPart).where(OrderItemPart.id == order_item_part_id)
    )
    oip = result.scalar_one_or_none()
    
    if not oip or not oip.is_scanned:
        raise HTTPException(status_code=400, detail="Part not scanned or not found")
    
    # Return to stock
    from app.db.models import Part
    part_result = await db.execute(
        select(Part).where(Part.id == oip.part_id)
    )
    part = part_result.scalar_one()
    part.stock_quantity += oip.quantity
    
    # Mark as unscanned
    oip.is_scanned = False
    oip.scanned_at = None
    oip.scanned_by = None
    
    # Mark order item as unfilled
    item_result = await db.execute(
        select(OrderItem).where(OrderItem.id == oip.order_item_id)
    )
    order_item = item_result.scalar_one()
    order_item.is_filled = False
    order_item.filled_at = None
    order_item.filled_by = None
    
    await db.commit()
    
    return {"success": True, "message": "Part unscanned successfully"}


@router.get("/orders/{order_id}/packing-slip")
async def get_packing_slip(
    order_id: int,
    db: AsyncSession = Depends(get_db),
    current_user: User = Depends(require_role("admin"))
):
    """
    Get packing slip data - only includes items with all parts scanned.
    Used to generate the final packing slip PDF.
    """
    
    query = (
        select(Order)
        .options(
            selectinload(Order.items).selectinload(OrderItem.parts),
            selectinload(Order.shipping_address)
        )
        .where(Order.id == order_id)
    )
    
    result = await db.execute(query)
    order = result.scalar_one_or_none()
    
    if not order:
        raise HTTPException(status_code=404, detail="Order not found")
    
    # Only include filled items
    filled_items = []
    for item in order.items:
        if item.is_filled:
            filled_items.append({
                "sku": item.sku,
                "title": item.title,
                "quantity": item.quantity,
                "price": float(item.price),
                "subtotal": float(item.subtotal)
            })
    
    return {
        "data": {
            "order_number": order.order_number,
            "customer_name": f"{order.first_name} {order.last_name}",
            "email": order.email,
            "shipping_address": order.shipping_address.full_address if order.shipping_address else None,
            "items": filled_items,
            "subtotal": float(order.subtotal),
            "tax": float(order.tax_amount),
            "shipping": float(order.shipping_amount),
            "total": float(order.total),
            "created_at": order.created_at.isoformat()
        }
    }

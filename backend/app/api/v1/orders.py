"""
Order Endpoints

Handles order creation, checkout, and order management.
"""

from typing import List, Optional
from datetime import datetime
from decimal import Decimal
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, func, delete
from sqlalchemy.orm import selectinload

from app.db.session import get_db
from app.db.models import (
    Order,
    OrderItem,
    OrderHistory,
    CartItem,
    Product,
    Address,
    User,
)
from app.schemas.order import (
    CheckoutRequest,
    CheckoutResponse,
    OrderCreate,
    OrderResponse,
    OrderDetailResponse,
    OrderUpdateStatus,
    CartItemResponse,
)
from app.schemas import APIResponse, ResponseMetadata, MessageResponse
from app.api.deps import (
    get_current_active_user,
    get_optional_current_user,
    get_current_admin_user,
    get_optional_session_id,
)
from app.core.logging import get_logger

logger = get_logger(__name__)
router = APIRouter()


async def calculate_order_totals(
    items: List[CartItem],
    shipping_state: str,
) -> dict:
    """
    Calculate order totals including tax and shipping.
    
    Simple calculation:
    - Subtotal: sum of (price * quantity)
    - Tax: 8% for Texas, 0% for other states
    - Shipping: $5 flat rate
    """
    subtotal = sum(item.price * item.quantity for item in items)
    
    # Simple tax calculation (8% for TX, 0% otherwise)
    tax_rate = Decimal("0.08") if shipping_state.upper() == "TX" else Decimal("0.00")
    tax_amount = subtotal * tax_rate
    
    # Flat rate shipping
    shipping_amount = Decimal("5.00")
    
    # No discounts for now
    discount_amount = Decimal("0.00")
    
    total = subtotal + tax_amount + shipping_amount - discount_amount
    
    return {
        "subtotal": subtotal,
        "tax_amount": tax_amount,
        "shipping_amount": shipping_amount,
        "discount_amount": discount_amount,
        "total": total,
    }


@router.post("/checkout", response_model=APIResponse[CheckoutResponse])
async def calculate_checkout(
    checkout_data: CheckoutRequest,
    db: AsyncSession = Depends(get_db),
):
    """
    Calculate checkout totals without creating order.
    
    - Gets cart items by session_id or user_id
    - Calculates subtotal, tax, shipping, total
    - Validates stock availability
    """
    # Get cart items
    if checkout_data.user_id:
        query = select(CartItem).where(CartItem.user_id == checkout_data.user_id)
    else:
        query = select(CartItem).where(CartItem.session_id == checkout_data.session_id)
    
    result = await db.execute(query)
    items = result.scalars().all()
    
    if not items:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Cart is empty",
        )
    
    # Validate stock for all items
    for item in items:
        result = await db.execute(
            select(Product).where(Product.id == item.product_id)
        )
        product = result.scalar_one_or_none()
        
        if not product or product.stock_quantity < item.quantity:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Insufficient stock for {item.sku}",
            )
    
    # Calculate totals
    totals = await calculate_order_totals(items, checkout_data.shipping_address.state)
    
    return APIResponse(
        data=CheckoutResponse(
            **totals,
            items=[CartItemResponse.model_validate(item) for item in items],
        )
    )


@router.post("/", response_model=APIResponse[OrderResponse], status_code=status.HTTP_201_CREATED)
async def create_order(
    order_data: OrderCreate,
    current_user: Optional[User] = Depends(get_optional_current_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Create new order from cart.
    
    - Validates cart has items
    - Validates stock availability
    - Creates order with items
    - Creates order history entry
    - Clears cart
    - Reduces stock (if using parts system)
    """
    # Get cart items
    if current_user:
        query = select(CartItem).where(CartItem.user_id == current_user.id)
    else:
        query = select(CartItem).where(CartItem.session_id == order_data.session_id)
    
    result = await db.execute(query)
    cart_items = result.scalars().all()
    
    if not cart_items:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Cart is empty",
        )
    
    # Validate stock and get products
    products = {}
    for item in cart_items:
        result = await db.execute(
            select(Product).where(Product.id == item.product_id)
        )
        product = result.scalar_one_or_none()
        
        if not product:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Product {item.sku} not found",
            )
        
        if product.stock_quantity < item.quantity:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Insufficient stock for {item.sku}. Available: {product.stock_quantity}",
            )
        
        products[item.product_id] = product
    
    # Calculate totals
    totals = await calculate_order_totals(
        cart_items,
        order_data.shipping_address.state
    )
    
    # Generate order number (simple timestamp-based)
    order_number = f"ORD-{datetime.utcnow().strftime('%Y%m%d%H%M%S')}"
    
    # Create shipping address
    shipping_address = Address(
        user_id=current_user.id if current_user else None,
        first_name=order_data.shipping_address.first_name,
        last_name=order_data.shipping_address.last_name,
        company_name=order_data.shipping_address.company_name,
        address_line1=order_data.shipping_address.address_line1,
        address_line2=order_data.shipping_address.address_line2,
        city=order_data.shipping_address.city,
        state=order_data.shipping_address.state,
        zip_code=order_data.shipping_address.zip_code,
        country=order_data.shipping_address.country,
        phone=order_data.shipping_address.phone,
        address_type="shipping",
    )
    db.add(shipping_address)
    await db.flush()  # Get address ID
    
    # Create billing address if different
    billing_address = None
    if order_data.billing_address:
        billing_address = Address(
            user_id=current_user.id if current_user else None,
            first_name=order_data.billing_address.first_name,
            last_name=order_data.billing_address.last_name,
            company_name=order_data.billing_address.company_name,
            address_line1=order_data.billing_address.address_line1,
            address_line2=order_data.billing_address.address_line2,
            city=order_data.billing_address.city,
            state=order_data.billing_address.state,
            zip_code=order_data.billing_address.zip_code,
            country=order_data.billing_address.country,
            phone=order_data.billing_address.phone,
            address_type="billing",
        )
        db.add(billing_address)
        await db.flush()
    
    # Create order
    order = Order(
        order_number=order_number,
        user_id=current_user.id if current_user else None,
        email=order_data.email,
        first_name=order_data.first_name,
        last_name=order_data.last_name,
        company_name=order_data.company_name,
        subtotal=totals["subtotal"],
        tax_amount=totals["tax_amount"],
        shipping_amount=totals["shipping_amount"],
        discount_amount=totals["discount_amount"],
        total=totals["total"],
        status_id=1,  # Pending
        payment_method="PayPal",
        payment_transaction_id=order_data.payment_transaction_id,
        shipping_address_id=shipping_address.id,
        billing_address_id=billing_address.id if billing_address else shipping_address.id,
        notes=order_data.notes,
        client_ip=order_data.client_ip,
        client_browser=order_data.client_browser,
        referral=order_data.referral,
    )
    db.add(order)
    await db.flush()  # Get order ID
    
    # Create order items
    for cart_item in cart_items:
        product = products[cart_item.product_id]
        
        order_item = OrderItem(
            order_id=order.id,
            product_id=product.id,
            sku=cart_item.sku,
            title=product.title,
            customization=cart_item.customization,
            quantity=cart_item.quantity,
            price=cart_item.price,
        )
        db.add(order_item)
    
    # Create initial order history entry
    order_history = OrderHistory(
        order_id=order.id,
        status_id=1,
        notes="Order created",
        updated_by="system",
    )
    db.add(order_history)
    
    # Clear cart
    if current_user:
        await db.execute(
            delete(CartItem).where(CartItem.user_id == current_user.id)
        )
    else:
        await db.execute(
            delete(CartItem).where(CartItem.session_id == order_data.session_id)
        )
    
    await db.commit()
    await db.refresh(order)
    
    # Load relationships for response
    result = await db.execute(
        select(Order)
        .where(Order.id == order.id)
        .options(
            selectinload(Order.items),
            selectinload(Order.shipping_address),
        )
    )
    order = result.scalar_one()
    
    logger.info(f"Order created: {order.order_number}")
    
    return APIResponse(data=OrderResponse.model_validate(order))


@router.get("/", response_model=APIResponse[List[OrderResponse]])
async def list_user_orders(
    page: int = Query(1, ge=1),
    per_page: int = Query(20, ge=1, le=100),
    current_user: User = Depends(get_current_active_user),
    db: AsyncSession = Depends(get_db),
):
    """
    List current user's orders.
    
    - Requires authentication
    - Returns paginated list of orders
    - Sorted by created_at descending
    """
    query = select(Order).where(Order.user_id == current_user.id)
    
    # Get total count
    count_query = select(func.count()).select_from(query.subquery())
    total_result = await db.execute(count_query)
    total = total_result.scalar()
    
    # Apply pagination and sorting
    query = (
        query.order_by(Order.created_at.desc())
        .offset((page - 1) * per_page)
        .limit(per_page)
    )
    
    # Load relationships
    query = query.options(
        selectinload(Order.items),
        selectinload(Order.shipping_address),
    )
    
    result = await db.execute(query)
    orders = result.scalars().all()
    
    return APIResponse(
        data=[OrderResponse.model_validate(order) for order in orders],
        meta=ResponseMetadata(
            page=page,
            per_page=per_page,
            total=total,
            total_pages=(total + per_page - 1) // per_page,
        ),
    )


@router.get("/{order_id}", response_model=APIResponse[OrderDetailResponse])
async def get_order(
    order_id: int,
    current_user: User = Depends(get_current_active_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Get order details.
    
    - Requires authentication
    - User can only access their own orders
    - Returns order with items, addresses, and history
    """
    query = (
        select(Order)
        .where(Order.id == order_id, Order.user_id == current_user.id)
        .options(
            selectinload(Order.items),
            selectinload(Order.shipping_address),
            selectinload(Order.history),
        )
    )
    
    result = await db.execute(query)
    order = result.scalar_one_or_none()
    
    if not order:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Order not found",
        )
    
    return APIResponse(data=OrderDetailResponse.model_validate(order))


# Admin endpoints
@router.get("/admin/all", response_model=APIResponse[List[OrderResponse]])
async def list_all_orders(
    page: int = Query(1, ge=1),
    per_page: int = Query(50, ge=1, le=200),
    status_id: Optional[int] = None,
    search: Optional[str] = None,
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    List all orders (admin only).
    
    - Requires admin role
    - Supports filtering by status and search
    - Returns paginated list
    """
    query = select(Order)
    
    # Apply filters
    if status_id:
        query = query.where(Order.status_id == status_id)
    
    if search:
        query = query.where(
            (Order.order_number.ilike(f"%{search}%"))
            | (Order.email.ilike(f"%{search}%"))
            | (Order.first_name.ilike(f"%{search}%"))
            | (Order.last_name.ilike(f"%{search}%"))
        )
    
    # Get total count
    count_query = select(func.count()).select_from(query.subquery())
    total_result = await db.execute(count_query)
    total = total_result.scalar()
    
    # Apply pagination and sorting
    query = (
        query.order_by(Order.created_at.desc())
        .offset((page - 1) * per_page)
        .limit(per_page)
    )
    
    # Load relationships
    query = query.options(
        selectinload(Order.items),
        selectinload(Order.shipping_address),
    )
    
    result = await db.execute(query)
    orders = result.scalars().all()
    
    return APIResponse(
        data=[OrderResponse.model_validate(order) for order in orders],
        meta=ResponseMetadata(
            page=page,
            per_page=per_page,
            total=total,
            total_pages=(total + per_page - 1) // per_page,
        ),
    )


@router.put("/{order_id}/status", response_model=APIResponse[OrderDetailResponse])
async def update_order_status(
    order_id: int,
    status_data: OrderUpdateStatus,
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Update order status (admin only).
    
    - Requires admin role
    - Creates order history entry
    - Updates tracking info if provided
    """
    query = (
        select(Order)
        .where(Order.id == order_id)
        .options(
            selectinload(Order.items),
            selectinload(Order.shipping_address),
            selectinload(Order.history),
        )
    )
    
    result = await db.execute(query)
    order = result.scalar_one_or_none()
    
    if not order:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Order not found",
        )
    
    # Update status
    old_status = order.status_id
    order.status_id = status_data.status_id
    
    # Update tracking info if provided
    if status_data.tracking_number:
        order.tracking_number = status_data.tracking_number
    if status_data.carrier:
        order.carrier = status_data.carrier
    
    # Create history entry
    order_history = OrderHistory(
        order_id=order.id,
        status_id=status_data.status_id,
        notes=status_data.notes or f"Status changed from {old_status} to {status_data.status_id}",
        updated_by=current_user.email,
    )
    db.add(order_history)
    
    await db.commit()
    await db.refresh(order)
    
    logger.info(
        f"Order {order.order_number} status updated to {status_data.status_id} by {current_user.email}"
    )
    
    return APIResponse(data=OrderDetailResponse.model_validate(order))

"""
Cart Endpoints

Handles shopping cart operations for both guest and authenticated users.
"""

from typing import List
from fastapi import APIRouter, Depends, HTTPException, status, Header
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, delete

from app.db.session import get_db
from app.db.models import CartItem, Product, User
from app.schemas.order import CartItemCreate, CartItemUpdate, CartItemResponse
from app.schemas import APIResponse, MessageResponse
from app.api.deps import get_optional_current_user, get_optional_session_id
from app.core.logging import get_logger

logger = get_logger(__name__)
router = APIRouter()


async def get_cart_items(
    db: AsyncSession,
    user: User = None,
    session_id: str = None,
) -> List[CartItem]:
    """Get cart items for user or guest session."""
    if user:
        query = select(CartItem).where(CartItem.user_id == user.id)
    elif session_id:
        query = select(CartItem).where(CartItem.session_id == session_id)
    else:
        return []
    
    result = await db.execute(query)
    return result.scalars().all()


@router.get("/", response_model=APIResponse[List[CartItemResponse]])
async def get_cart(
    current_user = Depends(get_optional_current_user),
    session_id: str = Depends(get_optional_session_id),
    db: AsyncSession = Depends(get_db),
):
    """
    Get cart items.
    
    - Works for both authenticated and guest users
    - Authenticated: uses user_id
    - Guest: uses session_id from X-Session-ID header
    """
    if not current_user and not session_id:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Session ID or authentication required",
        )
    
    items = await get_cart_items(db, user=current_user, session_id=session_id)
    
    return APIResponse(
        data=[CartItemResponse.model_validate(item) for item in items]
    )


@router.post("/items", response_model=APIResponse[CartItemResponse], status_code=status.HTTP_201_CREATED)
async def add_to_cart(
    item_data: CartItemCreate,
    current_user = Depends(get_optional_current_user),
    session_id: str = Depends(get_optional_session_id),
    db: AsyncSession = Depends(get_db),
):
    """
    Add item to cart.
    
    - Checks product exists and has stock
    - If item already exists, updates quantity
    """
    if not current_user and not session_id:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Session ID or authentication required",
        )
    
    # Get product
    result = await db.execute(
        select(Product).where(Product.sku == item_data.sku, Product.is_active == True)
    )
    product = result.scalar_one_or_none()
    
    if not product:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Product not found",
        )
    
    # Check stock
    if product.stock_quantity < item_data.quantity:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=f"Insufficient stock. Available: {product.stock_quantity}",
        )
    
    # Check if item already in cart
    query = select(CartItem).where(
        CartItem.product_id == product.id,
        CartItem.customization == item_data.customization,
    )
    if current_user:
        query = query.where(CartItem.user_id == current_user.id)
    else:
        query = query.where(CartItem.session_id == session_id)
    
    result = await db.execute(query)
    existing_item = result.scalar_one_or_none()
    
    if existing_item:
        # Update quantity
        new_quantity = existing_item.quantity + item_data.quantity
        if product.stock_quantity < new_quantity:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Insufficient stock. Available: {product.stock_quantity}",
            )
        existing_item.quantity = new_quantity
        await db.commit()
        await db.refresh(existing_item)
        
        logger.info(f"Cart item updated: {product.sku} qty={new_quantity}")
        return APIResponse(data=CartItemResponse.model_validate(existing_item))
    
    # Create new cart item
    cart_item = CartItem(
        user_id=current_user.id if current_user else None,
        session_id=session_id if not current_user else None,
        product_id=product.id,
        sku=product.sku,
        quantity=item_data.quantity,
        price=product.base_price,
        customization=item_data.customization,
    )
    
    db.add(cart_item)
    await db.commit()
    await db.refresh(cart_item)
    
    logger.info(f"Item added to cart: {product.sku}")
    
    return APIResponse(data=CartItemResponse.model_validate(cart_item))


@router.put("/items/{item_id}", response_model=APIResponse[CartItemResponse])
async def update_cart_item(
    item_id: int,
    item_data: CartItemUpdate,
    current_user = Depends(get_optional_current_user),
    session_id: str = Depends(get_optional_session_id),
    db: AsyncSession = Depends(get_db),
):
    """Update cart item quantity."""
    if not current_user and not session_id:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Session ID or authentication required",
        )
    
    # Get cart item
    query = select(CartItem).where(CartItem.id == item_id)
    if current_user:
        query = query.where(CartItem.user_id == current_user.id)
    else:
        query = query.where(CartItem.session_id == session_id)
    
    result = await db.execute(query)
    cart_item = result.scalar_one_or_none()
    
    if not cart_item:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Cart item not found",
        )
    
    # Get product and check stock
    result = await db.execute(
        select(Product).where(Product.id == cart_item.product_id)
    )
    product = result.scalar_one_or_none()
    
    if not product:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Product not found",
        )
    
    if product.stock_quantity < item_data.quantity:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=f"Insufficient stock. Available: {product.stock_quantity}",
        )
    
    cart_item.quantity = item_data.quantity
    await db.commit()
    await db.refresh(cart_item)
    
    logger.info(f"Cart item updated: {product.sku} qty={item_data.quantity}")
    
    return APIResponse(data=CartItemResponse.model_validate(cart_item))


@router.delete("/items/{item_id}", status_code=status.HTTP_204_NO_CONTENT)
async def remove_from_cart(
    item_id: int,
    current_user = Depends(get_optional_current_user),
    session_id: str = Depends(get_optional_session_id),
    db: AsyncSession = Depends(get_db),
):
    """Remove item from cart."""
    if not current_user and not session_id:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Session ID or authentication required",
        )
    
    # Get cart item
    query = select(CartItem).where(CartItem.id == item_id)
    if current_user:
        query = query.where(CartItem.user_id == current_user.id)
    else:
        query = query.where(CartItem.session_id == session_id)
    
    result = await db.execute(query)
    cart_item = result.scalar_one_or_none()
    
    if not cart_item:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Cart item not found",
        )
    
    await db.delete(cart_item)
    await db.commit()
    
    logger.info(f"Item removed from cart: {cart_item.sku}")


@router.delete("/", status_code=status.HTTP_204_NO_CONTENT)
async def clear_cart(
    current_user = Depends(get_optional_current_user),
    session_id: str = Depends(get_optional_session_id),
    db: AsyncSession = Depends(get_db),
):
    """Clear all items from cart."""
    if not current_user and not session_id:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Session ID or authentication required",
        )
    
    if current_user:
        await db.execute(
            delete(CartItem).where(CartItem.user_id == current_user.id)
        )
    else:
        await db.execute(
            delete(CartItem).where(CartItem.session_id == session_id)
        )
    
    await db.commit()
    
    logger.info("Cart cleared")


@router.post("/merge")
async def merge_guest_cart(
    session_id: str,
    current_user: User = Depends(get_optional_current_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Merge guest cart into user cart after login.
    
    - Moves items from session_id to user_id
    - Handles duplicate items by updating quantities
    """
    if not current_user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Authentication required",
        )
    
    # Get guest cart items
    guest_items = await get_cart_items(db, session_id=session_id)
    
    if not guest_items:
        return APIResponse(data=MessageResponse(message="No items to merge"))
    
    # Get user cart items
    user_items = await get_cart_items(db, user=current_user)
    user_items_map = {
        (item.product_id, item.customization): item
        for item in user_items
    }
    
    merged_count = 0
    for guest_item in guest_items:
        key = (guest_item.product_id, guest_item.customization)
        
        if key in user_items_map:
            # Update existing user item quantity
            user_items_map[key].quantity += guest_item.quantity
            await db.delete(guest_item)
        else:
            # Transfer to user
            guest_item.user_id = current_user.id
            guest_item.session_id = None
        
        merged_count += 1
    
    await db.commit()
    
    logger.info(f"Merged {merged_count} items from guest cart to user cart")
    
    return APIResponse(
        data=MessageResponse(message=f"Merged {merged_count} items into cart")
    )

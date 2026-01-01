"""
Product Endpoints

Handles product listing, details, and admin CRUD operations.
"""

from typing import Optional, List
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, func, or_
from sqlalchemy.orm import selectinload

from app.db.session import get_db
from app.db.models import Product, Category, ProductImage, ProductPart, Part
from app.schemas.product import (
    ProductResponse,
    ProductDetailResponse,
    ProductListQuery,
    ProductCreate,
    ProductUpdate,
    PartResponse,
    PartCreate,
    PartUpdate,
)
from app.schemas import APIResponse, ResponseMetadata
from app.api.deps import get_current_admin_user, get_optional_current_user
from app.core.logging import get_logger

logger = get_logger(__name__)
router = APIRouter()


@router.get("/", response_model=APIResponse[List[ProductResponse]])
async def list_products(
    page: int = Query(1, ge=1),
    per_page: int = Query(20, ge=1, le=100),
    category: Optional[str] = None,
    search: Optional[str] = None,
    featured: Optional[bool] = None,
    sort_by: str = Query("sort_order", regex="^(sort_order|title|base_price|created_at)$"),
    sort_order: str = Query("asc", regex="^(asc|desc)$"),
    db: AsyncSession = Depends(get_db),
):
    """
    List products with pagination and filtering.
    
    - Public endpoint (no auth required)
    - Returns calculated stock quantity (not parts)
    - Supports search, category filter, featured filter
    """
    query = select(Product).where(Product.is_active == True)
    
    # Apply filters
    if category:
        query = query.join(Product.categories).where(Category.slug == category)
    
    if search:
        query = query.where(
            or_(
                Product.title.ilike(f"%{search}%"),
                Product.description.ilike(f"%{search}%"),
                Product.sku.ilike(f"%{search}%"),
            )
        )
    
    if featured is not None:
        query = query.where(Product.featured == featured)
    
    # Get total count
    count_query = select(func.count()).select_from(query.subquery())
    total_result = await db.execute(count_query)
    total = total_result.scalar()
    
    # Apply sorting
    sort_column = getattr(Product, sort_by)
    if sort_order == "desc":
        query = query.order_by(sort_column.desc())
    else:
        query = query.order_by(sort_column.asc())
    
    # Apply pagination
    query = query.offset((page - 1) * per_page).limit(per_page)
    
    # Load with relationships
    query = query.options(
        selectinload(Product.images),
        selectinload(Product.categories),
    )
    
    result = await db.execute(query)
    products = result.scalars().all()
    
    return APIResponse(
        data=[ProductResponse.model_validate(p) for p in products],
        meta=ResponseMetadata(
            page=page,
            per_page=per_page,
            total=total,
            total_pages=(total + per_page - 1) // per_page,
        ),
    )


@router.get("/{product_id}", response_model=APIResponse[ProductDetailResponse])
async def get_product(
    product_id: int,
    current_user = Depends(get_optional_current_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Get product details.
    
    - Public endpoint (no auth required)
    - Admin users see parts list
    - Regular users see only calculated stock
    """
    query = select(Product).where(Product.id == product_id, Product.is_active == True)
    query = query.options(
        selectinload(Product.images),
        selectinload(Product.categories),
        selectinload(Product.product_parts).selectinload(ProductPart.part),
    )
    
    result = await db.execute(query)
    product = result.scalar_one_or_none()
    
    if not product:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Product not found",
        )
    
    # Only show parts to admin users
    if current_user and current_user.is_admin:
        return APIResponse(data=ProductDetailResponse.model_validate(product))
    else:
        return APIResponse(data=ProductResponse.model_validate(product))


@router.post("/", response_model=APIResponse[ProductDetailResponse], status_code=status.HTTP_201_CREATED)
async def create_product(
    product_data: ProductCreate,
    current_user = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Create new product (admin only).
    
    - Requires admin role
    - Can assign categories and parts
    """
    # Check SKU uniqueness
    result = await db.execute(
        select(Product).where(Product.sku == product_data.sku)
    )
    if result.scalar_one_or_none():
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="SKU already exists",
        )
    
    # Create product
    product = Product(
        sku=product_data.sku,
        title=product_data.title,
        description=product_data.description,
        base_price=product_data.base_price,
        cost=product_data.cost,
        weight=product_data.weight,
        is_active=product_data.is_active,
        featured=product_data.featured,
        meta_title=product_data.meta_title,
        meta_description=product_data.meta_description,
        sort_order=product_data.sort_order,
    )
    
    # Assign categories
    if product_data.category_ids:
        result = await db.execute(
            select(Category).where(Category.id.in_(product_data.category_ids))
        )
        categories = result.scalars().all()
        product.categories.extend(categories)
    
    # Assign parts
    for part_data in product_data.parts:
        product_part = ProductPart(
            part_id=part_data.part_id,
            quantity=part_data.quantity,
            is_optional=part_data.is_optional,
            alternative_group=part_data.alternative_group,
            priority=part_data.priority,
            notes=part_data.notes,
        )
        product.product_parts.append(product_part)
    
    db.add(product)
    await db.commit()
    await db.refresh(product)
    
    logger.info(f"Product created: {product.sku} by {current_user.email}")
    
    return APIResponse(data=ProductDetailResponse.model_validate(product))


@router.put("/{product_id}", response_model=APIResponse[ProductDetailResponse])
async def update_product(
    product_id: int,
    product_data: ProductUpdate,
    current_user = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Update product (admin only).
    
    - Requires admin role
    - Can update all product fields, categories, and parts
    """
    result = await db.execute(
        select(Product).where(Product.id == product_id)
    )
    product = result.scalar_one_or_none()
    
    if not product:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Product not found",
        )
    
    # Update fields
    update_data = product_data.model_dump(exclude_unset=True)
    category_ids = update_data.pop("category_ids", None)
    parts = update_data.pop("parts", None)
    
    for field, value in update_data.items():
        setattr(product, field, value)
    
    # Update categories
    if category_ids is not None:
        result = await db.execute(
            select(Category).where(Category.id.in_(category_ids))
        )
        categories = result.scalars().all()
        product.categories = list(categories)
    
    # Update parts
    if parts is not None:
        # Remove existing parts
        for product_part in product.product_parts:
            await db.delete(product_part)
        
        # Add new parts
        for part_data in parts:
            product_part = ProductPart(
                part_id=part_data.part_id,
                quantity=part_data.quantity,
                is_optional=part_data.is_optional,
                alternative_group=part_data.alternative_group,
                priority=part_data.priority,
                notes=part_data.notes,
            )
            product.product_parts.append(product_part)
    
    await db.commit()
    await db.refresh(product)
    
    logger.info(f"Product updated: {product.sku} by {current_user.email}")
    
    return APIResponse(data=ProductDetailResponse.model_validate(product))


@router.delete("/{product_id}", status_code=status.HTTP_204_NO_CONTENT)
async def delete_product(
    product_id: int,
    current_user = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Delete product (admin only).
    
    - Requires admin role
    - Soft delete (sets is_active = False)
    """
    result = await db.execute(
        select(Product).where(Product.id == product_id)
    )
    product = result.scalar_one_or_none()
    
    if not product:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Product not found",
        )
    
    product.is_active = False
    await db.commit()
    
    logger.info(f"Product deleted: {product.sku} by {current_user.email}")


# Parts management (admin only)
@router.get("/admin/parts", response_model=APIResponse[List[PartResponse]])
async def list_parts(
    page: int = Query(1, ge=1),
    per_page: int = Query(50, ge=1, le=200),
    search: Optional[str] = None,
    low_stock: bool = False,
    current_user = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    List parts (admin only).
    
    - Supports search and low stock filter
    - Returns parts inventory details
    """
    query = select(Part).where(Part.is_active == True)
    
    if search:
        query = query.where(
            or_(
                Part.part_number.ilike(f"%{search}%"),
                Part.name.ilike(f"%{search}%"),
            )
        )
    
    if low_stock:
        query = query.where(Part.stock_quantity <= Part.low_stock_threshold)
    
    # Get total count
    count_query = select(func.count()).select_from(query.subquery())
    total_result = await db.execute(count_query)
    total = total_result.scalar()
    
    # Apply pagination
    query = query.offset((page - 1) * per_page).limit(per_page)
    
    result = await db.execute(query)
    parts = result.scalars().all()
    
    return APIResponse(
        data=[PartResponse.model_validate(p) for p in parts],
        meta=ResponseMetadata(
            page=page,
            per_page=per_page,
            total=total,
            total_pages=(total + per_page - 1) // per_page,
        ),
    )


@router.post("/admin/parts", response_model=APIResponse[PartResponse], status_code=status.HTTP_201_CREATED)
async def create_part(
    part_data: PartCreate,
    current_user = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """Create new part (admin only)."""
    # Check part number uniqueness
    result = await db.execute(
        select(Part).where(Part.part_number == part_data.part_number)
    )
    if result.scalar_one_or_none():
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Part number already exists",
        )
    
    part = Part(**part_data.model_dump())
    db.add(part)
    await db.commit()
    await db.refresh(part)
    
    logger.info(f"Part created: {part.part_number} by {current_user.email}")
    
    return APIResponse(data=PartResponse.model_validate(part))


@router.put("/admin/parts/{part_id}", response_model=APIResponse[PartResponse])
async def update_part(
    part_id: int,
    part_data: PartUpdate,
    current_user = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """Update part (admin only)."""
    result = await db.execute(
        select(Part).where(Part.id == part_id)
    )
    part = result.scalar_one_or_none()
    
    if not part:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Part not found",
        )
    
    update_data = part_data.model_dump(exclude_unset=True)
    for field, value in update_data.items():
        setattr(part, field, value)
    
    await db.commit()
    await db.refresh(part)
    
    logger.info(f"Part updated: {part.part_number} by {current_user.email}")
    
    return APIResponse(data=PartResponse.model_validate(part))

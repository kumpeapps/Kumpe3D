"""
Admin Endpoints

Administrative functions for user management, system configuration, and reporting.
"""

from typing import List, Optional
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, func, or_
from sqlalchemy.orm import selectinload

from app.db.session import get_db
from app.db.models import User, Role, Permission, SiteParameter
from app.schemas.user import (
    UserResponse,
    RoleResponse,
    PermissionResponse,
)
from app.schemas import APIResponse, ResponseMetadata, MessageResponse
from app.api.deps import get_current_admin_user, check_permission
from app.core.logging import get_logger
from pydantic import BaseModel

logger = get_logger(__name__)
router = APIRouter()


class UserRoleUpdate(BaseModel):
    """Schema for updating user roles."""
    
    role_ids: List[int]


class SiteParameterUpdate(BaseModel):
    """Schema for updating site parameters."""
    
    value: str


# ============================================================================
# User Management
# ============================================================================

@router.get("/users", response_model=APIResponse[List[UserResponse]])
async def list_users(
    page: int = Query(1, ge=1),
    per_page: int = Query(50, ge=1, le=200),
    search: Optional[str] = None,
    is_active: Optional[bool] = None,
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    List all users (admin only).
    
    - Requires admin role
    - Supports search and filtering
    - Returns paginated list with roles
    """
    query = select(User)
    
    # Apply filters
    if search:
        query = query.where(
            or_(
                User.email.ilike(f"%{search}%"),
                User.username.ilike(f"%{search}%"),
                User.first_name.ilike(f"%{search}%"),
                User.last_name.ilike(f"%{search}%"),
            )
        )
    
    if is_active is not None:
        query = query.where(User.is_active == is_active)
    
    # Get total count
    count_query = select(func.count()).select_from(query.subquery())
    total_result = await db.execute(count_query)
    total = total_result.scalar()
    
    # Apply pagination and sorting
    query = (
        query.order_by(User.created_at.desc())
        .offset((page - 1) * per_page)
        .limit(per_page)
    )
    
    # Load relationships
    query = query.options(selectinload(User.roles))
    
    result = await db.execute(query)
    users = result.scalars().all()
    
    return APIResponse(
        data=[UserResponse.model_validate(user) for user in users],
        meta=ResponseMetadata(
            page=page,
            per_page=per_page,
            total=total,
            total_pages=(total + per_page - 1) // per_page,
        ),
    )


@router.get("/users/{user_id}", response_model=APIResponse[UserResponse])
async def get_user(
    user_id: int,
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """Get user details (admin only)."""
    query = (
        select(User)
        .where(User.id == user_id)
        .options(selectinload(User.roles))
    )
    
    result = await db.execute(query)
    user = result.scalar_one_or_none()
    
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="User not found",
        )
    
    return APIResponse(data=UserResponse.model_validate(user))


@router.put("/users/{user_id}/roles", response_model=APIResponse[UserResponse])
async def update_user_roles(
    user_id: int,
    role_data: UserRoleUpdate,
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Update user roles (admin only).
    
    - Requires admin role
    - Replaces all user roles with provided list
    - Cannot remove admin role from yourself
    """
    # Get user
    query = (
        select(User)
        .where(User.id == user_id)
        .options(selectinload(User.roles))
    )
    
    result = await db.execute(query)
    user = result.scalar_one_or_none()
    
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="User not found",
        )
    
    # Prevent removing admin role from yourself
    if user_id == current_user.id:
        result = await db.execute(
            select(Role).where(Role.name == "admin")
        )
        admin_role = result.scalar_one_or_none()
        
        if admin_role and admin_role.id not in role_data.role_ids:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Cannot remove admin role from yourself",
            )
    
    # Get roles
    result = await db.execute(
        select(Role).where(Role.id.in_(role_data.role_ids))
    )
    roles = result.scalars().all()
    
    if len(roles) != len(role_data.role_ids):
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="One or more role IDs are invalid",
        )
    
    # Update user roles
    user.roles = list(roles)
    await db.commit()
    await db.refresh(user)
    
    logger.info(
        f"User {user.email} roles updated by {current_user.email}. New roles: {[r.name for r in roles]}"
    )
    
    return APIResponse(data=UserResponse.model_validate(user))


@router.put("/users/{user_id}/activate", response_model=APIResponse[MessageResponse])
async def activate_user(
    user_id: int,
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """Activate user account (admin only)."""
    result = await db.execute(
        select(User).where(User.id == user_id)
    )
    user = result.scalar_one_or_none()
    
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="User not found",
        )
    
    user.is_active = True
    await db.commit()
    
    logger.info(f"User {user.email} activated by {current_user.email}")
    
    return APIResponse(
        data=MessageResponse(message=f"User {user.email} activated")
    )


@router.put("/users/{user_id}/deactivate", response_model=APIResponse[MessageResponse])
async def deactivate_user(
    user_id: int,
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Deactivate user account (admin only).
    
    - Cannot deactivate yourself
    """
    if user_id == current_user.id:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Cannot deactivate yourself",
        )
    
    result = await db.execute(
        select(User).where(User.id == user_id)
    )
    user = result.scalar_one_or_none()
    
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="User not found",
        )
    
    user.is_active = False
    await db.commit()
    
    logger.info(f"User {user.email} deactivated by {current_user.email}")
    
    return APIResponse(
        data=MessageResponse(message=f"User {user.email} deactivated")
    )


# ============================================================================
# Role & Permission Management
# ============================================================================

@router.get("/roles", response_model=APIResponse[List[RoleResponse]])
async def list_roles(
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """List all roles (admin only)."""
    result = await db.execute(
        select(Role).where(Role.is_active == True).order_by(Role.name)
    )
    roles = result.scalars().all()
    
    return APIResponse(
        data=[RoleResponse.model_validate(role) for role in roles]
    )


@router.get("/permissions", response_model=APIResponse[List[PermissionResponse]])
async def list_permissions(
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """List all permissions (admin only)."""
    result = await db.execute(
        select(Permission).order_by(Permission.resource, Permission.action)
    )
    permissions = result.scalars().all()
    
    return APIResponse(
        data=[PermissionResponse.model_validate(perm) for perm in permissions]
    )


# ============================================================================
# Site Configuration
# ============================================================================

@router.get("/site-parameters", response_model=APIResponse[dict])
async def get_site_parameters(
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Get all site parameters (admin only).
    
    Returns dict of parameter_name: value
    """
    result = await db.execute(select(SiteParameter))
    params = result.scalars().all()
    
    param_dict = {param.parameter_name: param.value for param in params}
    
    return APIResponse(data=param_dict)


@router.put("/site-parameters/{param_name}", response_model=APIResponse[MessageResponse])
async def update_site_parameter(
    param_name: str,
    param_data: SiteParameterUpdate,
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """Update site parameter (admin only)."""
    result = await db.execute(
        select(SiteParameter).where(SiteParameter.parameter_name == param_name)
    )
    param = result.scalar_one_or_none()
    
    if not param:
        # Create if doesn't exist
        param = SiteParameter(
            parameter_name=param_name,
            value=param_data.value,
        )
        db.add(param)
    else:
        param.value = param_data.value
    
    await db.commit()
    
    logger.info(
        f"Site parameter {param_name} updated to '{param_data.value}' by {current_user.email}"
    )
    
    return APIResponse(
        data=MessageResponse(message=f"Parameter {param_name} updated")
    )


# ============================================================================
# System Stats & Reports
# ============================================================================

@router.get("/stats/dashboard", response_model=APIResponse[dict])
async def get_dashboard_stats(
    current_user: User = Depends(get_current_admin_user),
    db: AsyncSession = Depends(get_db),
):
    """
    Get dashboard statistics (admin only).
    
    Returns overview stats for admin dashboard.
    """
    from app.db.models import Order, Product, Part
    
    # Total users
    user_count_result = await db.execute(select(func.count(User.id)))
    total_users = user_count_result.scalar()
    
    # Active users (logged in last 30 days)
    from datetime import datetime, timedelta
    thirty_days_ago = datetime.utcnow() - timedelta(days=30)
    active_users_result = await db.execute(
        select(func.count(User.id)).where(User.last_login >= thirty_days_ago)
    )
    active_users = active_users_result.scalar()
    
    # Total orders
    order_count_result = await db.execute(select(func.count(Order.id)))
    total_orders = order_count_result.scalar()
    
    # Pending orders
    pending_orders_result = await db.execute(
        select(func.count(Order.id)).where(Order.status_id == 1)
    )
    pending_orders = pending_orders_result.scalar()
    
    # Total revenue
    revenue_result = await db.execute(
        select(func.sum(Order.total)).where(Order.status_id.in_([3, 6, 7]))  # Shipped, Completed, Archived
    )
    total_revenue = revenue_result.scalar() or 0
    
    # Total products
    product_count_result = await db.execute(
        select(func.count(Product.id)).where(Product.is_active == True)
    )
    total_products = product_count_result.scalar()
    
    # Low stock parts
    low_stock_result = await db.execute(
        select(func.count(Part.id)).where(
            Part.is_active == True,
            Part.stock_quantity <= Part.low_stock_threshold,
        )
    )
    low_stock_parts = low_stock_result.scalar()
    
    return APIResponse(
        data={
            "users": {
                "total": total_users,
                "active_last_30_days": active_users,
            },
            "orders": {
                "total": total_orders,
                "pending": pending_orders,
            },
            "revenue": {
                "total": float(total_revenue),
            },
            "inventory": {
                "total_products": total_products,
                "low_stock_parts": low_stock_parts,
            },
        }
    )

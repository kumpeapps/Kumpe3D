"""
API Dependencies

Common dependencies for API endpoints including authentication, authorization, and database.
"""

from typing import Optional, List
from fastapi import Depends, HTTPException, status, Header
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select

from app.db.session import get_db
from app.db.models import User, Role, Permission
from app.core.security import decode_token, verify_token_type
from app.core.logging import get_logger

logger = get_logger(__name__)
security = HTTPBearer()


async def get_current_user(
    credentials: HTTPAuthorizationCredentials = Depends(security),
    db: AsyncSession = Depends(get_db),
) -> User:
    """
    Get current authenticated user from JWT token.
    
    Raises:
        HTTPException: If token is invalid or user not found.
    """
    token = credentials.credentials
    payload = decode_token(token)
    
    # Verify token type
    if not verify_token_type(payload, "access"):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid token type",
        )
    
    user_id = payload.get("sub")
    if not user_id:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid token payload",
        )
    
    # Get user from database
    result = await db.execute(
        select(User).where(User.id == int(user_id))
    )
    user = result.scalar_one_or_none()
    
    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="User not found",
        )
    
    if not user.is_active:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="User account is inactive",
        )
    
    return user


async def get_current_active_user(
    current_user: User = Depends(get_current_user),
) -> User:
    """Get current active user."""
    if not current_user.is_active:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Inactive user",
        )
    return current_user


async def get_current_admin_user(
    current_user: User = Depends(get_current_active_user),
) -> User:
    """
    Get current user and verify admin role.
    
    Raises:
        HTTPException: If user is not an admin.
    """
    if not current_user.is_admin:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Admin access required",
        )
    return current_user


def check_permission(permission_name: str):
    """
    Dependency factory to check if user has specific permission.
    
    Usage:
        @router.post("/products")
        async def create_product(
            user: User = Depends(check_permission("products:create"))
        ):
            ...
    """
    async def permission_checker(
        current_user: User = Depends(get_current_active_user),
    ) -> User:
        # Get user permissions through roles
        user_permissions = set()
        for role in current_user.roles:
            for permission in role.permissions:
                user_permissions.add(permission.name)
        
        if permission_name not in user_permissions:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail=f"Permission denied: {permission_name}",
            )
        
        return current_user
    
    return permission_checker


async def get_optional_current_user(
    credentials: Optional[HTTPAuthorizationCredentials] = Depends(security),
    db: AsyncSession = Depends(get_db),
) -> Optional[User]:
    """
    Get current user if token is provided, otherwise return None.
    Useful for endpoints that work for both authenticated and guest users.
    """
    if not credentials:
        return None
    
    try:
        return await get_current_user(credentials, db)
    except HTTPException:
        return None


async def get_session_id(
    x_session_id: Optional[str] = Header(None),
) -> str:
    """
    Get session ID from header for guest cart tracking.
    
    Raises:
        HTTPException: If session ID is not provided.
    """
    if not x_session_id:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Session ID required",
        )
    return x_session_id


async def get_optional_session_id(
    x_session_id: Optional[str] = Header(None),
) -> Optional[str]:
    """Get optional session ID from header."""
    return x_session_id

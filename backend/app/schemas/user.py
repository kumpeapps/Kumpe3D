"""
User Schemas

Pydantic models for user-related requests and responses.
"""

from datetime import datetime
from typing import Optional, List
from pydantic import BaseModel, EmailStr, Field, ConfigDict


class RoleBase(BaseModel):
    """Base role schema."""
    
    name: str
    description: Optional[str] = None


class RoleResponse(RoleBase):
    """Role response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    is_active: bool
    created_at: datetime


class PermissionBase(BaseModel):
    """Base permission schema."""
    
    name: str
    resource: str
    action: str
    description: Optional[str] = None


class PermissionResponse(PermissionBase):
    """Permission response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    created_at: datetime


class UserBase(BaseModel):
    """Base user schema."""
    
    email: EmailStr
    username: Optional[str] = None
    first_name: Optional[str] = None
    last_name: Optional[str] = None


class UserCreate(UserBase):
    """User creation schema."""
    
    password: str = Field(..., min_length=8, max_length=100)


class UserUpdate(BaseModel):
    """User update schema."""
    
    email: Optional[EmailStr] = None
    username: Optional[str] = None
    first_name: Optional[str] = None
    last_name: Optional[str] = None
    is_active: Optional[bool] = None


class UserResponse(UserBase):
    """User response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    is_active: bool
    is_verified: bool
    created_at: datetime
    last_login: Optional[datetime] = None
    roles: List[RoleResponse] = []


class UserLogin(BaseModel):
    """User login schema."""
    
    email: EmailStr
    password: str


class UserRegister(UserCreate):
    """User registration schema."""
    
    pass


class TokenResponse(BaseModel):
    """Token response schema."""
    
    access_token: str
    refresh_token: str
    token_type: str = "bearer"
    expires_in: int


class TokenRefresh(BaseModel):
    """Token refresh request schema."""
    
    refresh_token: str


class PasswordChange(BaseModel):
    """Password change schema."""
    
    current_password: str
    new_password: str = Field(..., min_length=8, max_length=100)

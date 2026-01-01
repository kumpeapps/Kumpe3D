"""
User Model

Defines User, Role, Permission, and related tables for RBAC.
"""

from datetime import datetime
from typing import List

from sqlalchemy import String, Integer, Boolean, DateTime, Text, ForeignKey, Table, Column
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.db.base import Base


# Association table for user roles
user_roles = Table(
    "user_roles",
    Base.metadata,
    Column("user_id", Integer, ForeignKey("users.id", ondelete="CASCADE"), primary_key=True),
    Column("role_id", Integer, ForeignKey("roles.id", ondelete="CASCADE"), primary_key=True),
    Column("assigned_at", DateTime, default=datetime.utcnow),
    Column("assigned_by", Integer, ForeignKey("users.id", ondelete="SET NULL"), nullable=True),
)


# Association table for role permissions
role_permissions = Table(
    "role_permissions",
    Base.metadata,
    Column("role_id", Integer, ForeignKey("roles.id", ondelete="CASCADE"), primary_key=True),
    Column("permission_id", Integer, ForeignKey("permissions.id", ondelete="CASCADE"), primary_key=True),
    Column("created_at", DateTime, default=datetime.utcnow),
)


class User(Base):
    """User model for authentication and account management."""
    
    __tablename__ = "users"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    email: Mapped[str] = mapped_column(String(255), unique=True, index=True, nullable=False)
    username: Mapped[str | None] = mapped_column(String(100), unique=True, index=True, nullable=True)
    password_hash: Mapped[str] = mapped_column(String(255), nullable=False)
    first_name: Mapped[str | None] = mapped_column(String(100), nullable=True)
    last_name: Mapped[str | None] = mapped_column(String(100), nullable=True)
    
    # Account status
    is_active: Mapped[bool] = mapped_column(Boolean, default=True, index=True)
    is_verified: Mapped[bool] = mapped_column(Boolean, default=False)
    
    # Security
    failed_login_attempts: Mapped[int] = mapped_column(Integer, default=0)
    locked_until: Mapped[datetime | None] = mapped_column(DateTime, nullable=True)
    last_login: Mapped[datetime | None] = mapped_column(DateTime, nullable=True)
    
    # Timestamps
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    roles: Mapped[List["Role"]] = relationship("Role", secondary=user_roles, back_populates="users", lazy="selectin")
    cart_items: Mapped[List["CartItem"]] = relationship("CartItem", back_populates="user", cascade="all, delete-orphan")
    orders: Mapped[List["Order"]] = relationship("Order", back_populates="user")
    addresses: Mapped[List["Address"]] = relationship("Address", back_populates="user", cascade="all, delete-orphan")
    
    def __repr__(self) -> str:
        return f"<User(id={self.id}, email='{self.email}')>"
    
    @property
    def is_admin(self) -> bool:
        """Check if user has admin role."""
        return any(role.name == "admin" for role in self.roles)
    
    @property
    def full_name(self) -> str:
        """Get user's full name."""
        if self.first_name and self.last_name:
            return f"{self.first_name} {self.last_name}"
        return self.email


class Role(Base):
    """Role model for RBAC."""
    
    __tablename__ = "roles"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    name: Mapped[str] = mapped_column(String(50), unique=True, index=True, nullable=False)
    description: Mapped[str | None] = mapped_column(String(255), nullable=True)
    is_active: Mapped[bool] = mapped_column(Boolean, default=True)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    
    # Relationships
    users: Mapped[List["User"]] = relationship("User", secondary=user_roles, back_populates="roles")
    permissions: Mapped[List["Permission"]] = relationship("Permission", secondary=role_permissions, back_populates="roles", lazy="selectin")
    
    def __repr__(self) -> str:
        return f"<Role(id={self.id}, name='{self.name}')>"


class Permission(Base):
    """Permission model for granular access control."""
    
    __tablename__ = "permissions"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    name: Mapped[str] = mapped_column(String(100), unique=True, index=True, nullable=False)
    resource: Mapped[str] = mapped_column(String(50), index=True, nullable=False)
    action: Mapped[str] = mapped_column(String(50), nullable=False)
    description: Mapped[str | None] = mapped_column(String(255), nullable=True)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    
    # Relationships
    roles: Mapped[List["Role"]] = relationship("Role", secondary=role_permissions, back_populates="permissions")
    
    def __repr__(self) -> str:
        return f"<Permission(id={self.id}, name='{self.name}')>"


# Forward references for type hints
from app.db.models.product import CartItem, Order, Address  # noqa: E402

"""
Order and Address Models

Defines Order, OrderItem, OrderHistory, Address, Country, ZipCode models.
"""

from datetime import datetime
from typing import List, Optional
from decimal import Decimal

from sqlalchemy import String, Integer, Boolean, DateTime, Text, ForeignKey, Numeric
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.db.base import Base


class Order(Base):
    """Customer orders."""
    
    __tablename__ = "orders"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    order_number: Mapped[str] = mapped_column(String(50), unique=True, index=True, nullable=False)
    user_id: Mapped[int | None] = mapped_column(Integer, ForeignKey("users.id"), index=True, nullable=True)
    session_id: Mapped[str] = mapped_column(String(255), nullable=False)
    
    # Customer info
    email: Mapped[str] = mapped_column(String(255), index=True, nullable=False)
    first_name: Mapped[str] = mapped_column(String(100), nullable=False)
    last_name: Mapped[str] = mapped_column(String(100), nullable=False)
    company_name: Mapped[str | None] = mapped_column(String(255), nullable=True)
    
    # Addresses
    shipping_address_id: Mapped[int | None] = mapped_column(Integer, ForeignKey("addresses.id"), nullable=True)
    billing_address_id: Mapped[int | None] = mapped_column(Integer, ForeignKey("addresses.id"), nullable=True)
    
    # Order totals
    subtotal: Mapped[Decimal] = mapped_column(Numeric(10, 2), nullable=False)
    tax_amount: Mapped[Decimal] = mapped_column(Numeric(10, 2), default=0)
    shipping_amount: Mapped[Decimal] = mapped_column(Numeric(10, 2), default=0)
    discount_amount: Mapped[Decimal] = mapped_column(Numeric(10, 2), default=0)
    total: Mapped[Decimal] = mapped_column(Numeric(10, 2), nullable=False)
    
    # Status
    status_id: Mapped[int] = mapped_column(Integer, index=True, nullable=False, default=1)
    
    # Payment
    payment_method: Mapped[str | None] = mapped_column(String(50), nullable=True)
    payment_transaction_id: Mapped[str | None] = mapped_column(String(255), nullable=True)
    
    # Shipping
    tracking_number: Mapped[str | None] = mapped_column(String(255), nullable=True)
    carrier: Mapped[str | None] = mapped_column(String(50), nullable=True)
    
    # Additional info
    notes: Mapped[str | None] = mapped_column(Text, nullable=True)
    client_ip: Mapped[str | None] = mapped_column(String(45), nullable=True)
    client_browser: Mapped[str | None] = mapped_column(String(255), nullable=True)
    referral: Mapped[str | None] = mapped_column(String(500), nullable=True)
    
    # External references
    po_number: Mapped[str | None] = mapped_column(String(100), nullable=True)
    so_number: Mapped[str | None] = mapped_column(String(100), nullable=True)
    invoice_number: Mapped[str | None] = mapped_column(String(100), nullable=True)
    
    # Timestamps
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, index=True)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    shipped_at: Mapped[datetime | None] = mapped_column(DateTime, nullable=True)
    delivered_at: Mapped[datetime | None] = mapped_column(DateTime, nullable=True)
    
    # Relationships
    user: Mapped[Optional["User"]] = relationship("User", back_populates="orders")
    items: Mapped[List["OrderItem"]] = relationship("OrderItem", back_populates="order", cascade="all, delete-orphan")
    history: Mapped[List["OrderHistory"]] = relationship("OrderHistory", back_populates="order", cascade="all, delete-orphan")
    shipping_address: Mapped[Optional["Address"]] = relationship("Address", foreign_keys=[shipping_address_id])
    billing_address: Mapped[Optional["Address"]] = relationship("Address", foreign_keys=[billing_address_id])
    
    def __repr__(self) -> str:
        return f"<Order(id={self.id}, order_number='{self.order_number}', total={self.total})>"
    
    @property
    def status_name(self) -> str:
        """Get human-readable status name."""
        status_map = {
            1: "Pending",
            2: "Processing",
            3: "Processed",
            4: "Shipped",
            5: "Delivered",
            6: "Cancelled",
            7: "Refunded",
        }
        return status_map.get(self.status_id, "Unknown")


class OrderItem(Base):
    """Order line items."""
    
    __tablename__ = "order_items"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    order_id: Mapped[int] = mapped_column(Integer, ForeignKey("orders.id", ondelete="CASCADE"), index=True, nullable=False)
    product_id: Mapped[int | None] = mapped_column(Integer, ForeignKey("products.id", ondelete="SET NULL"), nullable=True)
    sku: Mapped[str] = mapped_column(String(20), index=True, nullable=False)
    title: Mapped[str] = mapped_column(String(255), nullable=False)
    customization: Mapped[str | None] = mapped_column(Text, nullable=True)
    quantity: Mapped[int] = mapped_column(Integer, nullable=False)
    price: Mapped[Decimal] = mapped_column(Numeric(10, 2), nullable=False)
    cost: Mapped[Decimal] = mapped_column(Numeric(10, 2), default=0)
    subtotal: Mapped[Decimal] = mapped_column(Numeric(10, 2), nullable=False)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    
    # Relationships
    order: Mapped["Order"] = relationship("Order", back_populates="items")
    product: Mapped[Optional["Product"]] = relationship("Product", back_populates="order_items")
    
    def __repr__(self) -> str:
        return f"<OrderItem(id={self.id}, order_id={self.order_id}, sku='{self.sku}')>"


class OrderHistory(Base):
    """Order status change history."""
    
    __tablename__ = "order_history"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    order_id: Mapped[int] = mapped_column(Integer, ForeignKey("orders.id", ondelete="CASCADE"), index=True, nullable=False)
    status_id: Mapped[int] = mapped_column(Integer, nullable=False)
    notes: Mapped[str | None] = mapped_column(Text, nullable=True)
    updated_by: Mapped[str] = mapped_column(String(100), nullable=False)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, index=True)
    
    # Relationships
    order: Mapped["Order"] = relationship("Order", back_populates="history")
    
    def __repr__(self) -> str:
        return f"<OrderHistory(id={self.id}, order_id={self.order_id}, status_id={self.status_id})>"


class Address(Base):
    """Shipping and billing addresses."""
    
    __tablename__ = "addresses"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    user_id: Mapped[int | None] = mapped_column(Integer, ForeignKey("users.id", ondelete="CASCADE"), index=True, nullable=True)
    first_name: Mapped[str] = mapped_column(String(100), nullable=False)
    last_name: Mapped[str] = mapped_column(String(100), nullable=False)
    company_name: Mapped[str | None] = mapped_column(String(255), nullable=True)
    address_line1: Mapped[str] = mapped_column(String(255), nullable=False)
    address_line2: Mapped[str | None] = mapped_column(String(255), nullable=True)
    city: Mapped[str] = mapped_column(String(100), nullable=False)
    state: Mapped[str] = mapped_column(String(2), nullable=False)
    zip_code: Mapped[str] = mapped_column(String(10), index=True, nullable=False)
    country: Mapped[str] = mapped_column(String(2), nullable=False)
    phone: Mapped[str | None] = mapped_column(String(20), nullable=True)
    is_default: Mapped[bool] = mapped_column(Boolean, default=False)
    address_type: Mapped[str] = mapped_column(String(20), nullable=False)  # shipping, billing, both
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    user: Mapped[Optional["User"]] = relationship("User", back_populates="addresses")
    
    def __repr__(self) -> str:
        return f"<Address(id={self.id}, city='{self.city}', state='{self.state}')>"
    
    @property
    def full_address(self) -> str:
        """Get formatted full address."""
        parts = [self.address_line1]
        if self.address_line2:
            parts.append(self.address_line2)
        parts.append(f"{self.city}, {self.state} {self.zip_code}")
        parts.append(self.country)
        return "\n".join(parts)


class Country(Base):
    """Country data for shipping."""
    
    __tablename__ = "countries"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    name: Mapped[str] = mapped_column(String(100), nullable=False)
    iso2: Mapped[str] = mapped_column(String(2), unique=True, index=True, nullable=False)
    iso3: Mapped[str] = mapped_column(String(3), unique=True, nullable=False)
    currency: Mapped[str] = mapped_column(String(3), nullable=False)
    currency_name: Mapped[str | None] = mapped_column(String(100), nullable=True)
    currency_symbol: Mapped[str | None] = mapped_column(String(5), nullable=True)
    emoji: Mapped[str | None] = mapped_column(String(10), nullable=True)
    us_sanctions: Mapped[bool] = mapped_column(Boolean, default=False)
    high_risk: Mapped[bool] = mapped_column(Boolean, default=False)
    packaging_restrictions: Mapped[bool] = mapped_column(Boolean, default=False)
    usps_block: Mapped[bool] = mapped_column(Boolean, default=False)
    is_active: Mapped[bool] = mapped_column(Boolean, default=True, index=True)
    
    def __repr__(self) -> str:
        return f"<Country(id={self.id}, name='{self.name}', iso2='{self.iso2}')>"


class ZipCode(Base):
    """ZIP code database for address validation."""
    
    __tablename__ = "zip_codes"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    zip: Mapped[str] = mapped_column(String(10), index=True, nullable=False)
    city: Mapped[str] = mapped_column(String(100), index=True, nullable=False)
    state_id: Mapped[str] = mapped_column(String(2), index=True, nullable=False)
    state_name: Mapped[str] = mapped_column(String(100), nullable=False)
    county: Mapped[str | None] = mapped_column(String(100), nullable=True)
    latitude: Mapped[Decimal | None] = mapped_column(Numeric(10, 7), nullable=True)
    longitude: Mapped[Decimal | None] = mapped_column(Numeric(10, 7), nullable=True)
    
    def __repr__(self) -> str:
        return f"<ZipCode(id={self.id}, zip='{self.zip}', city='{self.city}')>"


class SiteParameter(Base):
    """Site configuration parameters."""
    
    __tablename__ = "site_parameters"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    parameter: Mapped[str] = mapped_column(String(100), unique=True, index=True, nullable=False)
    value: Mapped[str] = mapped_column(Text, nullable=False)
    type: Mapped[str] = mapped_column(String(20), nullable=False)  # string, int, bool, json
    description: Mapped[str | None] = mapped_column(String(500), nullable=True)
    is_public: Mapped[bool] = mapped_column(Boolean, default=False, index=True)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    updated_by: Mapped[int | None] = mapped_column(Integer, ForeignKey("users.id"), nullable=True)
    
    def __repr__(self) -> str:
        return f"<SiteParameter(id={self.id}, parameter='{self.parameter}')>"


# Forward references
from app.db.models.user import User  # noqa: E402
from app.db.models.product import Product  # noqa: E402

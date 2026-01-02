"""
Product, Part, and Inventory Models

Defines Product, Part, ProductPart (BOM), Category, Catalog, Filament, and related tables.
"""

from datetime import datetime
from typing import List, Optional
from decimal import Decimal

from sqlalchemy import String, Integer, Boolean, DateTime, Text, ForeignKey, Table, Column, Numeric
from sqlalchemy.orm import Mapped, mapped_column, relationship
from sqlalchemy.ext.hybrid import hybrid_property

from app.db.base import Base


# Association table for product categories
product_categories = Table(
    "product_categories",
    Base.metadata,
    Column("product_id", Integer, ForeignKey("products.id", ondelete="CASCADE"), primary_key=True),
    Column("category_id", Integer, ForeignKey("categories.id", ondelete="CASCADE"), primary_key=True),
    Column("created_at", DateTime, default=datetime.utcnow),
)


class Product(Base):
    """Product model for the catalog."""
    
    __tablename__ = "products"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    sku: Mapped[str] = mapped_column(String(20), unique=True, index=True, nullable=False)
    title: Mapped[str] = mapped_column(String(255), nullable=False)
    description: Mapped[str | None] = mapped_column(Text, nullable=True)
    base_price: Mapped[Decimal] = mapped_column(Numeric(10, 2), nullable=False)
    cost: Mapped[Decimal] = mapped_column(Numeric(10, 2), default=0)
    weight: Mapped[Decimal | None] = mapped_column(Numeric(10, 2), nullable=True)
    
    # Status flags
    is_active: Mapped[bool] = mapped_column(Boolean, default=True, index=True)
    featured: Mapped[bool] = mapped_column(Boolean, default=False, index=True)
    allow_order_when_out_of_stock: Mapped[bool] = mapped_column(Boolean, default=False)
    
    # SEO
    meta_title: Mapped[str | None] = mapped_column(String(255), nullable=True)
    meta_description: Mapped[str | None] = mapped_column(String(500), nullable=True)
    
    # Display
    sort_order: Mapped[int] = mapped_column(Integer, default=0, index=True)
    
    # Timestamps
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    created_by: Mapped[int | None] = mapped_column(Integer, ForeignKey("users.id"), nullable=True)
    updated_by: Mapped[int | None] = mapped_column(Integer, ForeignKey("users.id"), nullable=True)
    
    # Relationships
    images: Mapped[List["ProductImage"]] = relationship("ProductImage", back_populates="product", cascade="all, delete-orphan", lazy="selectin")
    categories: Mapped[List["Category"]] = relationship("Category", secondary=product_categories, back_populates="products")
    parts: Mapped[List["ProductPart"]] = relationship("ProductPart", back_populates="product", cascade="all, delete-orphan")
    options: Mapped[List["ProductOption"]] = relationship("ProductOption", back_populates="product", cascade="all, delete-orphan", lazy="selectin")
    cart_items: Mapped[List["CartItem"]] = relationship("CartItem", back_populates="product")
    order_items: Mapped[List["OrderItem"]] = relationship("OrderItem", back_populates="product")
    
    def __repr__(self) -> str:
        return f"<Product(id={self.id}, sku='{self.sku}', title='{self.title}')>"
    
    @hybrid_property
    def stock_quantity(self) -> int:
        """
        Calculate product stock based on parts inventory.
        Returns the maximum number of complete products that can be made.
        """
        if not self.parts:
            # No parts defined = unlimited stock
            return 999999
        
        # Group parts by alternative_group
        required_parts = []
        alternative_groups = {}
        
        for product_part in self.parts:
            if product_part.is_optional:
                continue  # Skip optional parts
            
            if product_part.alternative_group is None:
                # Simple required part
                required_parts.append(product_part)
            else:
                # Alternative part (OR relationship)
                if product_part.alternative_group not in alternative_groups:
                    alternative_groups[product_part.alternative_group] = []
                alternative_groups[product_part.alternative_group].append(product_part)
        
        # Calculate stock for each requirement
        stock_limits = []
        
        # Process simple required parts
        for product_part in required_parts:
            if product_part.part and product_part.part.stock_quantity >= 0:
                available = product_part.part.stock_quantity // product_part.quantity
                stock_limits.append(available)
        
        # Process alternative groups (take the best option)
        for group_id, alternatives in alternative_groups.items():
            best_stock = 0
            for product_part in alternatives:
                if product_part.part and product_part.part.stock_quantity >= 0:
                    available = product_part.part.stock_quantity // product_part.quantity
                    best_stock = max(best_stock, available)
            stock_limits.append(best_stock)
        
        # Return minimum (bottleneck)
        return min(stock_limits) if stock_limits else 999999


class ProductImage(Base):
    """Product images."""
    
    __tablename__ = "product_images"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    product_id: Mapped[int] = mapped_column(Integer, ForeignKey("products.id", ondelete="CASCADE"), nullable=False)
    file_path: Mapped[str] = mapped_column(String(500), nullable=False)
    alt_text: Mapped[str | None] = mapped_column(String(255), nullable=True)
    sort_order: Mapped[int] = mapped_column(Integer, default=0, index=True)
    is_primary: Mapped[bool] = mapped_column(Boolean, default=False)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    
    # Relationships
    product: Mapped["Product"] = relationship("Product", back_populates="images")
    
    def __repr__(self) -> str:
        return f"<ProductImage(id={self.id}, product_id={self.product_id})>"


class Part(Base):
    """Individual parts that make up products."""
    
    __tablename__ = "parts"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    part_number: Mapped[str] = mapped_column(String(50), unique=True, index=True, nullable=False)
    name: Mapped[str] = mapped_column(String(255), index=True, nullable=False)
    description: Mapped[str | None] = mapped_column(Text, nullable=True)
    
    # Inventory
    stock_quantity: Mapped[int] = mapped_column(Integer, default=0, index=True)
    low_stock_threshold: Mapped[int] = mapped_column(Integer, default=5)
    reorder_quantity: Mapped[int] = mapped_column(Integer, default=10)
    unit_cost: Mapped[Decimal] = mapped_column(Numeric(10, 2), default=0)
    
    # Supplier info
    supplier: Mapped[str | None] = mapped_column(String(255), nullable=True)
    supplier_part_number: Mapped[str | None] = mapped_column(String(100), nullable=True)
    location: Mapped[str | None] = mapped_column(String(100), nullable=True)
    notes: Mapped[str | None] = mapped_column(Text, nullable=True)
    
    # Status
    is_active: Mapped[bool] = mapped_column(Boolean, default=True, index=True)
    
    # Timestamps
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    products: Mapped[List["ProductPart"]] = relationship("ProductPart", back_populates="part")
    
    def __repr__(self) -> str:
        return f"<Part(id={self.id}, part_number='{self.part_number}', name='{self.name}')>"
    
    @property
    def is_low_stock(self) -> bool:
        """Check if part is low on stock."""
        return self.stock_quantity < self.low_stock_threshold


class ProductPart(Base):
    """
    Many-to-many relationship between products and parts (Bill of Materials).
    Supports flexible requirements including alternative parts (OR relationships).
    """
    
    __tablename__ = "product_parts"
    
    product_id: Mapped[int] = mapped_column(Integer, ForeignKey("products.id", ondelete="CASCADE"), primary_key=True)
    part_id: Mapped[int] = mapped_column(Integer, ForeignKey("parts.id", ondelete="CASCADE"), primary_key=True)
    quantity: Mapped[int] = mapped_column(Integer, nullable=False, default=1)
    is_optional: Mapped[bool] = mapped_column(Boolean, default=False)
    alternative_group: Mapped[int | None] = mapped_column(Integer, index=True, nullable=True)
    priority: Mapped[int] = mapped_column(Integer, default=0)
    notes: Mapped[str | None] = mapped_column(String(500), nullable=True)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    
    # Relationships
    product: Mapped["Product"] = relationship("Product", back_populates="parts")
    part: Mapped["Part"] = relationship("Part", back_populates="products", lazy="joined")
    
    def __repr__(self) -> str:
        return f"<ProductPart(product_id={self.product_id}, part_id={self.part_id}, qty={self.quantity})>"


class ProductOption(Base):
    """
    Customer-facing product options (e.g., color, size, material).
    Each option can reference a part for inventory tracking.
    """
    
    __tablename__ = "product_options"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    product_id: Mapped[int] = mapped_column(Integer, ForeignKey("products.id", ondelete="CASCADE"), nullable=False, index=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)  # e.g., "Red PLA", "Large", "Matte Finish"
    option_group: Mapped[str] = mapped_column(String(100), nullable=False, index=True)  # e.g., "Color", "Size", "Finish"
    price_modifier: Mapped[Decimal] = mapped_column(Numeric(10, 2), default=0)
    part_id: Mapped[int | None] = mapped_column(Integer, ForeignKey("parts.id"), nullable=True)  # Link to inventory
    sort_order: Mapped[int] = mapped_column(Integer, default=0)
    is_active: Mapped[bool] = mapped_column(Boolean, default=True, index=True)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    product: Mapped["Product"] = relationship("Product", back_populates="options")
    part: Mapped[Optional["Part"]] = relationship("Part")
    
    def __repr__(self) -> str:
        return f"<ProductOption(id={self.id}, name='{self.name}', group='{self.option_group}')>"


class Category(Base):
    """Product categories."""
    
    __tablename__ = "categories"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    name: Mapped[str] = mapped_column(String(100), unique=True, nullable=False)
    slug: Mapped[str] = mapped_column(String(100), unique=True, index=True, nullable=False)
    description: Mapped[str | None] = mapped_column(Text, nullable=True)
    photo: Mapped[str | None] = mapped_column(String(500), nullable=True)
    icon: Mapped[str | None] = mapped_column(String(50), nullable=True)
    parent_id: Mapped[int | None] = mapped_column(Integer, ForeignKey("categories.id"), nullable=True, index=True)
    sort_order: Mapped[int] = mapped_column(Integer, default=0, index=True)
    is_active: Mapped[bool] = mapped_column(Boolean, default=True, index=True)
    show_on_home: Mapped[bool] = mapped_column(Boolean, default=False, index=True)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    products: Mapped[List["Product"]] = relationship("Product", secondary=product_categories, back_populates="categories")
    children: Mapped[List["Category"]] = relationship("Category", backref="parent", remote_side=[id])
    
    def __repr__(self) -> str:
        return f"<Category(id={self.id}, name='{self.name}')>"


class Catalog(Base):
    """Product catalogs/collections."""
    
    __tablename__ = "catalogs"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    name: Mapped[str] = mapped_column(String(100), unique=True, nullable=False)
    slug: Mapped[str] = mapped_column(String(100), unique=True, index=True, nullable=False)
    description: Mapped[str | None] = mapped_column(Text, nullable=True)
    sort_order: Mapped[int] = mapped_column(Integer, default=0, index=True)
    is_active: Mapped[bool] = mapped_column(Boolean, default=True)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    def __repr__(self) -> str:
        return f"<Catalog(id={self.id}, name='{self.name}')>"


class Filament(Base):
    """Filament types and colors for 3D printing."""
    
    __tablename__ = "filament"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    swatch_id: Mapped[str] = mapped_column(String(3), unique=True, index=True, nullable=False)
    color_name: Mapped[str] = mapped_column(String(100), nullable=False)
    hex_color: Mapped[str | None] = mapped_column(String(7), nullable=True)
    type: Mapped[str] = mapped_column(String(50), index=True, nullable=False)
    brand: Mapped[str | None] = mapped_column(String(100), nullable=True)
    is_active: Mapped[bool] = mapped_column(Boolean, default=True, index=True)
    sort_order: Mapped[int] = mapped_column(Integer, default=0)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    def __repr__(self) -> str:
        return f"<Filament(id={self.id}, swatch_id='{self.swatch_id}', color='{self.color_name}')>"


class CartItem(Base):
    """Shopping cart items."""
    
    __tablename__ = "cart_items"
    
    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    session_id: Mapped[str] = mapped_column(String(255), index=True, nullable=False)
    user_id: Mapped[int | None] = mapped_column(Integer, ForeignKey("users.id", ondelete="CASCADE"), index=True, nullable=True)
    product_id: Mapped[int] = mapped_column(Integer, ForeignKey("products.id", ondelete="CASCADE"), index=True, nullable=False)
    sku: Mapped[str] = mapped_column(String(20), nullable=False)
    customization: Mapped[str | None] = mapped_column(Text, nullable=True)
    quantity: Mapped[int] = mapped_column(Integer, nullable=False, default=1)
    price: Mapped[Decimal] = mapped_column(Numeric(10, 2), nullable=False)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow, index=True)
    
    # Relationships
    user: Mapped[Optional["User"]] = relationship("User", back_populates="cart_items")
    product: Mapped["Product"] = relationship("Product", back_populates="cart_items")
    
    def __repr__(self) -> str:
        return f"<CartItem(id={self.id}, session_id='{self.session_id}', sku='{self.sku}')>"


# Forward references
from app.db.models.user import User  # noqa: E402
from app.db.models.order import Order, OrderItem, Address  # noqa: E402

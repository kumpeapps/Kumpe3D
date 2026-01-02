"""
Product Schemas

Pydantic models for product-related requests and responses.
"""

from datetime import datetime
from typing import Optional, List
from decimal import Decimal
from pydantic import BaseModel, Field, ConfigDict


class ProductImageBase(BaseModel):
    """Base product image schema."""
    
    file_path: str
    alt_text: Optional[str] = None
    sort_order: int = 0
    is_primary: bool = False


class ProductImageResponse(ProductImageBase):
    """Product image response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    product_id: int
    created_at: datetime


class CategoryBase(BaseModel):
    """Base category schema."""
    
    name: str
    slug: str
    description: Optional[str] = None
    photo: Optional[str] = None
    icon: Optional[str] = None
    parent_id: Optional[int] = None
    sort_order: int = 0
    is_active: bool = True
    show_on_home: bool = False


class CategoryResponse(CategoryBase):
    """Category response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    created_at: datetime


class CatalogBase(BaseModel):
    """Base catalog schema."""
    
    name: str
    slug: str
    description: Optional[str] = None
    sort_order: int = 0
    is_active: bool = True


class CatalogResponse(CatalogBase):
    """Catalog response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    created_at: datetime


class FilamentBase(BaseModel):
    """Base filament schema."""
    
    swatch_id: str = Field(..., max_length=3)
    color_name: str
    hex_color: Optional[str] = None
    type: str
    brand: Optional[str] = None
    is_active: bool = True
    sort_order: int = 0


class FilamentResponse(FilamentBase):
    """Filament response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    created_at: datetime


class PartBase(BaseModel):
    """Base part schema (admin only)."""
    
    part_number: str
    name: str
    description: Optional[str] = None
    stock_quantity: int = 0
    low_stock_threshold: int = 5
    reorder_quantity: int = 10
    unit_cost: Decimal = Decimal("0.00")
    supplier: Optional[str] = None
    supplier_part_number: Optional[str] = None
    location: Optional[str] = None
    notes: Optional[str] = None
    is_active: bool = True


class PartResponse(PartBase):
    """Part response schema (admin only)."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    created_at: datetime
    updated_at: datetime
    is_low_stock: bool


class PartCreate(PartBase):
    """Part creation schema."""
    
    pass


class PartUpdate(BaseModel):
    """Part update schema."""
    
    name: Optional[str] = None
    description: Optional[str] = None
    stock_quantity: Optional[int] = None
    low_stock_threshold: Optional[int] = None
    reorder_quantity: Optional[int] = None
    unit_cost: Optional[Decimal] = None
    supplier: Optional[str] = None
    supplier_part_number: Optional[str] = None
    location: Optional[str] = None
    notes: Optional[str] = None
    is_active: Optional[bool] = None


class ProductPartBase(BaseModel):
    """Base product-part relationship schema (admin only)."""
    
    part_id: int
    quantity: int = 1
    is_optional: bool = False
    alternative_group: Optional[int] = None
    priority: int = 0
    notes: Optional[str] = None


class ProductPartResponse(ProductPartBase):
    """Product-part response with part details (admin only)."""
    
    model_config = ConfigDict(from_attributes=True)
    
    part: PartResponse


class ProductBase(BaseModel):
    """Base product schema."""
    
    sku: str
    title: str
    description: Optional[str] = None
    base_price: Decimal
    cost: Decimal = Decimal("0.00")
    weight: Optional[Decimal] = None
    is_active: bool = True
    featured: bool = False
    allow_order_when_out_of_stock: bool = False
    meta_title: Optional[str] = None
    meta_description: Optional[str] = None
    sort_order: int = 0


class ProductCreate(ProductBase):
    """Product creation schema."""
    
    category_ids: List[int] = []
    parts: List[ProductPartBase] = []


class ProductUpdate(BaseModel):
    """Product update schema."""
    
    title: Optional[str] = None
    description: Optional[str] = None
    base_price: Optional[Decimal] = None
    cost: Optional[Decimal] = None
    weight: Optional[Decimal] = None
    is_active: Optional[bool] = None
    featured: Optional[bool] = None
    meta_title: Optional[str] = None
    meta_description: Optional[str] = None
    sort_order: Optional[int] = None
    category_ids: Optional[List[int]] = None
    parts: Optional[List[ProductPartBase]] = None


class ProductResponse(ProductBase):
    """Product response schema (public)."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    stock_quantity: int  # Calculated from parts
    images: List[ProductImageResponse] = []
    categories: List[CategoryResponse] = []
    created_at: datetime
    updated_at: datetime


class ProductDetailResponse(ProductResponse):
    """Product detail response with additional info (admin)."""
    
    parts: List[ProductPartResponse] = []  # Only visible to admins


class ProductOptionBase(BaseModel):
    """Base product option schema."""
    
    name: str
    option_group: str
    price_modifier: Decimal = Decimal(0)
    part_id: Optional[int] = None
    sort_order: int = 0
    is_active: bool = True


class ProductOptionResponse(ProductOptionBase):
    """Product option response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    product_id: int
    created_at: datetime


class ProductWithOptionsResponse(ProductResponse):
    """Product response with customer-facing options."""
    
    options: List[ProductOptionResponse] = []


class ProductListQuery(BaseModel):
    """Product list query parameters."""
    
    page: int = Field(1, ge=1)
    per_page: int = Field(20, ge=1, le=100)
    category: Optional[str] = None
    catalog: Optional[str] = None
    search: Optional[str] = None
    featured: Optional[bool] = None
    is_active: Optional[bool] = True
    sort_by: str = "sort_order"
    sort_order: str = "asc"

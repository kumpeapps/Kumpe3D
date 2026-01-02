"""
Database Models - Import all models here for Alembic autodiscovery.
"""

from app.db.base import Base
from app.db.models.user import User, Role, Permission
from app.db.models.product import (
    Product,
    ProductImage,
    Part,
    ProductPart,
    ProductOption,
    Category,
    Catalog,
    Filament,
    CartItem,
)
from app.db.models.order import (
    Order,
    OrderItem,
    OrderHistory,
    Address,
    Country,
    ZipCode,
    SiteParameter,
)

__all__ = [
    "Base",
    "User",
    "Role",
    "Permission",
    "Product",
    "ProductImage",
    "Part",
    "ProductPart",
    "ProductOption",
    "Category",
    "Catalog",
    "Filament",
    "CartItem",
    "Order",
    "OrderItem",
    "OrderHistory",
    "Address",
    "Country",
    "ZipCode",
    "SiteParameter",
]

"""
Cart and Order Schemas

Pydantic models for cart and order-related requests and responses.
"""

from datetime import datetime
from typing import Optional, List, TYPE_CHECKING
from decimal import Decimal
from pydantic import BaseModel, EmailStr, Field, ConfigDict

if TYPE_CHECKING:
    from app.schemas.product import ProductResponse


class CartItemBase(BaseModel):
    """Base cart item schema."""
    
    sku: str
    selected_options: Optional[List[int]] = None  # List of selected option IDs
    customization_notes: Optional[str] = None
    quantity: int = Field(1, ge=1)


class CartItemCreate(CartItemBase):
    """Cart item creation schema."""
    
    pass


class CartItemUpdate(BaseModel):
    """Cart item update schema."""
    
    quantity: int = Field(1, ge=1)


class CartItemResponse(CartItemBase):
    """Cart item response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    product_id: int
    price: Decimal
    created_at: datetime
    updated_at: datetime
    product_title: Optional[str] = None
    product_image: Optional[str] = None
    option_names: Optional[List[str]] = None  # Names of selected options for display
    
    @classmethod
    def from_cart_item(cls, cart_item):
        """Create response from CartItem model with product data."""
        import json
        from app.db.models import ProductOption
        
        product_title = cart_item.product.title if cart_item.product else None
        product_image = None
        if cart_item.product and cart_item.product.images:
            # Get primary image or first image
            primary = next((img for img in cart_item.product.images if img.is_primary), None)
            product_image = (primary or cart_item.product.images[0]).file_path if primary or cart_item.product.images else None
        
        # Parse selected_options and get option names
        option_names = []
        if cart_item.selected_options:
            try:
                option_ids = json.loads(cart_item.selected_options)
                if cart_item.product and cart_item.product.options:
                    option_names = [
                        opt.name for opt in cart_item.product.options 
                        if opt.id in option_ids
                    ]
            except (json.JSONDecodeError, TypeError):
                pass
        
        return cls(
            id=cart_item.id,
            sku=cart_item.sku,
            selected_options=json.loads(cart_item.selected_options) if cart_item.selected_options else None,
            customization_notes=cart_item.customization_notes,
            quantity=cart_item.quantity,
            product_id=cart_item.product_id,
            price=cart_item.price,
            created_at=cart_item.created_at,
            updated_at=cart_item.updated_at,
            product_title=product_title,
            product_image=product_image,
            option_names=option_names,
        )


class AddressBase(BaseModel):
    """Base address schema."""
    
    first_name: str
    last_name: str
    company_name: Optional[str] = None
    address_line1: str
    address_line2: Optional[str] = None
    city: str
    state: str = Field(..., max_length=2)
    zip_code: str
    country: str = Field("US", max_length=2)
    phone: Optional[str] = None
    address_type: str = "both"  # shipping, billing, both


class AddressCreate(AddressBase):
    """Address creation schema."""
    
    is_default: bool = False


class AddressResponse(AddressBase):
    """Address response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    is_default: bool
    created_at: datetime


class OrderItemResponse(BaseModel):
    """Order item response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    sku: str
    title: str
    customization: Optional[str] = None
    quantity: int
    price: Decimal
    subtotal: Decimal


class OrderHistoryResponse(BaseModel):
    """Order history response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    status_id: int
    notes: Optional[str] = None
    updated_by: str
    created_at: datetime


class CheckoutRequest(BaseModel):
    """Checkout calculation request."""
    
    session_id: str
    user_id: Optional[int] = None
    shipping_address: AddressBase
    billing_address: Optional[AddressBase] = None


class CheckoutResponse(BaseModel):
    """Checkout calculation response."""
    
    subtotal: Decimal
    tax_amount: Decimal
    shipping_amount: Decimal
    discount_amount: Decimal
    total: Decimal
    items: List[CartItemResponse]


class OrderCreate(BaseModel):
    """Order creation request."""
    
    session_id: str
    email: EmailStr
    first_name: str
    last_name: str
    company_name: Optional[str] = None
    shipping_address: AddressBase
    billing_address: Optional[AddressBase] = None
    payment_transaction_id: str  # PayPal transaction ID
    notes: Optional[str] = None
    client_ip: Optional[str] = None
    client_browser: Optional[str] = None
    referral: Optional[str] = None


class OrderResponse(BaseModel):
    """Order response schema."""
    
    model_config = ConfigDict(from_attributes=True)
    
    id: int
    order_number: str
    email: str
    first_name: str
    last_name: str
    company_name: Optional[str] = None
    subtotal: Decimal
    tax_amount: Decimal
    shipping_amount: Decimal
    discount_amount: Decimal
    total: Decimal
    status_id: int
    status_name: str
    payment_method: Optional[str] = None
    tracking_number: Optional[str] = None
    carrier: Optional[str] = None
    items: List[OrderItemResponse] = []
    shipping_address: Optional[AddressResponse] = None
    created_at: datetime


class OrderDetailResponse(OrderResponse):
    """Order detail response with history."""
    
    history: List[OrderHistoryResponse] = []


class OrderUpdateStatus(BaseModel):
    """Order status update schema (admin)."""
    
    status_id: int = Field(..., ge=1, le=7)
    notes: Optional[str] = None
    tracking_number: Optional[str] = None
    carrier: Optional[str] = None


class TaxCalculationRequest(BaseModel):
    """Tax calculation request."""
    
    address: str
    city: str
    state: str
    zip_code: str
    subtotal: Decimal


class TaxCalculationResponse(BaseModel):
    """Tax calculation response."""
    
    tax_amount: Decimal
    tax_rate: Decimal
    taxable_amount: Decimal


class ZipCodeResponse(BaseModel):
    """ZIP code response."""
    
    model_config = ConfigDict(from_attributes=True)
    
    zip: str
    city: str
    state_id: str
    state_name: str
    county: Optional[str] = None


class CountryResponse(BaseModel):
    """Country response."""
    
    model_config = ConfigDict(from_attributes=True)
    
    name: str
    iso2: str
    iso3: str
    currency: str
    currency_symbol: Optional[str] = None
    emoji: Optional[str] = None


# Rebuild models with forward references after ProductResponse is available
def rebuild_models():
    """Rebuild models to resolve forward references."""
    try:
        from app.schemas.product import ProductResponse
        CartItemResponse.model_rebuild()
    except ImportError:
        pass  # ProductResponse may not be available yet

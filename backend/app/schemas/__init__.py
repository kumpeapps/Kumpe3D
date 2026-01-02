"""
Pydantic Schemas - Base response models.
"""

from typing import Generic, TypeVar, Optional, List
from pydantic import BaseModel


T = TypeVar("T")


class ResponseMetadata(BaseModel):
    """Metadata for paginated responses."""
    
    page: int = 1
    per_page: int = 20
    total: int = 0
    total_pages: int = 0


class ErrorDetail(BaseModel):
    """Error detail structure."""
    
    code: str
    message: str
    details: Optional[dict] = None


class APIResponse(BaseModel, Generic[T]):
    """Standard API response wrapper."""
    
    data: Optional[T] = None
    meta: Optional[ResponseMetadata] = None
    error: Optional[ErrorDetail] = None
    
    class Config:
        from_attributes = True


class MessageResponse(BaseModel):
    """Simple message response."""
    
    message: str


# Rebuild models with forward references after all schemas are imported
def _rebuild_forward_refs():
    """Rebuild models to resolve forward references."""
    try:
        from app.schemas.order import CartItemResponse
        from app.schemas.product import ProductResponse
        CartItemResponse.model_rebuild()
    except (ImportError, AttributeError):
        pass  # Schemas may not be fully loaded yet


# Call rebuild on import
_rebuild_forward_refs()

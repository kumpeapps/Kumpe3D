"""
API Router for v1 endpoints.
"""

from fastapi import APIRouter
from fastapi.responses import JSONResponse
from app.api.v1 import auth, products, cart, orders, admin
from app.core.config import settings

api_router = APIRouter()

# Health check endpoint
@api_router.get("/health", tags=["Health"])
async def health_check():
    """Health check endpoint for monitoring."""
    return JSONResponse(
        status_code=200,
        content={
            "status": "healthy",
            "environment": settings.APP_ENV,
            "version": "1.0.0",
        },
    )

# Include routers
api_router.include_router(auth.router, prefix="/auth", tags=["Authentication"])
api_router.include_router(products.router, prefix="/products", tags=["Products"])
api_router.include_router(cart.router, prefix="/cart", tags=["Cart"])
api_router.include_router(orders.router, prefix="/orders", tags=["Orders"])
api_router.include_router(admin.router, prefix="/admin", tags=["Admin"])

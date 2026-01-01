"""
Application Configuration

Loads configuration from environment variables using Pydantic Settings.
"""

from typing import List, Optional
from pydantic import field_validator
from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    """Application settings loaded from environment variables."""

    model_config = SettingsConfigDict(
        env_file=".env",
        env_file_encoding="utf-8",
        case_sensitive=True,
        extra="allow",
    )

    # Application
    APP_ENV: str = "development"
    DEBUG: bool = True
    LOG_LEVEL: str = "INFO"
    ENABLE_DOCS: bool = True

    # Database
    DATABASE_URL: str = "sqlite:///./kumpe3d.db"
    DB_POOL_SIZE: int = 20
    DB_MAX_OVERFLOW: int = 10
    DB_ECHO: bool = False

    # Security
    SECRET_KEY: str = "change-this-secret-key-in-production"
    ALGORITHM: str = "HS256"
    ACCESS_TOKEN_EXPIRE_MINUTES: int = 15
    REFRESH_TOKEN_EXPIRE_DAYS: int = 7
    BCRYPT_ROUNDS: int = 12

    # CORS
    CORS_ORIGINS: List[str] = ["http://localhost:4200", "https://localhost"]
    CORS_ALLOW_CREDENTIALS: bool = True

    @field_validator("CORS_ORIGINS", mode="before")
    @classmethod
    def parse_cors_origins(cls, v):
        """Parse CORS origins from string or list."""
        if isinstance(v, str):
            return [origin.strip() for origin in v.split(",")]
        return v

    # PayPal
    PAYPAL_CLIENT_ID: Optional[str] = None
    PAYPAL_SECRET: Optional[str] = None
    PAYPAL_API_URL: str = "https://api-m.sandbox.paypal.com"

    # Email (SMTP)
    SMTP_HOST: Optional[str] = None
    SMTP_PORT: int = 587
    SMTP_USERNAME: Optional[str] = None
    SMTP_PASSWORD: Optional[str] = None
    SMTP_FROM_EMAIL: str = "noreply@kumpe3d.com"
    SMTP_FROM_NAME: str = "Kumpe3D"
    SMTP_USE_TLS: bool = True

    # Pushover (Notifications)
    PUSHOVER_API_KEY: Optional[str] = None
    PUSHOVER_USER_KEY: Optional[str] = None
    PUSHOVER_ORDERS_GROUP: Optional[str] = None

    # Webhooks
    ZOHO_WEBHOOK_SECRET: Optional[str] = None
    SHIPPO_API_KEY: Optional[str] = None
    SHIPPO_WEBHOOK_SECRET: Optional[str] = None

    # File Storage
    UPLOAD_DIR: str = "/app/uploads"
    MAX_UPLOAD_SIZE: int = 10485760  # 10MB
    ALLOWED_EXTENSIONS: List[str] = ["jpg", "jpeg", "png", "gif", "webp"]

    @field_validator("ALLOWED_EXTENSIONS", mode="before")
    @classmethod
    def parse_extensions(cls, v):
        """Parse allowed extensions from string or list."""
        if isinstance(v, str):
            return [ext.strip() for ext in v.split(",")]
        return v

    # Rate Limiting
    RATE_LIMIT_ENABLED: bool = True
    RATE_LIMIT_PER_MINUTE: int = 100

    # Session
    SESSION_EXPIRE_DAYS: int = 30

    # External Services
    IMAGE_SERVICE_URL: str = "https://images.kumpeapps.com"
    GOOGLE_ANALYTICS_ID: Optional[str] = None
    SENTRY_DSN: Optional[str] = None

    @property
    def is_production(self) -> bool:
        """Check if running in production."""
        return self.APP_ENV == "production"

    @property
    def is_development(self) -> bool:
        """Check if running in development."""
        return self.APP_ENV == "development"


# Global settings instance
settings = Settings()

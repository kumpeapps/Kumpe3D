"""
Database Session Management

Configures SQLAlchemy engine and session factory.
"""

from sqlalchemy.ext.asyncio import create_async_engine, AsyncSession, async_sessionmaker
from sqlalchemy.pool import NullPool

from app.core.config import settings


# Convert DATABASE_URL to async if needed
database_url = settings.DATABASE_URL

# Handle different database types
if database_url.startswith("postgresql://"):
    database_url = database_url.replace("postgresql://", "postgresql+asyncpg://", 1)
elif database_url.startswith("mysql://"):
    database_url = database_url.replace("mysql://", "mysql+aiomysql://", 1)
elif database_url.startswith("sqlite://"):
    database_url = database_url.replace("sqlite://", "sqlite+aiosqlite://", 1)


# Create async engine
engine = create_async_engine(
    database_url,
    echo=settings.DB_ECHO,
    pool_size=settings.DB_POOL_SIZE if not database_url.startswith("sqlite") else 0,
    max_overflow=settings.DB_MAX_OVERFLOW if not database_url.startswith("sqlite") else 0,
    poolclass=NullPool if database_url.startswith("sqlite") else None,
)


# Create async session factory
AsyncSessionLocal = async_sessionmaker(
    engine,
    class_=AsyncSession,
    expire_on_commit=False,
    autocommit=False,
    autoflush=False,
)


async def get_db() -> AsyncSession:
    """
    Dependency to get database session.
    
    Usage:
        @app.get("/users")
        async def get_users(db: AsyncSession = Depends(get_db)):
            ...
    """
    async with AsyncSessionLocal() as session:
        try:
            yield session
        finally:
            await session.close()

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
engine_kwargs = {
    "echo": settings.DB_ECHO,
}

# Only add pool settings for non-SQLite databases
if not database_url.startswith("sqlite"):
    engine_kwargs["pool_size"] = settings.DB_POOL_SIZE
    engine_kwargs["max_overflow"] = settings.DB_MAX_OVERFLOW
else:
    engine_kwargs["poolclass"] = NullPool

engine = create_async_engine(database_url, **engine_kwargs)


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

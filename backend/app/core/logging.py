"""
Logging Configuration

Sets up structured logging using loguru.
"""

import sys
from loguru import logger

from app.core.config import settings


def setup_logging():
    """Configure logging for the application."""
    
    # Remove default handler
    logger.remove()
    
    # Add custom handler with format based on environment
    if settings.is_production:
        # JSON format for production (structured logging)
        logger.add(
            sys.stdout,
            format="{time:YYYY-MM-DD HH:mm:ss.SSS} | {level: <8} | {name}:{function}:{line} | {message}",
            level=settings.LOG_LEVEL,
            serialize=True,  # Output as JSON
        )
    else:
        # Pretty format for development
        logger.add(
            sys.stdout,
            format="<green>{time:YYYY-MM-DD HH:mm:ss.SSS}</green> | <level>{level: <8}</level> | <cyan>{name}</cyan>:<cyan>{function}</cyan>:<cyan>{line}</cyan> | <level>{message}</level>",
            level=settings.LOG_LEVEL,
            colorize=True,
        )
    
    logger.info(f"Logging initialized - Level: {settings.LOG_LEVEL}")


def get_logger(name: str):
    """Get a logger instance with the given name."""
    return logger.bind(name=name)

# Kumpe3D Backend

FastAPI backend for the Kumpe3D e-commerce platform.

## Overview

This is the Python FastAPI backend that provides REST API endpoints for the Kumpe3D e-commerce platform. It includes:

- RESTful API endpoints
- JWT authentication
- Role-Based Access Control (RBAC)
- SQLAlchemy ORM with database migrations
- PayPal payment integration
- Email notifications
- Webhook handlers

## Project Structure

```
backend/
├── alembic/                    # Database migrations
│   ├── versions/               # Migration scripts
│   └── env.py                  # Alembic configuration
├── app/
│   ├── api/                    # API routes
│   │   ├── v1/                 # API v1 endpoints
│   │   │   ├── __init__.py
│   │   │   ├── auth.py         # Authentication endpoints
│   │   │   ├── products.py     # Product management
│   │   │   ├── cart.py         # Shopping cart
│   │   │   ├── orders.py       # Order management
│   │   │   ├── admin.py        # Admin endpoints
│   │   │   └── webhooks.py     # Webhook handlers
│   │   └── deps.py             # API dependencies
│   ├── core/                   # Core functionality
│   │   ├── __init__.py
│   │   ├── config.py           # Configuration
│   │   ├── security.py         # Auth & security
│   │   └── logging.py          # Logging setup
│   ├── db/                     # Database
│   │   ├── base.py             # Base model class
│   │   ├── session.py          # Database session
│   │   └── models/             # SQLAlchemy models
│   │       ├── __init__.py
│   │       ├── user.py
│   │       ├── product.py
│   │       ├── order.py
│   │       └── ...
│   ├── schemas/                # Pydantic schemas
│   │   ├── __init__.py
│   │   ├── user.py
│   │   ├── product.py
│   │   ├── order.py
│   │   └── ...
│   ├── services/               # Business logic
│   │   ├── __init__.py
│   │   ├── auth_service.py
│   │   ├── product_service.py
│   │   ├── order_service.py
│   │   ├── payment_service.py
│   │   └── email_service.py
│   └── main.py                 # FastAPI application
├── tests/                      # Tests
│   ├── conftest.py
│   ├── test_api/
│   ├── test_services/
│   └── test_models/
├── Dockerfile                  # Docker configuration
├── requirements.txt            # Python dependencies
├── alembic.ini                 # Alembic configuration
├── pytest.ini                  # Pytest configuration
└── README.md                   # This file
```

## Setup

### Prerequisites

- Python 3.11 or higher
- PostgreSQL 15+ or MySQL 8.0+
- pip and virtualenv

### Installation

1. **Create virtual environment**:
   ```bash
   python -m venv venv
   source venv/bin/activate  # On Windows: venv\Scripts\activate
   ```

2. **Install dependencies**:
   ```bash
   pip install -r requirements.txt
   ```

3. **Set up environment variables**:
   ```bash
   cp ../.env.example ../.env
   # Edit .env with your configuration
   ```

4. **Run database migrations**:
   ```bash
   alembic upgrade head
   ```

5. **Start development server**:
   ```bash
   uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
   ```

## Development

### Running the Server

Development mode with auto-reload:
```bash
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

Production mode:
```bash
uvicorn app.main:app --host 0.0.0.0 --port 8000 --workers 4
```

### API Documentation

When running in development mode, interactive API documentation is available:

- **Swagger UI**: http://localhost:8000/docs
- **ReDoc**: http://localhost:8000/redoc
- **OpenAPI JSON**: http://localhost:8000/openapi.json

### Database Migrations

Create a new migration:
```bash
alembic revision --autogenerate -m "Description of changes"
```

Apply migrations:
```bash
alembic upgrade head
```

Rollback one migration:
```bash
alembic downgrade -1
```

View migration history:
```bash
alembic history
```

## Testing

Run all tests:
```bash
pytest
```

Run with coverage:
```bash
pytest --cov=app --cov-report=html
```

Run specific test file:
```bash
pytest tests/test_api/test_auth.py
```

## Code Quality

Format code with Black:
```bash
black app/ tests/
```

Lint with Ruff:
```bash
ruff check app/ tests/
```

Type check with mypy:
```bash
mypy app/
```

## API Endpoints

### Authentication
- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login user
- `POST /api/v1/auth/refresh` - Refresh access token
- `POST /api/v1/auth/logout` - Logout user
- `GET /api/v1/auth/me` - Get current user

### Products
- `GET /api/v1/products` - List products (paginated, filtered)
- `GET /api/v1/products/{id}` - Get product details
- `POST /api/v1/products` - Create product (admin)
- `PUT /api/v1/products/{id}` - Update product (admin)
- `DELETE /api/v1/products/{id}` - Delete product (admin)
- `GET /api/v1/products/{id}/images` - Get product images
- `GET /api/v1/products/{id}/filament-options` - Get filament options

### Categories & Catalogs
- `GET /api/v1/categories` - List categories
- `GET /api/v1/catalogs` - List catalogs

### Shopping Cart
- `GET /api/v1/cart` - Get cart items
- `POST /api/v1/cart/items` - Add item to cart
- `PUT /api/v1/cart/items/{id}` - Update cart item
- `DELETE /api/v1/cart/items/{id}` - Remove cart item
- `PATCH /api/v1/cart/merge` - Merge guest cart to user cart

### Orders
- `POST /api/v1/orders/checkout` - Calculate checkout totals
- `POST /api/v1/orders` - Create order
- `GET /api/v1/orders` - List user orders
- `GET /api/v1/orders/{id}` - Get order details

### Admin
- `GET /api/v1/admin/users` - List users (admin)
- `PUT /api/v1/admin/users/{id}/roles` - Update user roles (admin)
- `GET /api/v1/admin/orders` - List all orders (admin)
- `PUT /api/v1/admin/orders/{id}/status` - Update order status (admin)

### Webhooks
- `POST /api/v1/webhooks/zoho` - Zoho Books webhook
- `POST /api/v1/webhooks/shippo` - Shippo webhook

## Environment Variables

See `../.env.example` for all available environment variables.

Key variables:
- `DATABASE_URL` - Database connection string
- `SECRET_KEY` - JWT secret key
- `PAYPAL_CLIENT_ID` - PayPal client ID
- `PAYPAL_SECRET` - PayPal secret
- `SMTP_HOST` - Email server host

## Docker

Build Docker image:
```bash
docker build -t kumpe3d-backend .
```

Run container:
```bash
docker run -p 8000:8000 --env-file ../.env kumpe3d-backend
```

## Security

### Authentication
- JWT tokens with short expiration
- Refresh token rotation
- Password hashing with bcrypt (12 rounds)
- Account lockout after failed login attempts

### Authorization
- Role-Based Access Control (RBAC)
- Granular permissions system
- Route-level protection

### Best Practices
- Input validation with Pydantic
- SQL injection prevention (ORM)
- CORS configuration
- Rate limiting
- Secure password storage

## Logging

Logs are written to stdout in JSON format for easy parsing:

```json
{
  "timestamp": "2026-01-01T12:00:00.000Z",
  "level": "INFO",
  "message": "User logged in",
  "user_id": 123,
  "correlation_id": "abc-123"
}
```

## Performance

### Database
- Connection pooling (20 connections, 10 overflow)
- Indexed foreign keys and query columns
- Lazy loading for relationships
- Query optimization

### Caching
- Implement Redis for session storage (future)
- Cache product catalog (future)
- Cache user permissions (future)

## Monitoring

### Health Check
GET `/health` returns application health status.

### Metrics (Future)
- Prometheus metrics endpoint
- Request latency tracking
- Error rate monitoring

## Troubleshooting

### Database Connection Issues
1. Check `DATABASE_URL` in `.env`
2. Verify database server is running
3. Check firewall rules
4. Verify database credentials

### Migration Issues
1. Check Alembic version compatibility
2. Review migration scripts
3. Check database schema manually
4. Use `alembic current` to see current revision

### Authentication Issues
1. Verify `SECRET_KEY` is set
2. Check token expiration settings
3. Verify user roles and permissions
4. Check CORS configuration

## Contributing

1. Create feature branch from `dev`
2. Make changes
3. Write/update tests
4. Run code quality checks
5. Submit pull request

## License

Proprietary - KumpeApps LLC

---

**Version**: 1.0.0  
**Last Updated**: January 1, 2026

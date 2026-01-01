# Kumpe3D Backend

FastAPI backend for the Kumpe3D e-commerce platform.

## Tech Stack

- **Framework**: FastAPI (async/await)
- **Database**: PostgreSQL (primary), MySQL/SQLite compatible
- **ORM**: SQLAlchemy 2.0+
- **Migrations**: Alembic
- **Authentication**: JWT with refresh tokens
- **Authorization**: Role-Based Access Control (RBAC)
- **Logging**: Loguru with structured logging
- **API Docs**: OpenAPI/Swagger (automatic)

## Project Structure

```
backend/
├── app/
│   ├── main.py              # FastAPI application entry point
│   ├── core/                # Core utilities
│   │   ├── config.py        # Configuration management
│   │   ├── logging.py       # Logging setup
│   │   └── security.py      # Auth and security utilities
│   ├── db/                  # Database
│   │   ├── session.py       # Database session management
│   │   ├── base.py          # Base model class
│   │   └── models/          # SQLAlchemy models
│   │       ├── user.py      # User, Role, Permission
│   │       ├── product.py   # Product, Part, Category, etc.
│   │       └── order.py     # Order, OrderItem, Address, etc.
│   ├── api/                 # API endpoints
│   │   ├── deps.py          # Shared dependencies
│   │   └── v1/              # API version 1
│   │       ├── auth.py      # Authentication endpoints
│   │       ├── products.py  # Product endpoints
│   │       └── cart.py      # Cart endpoints
│   └── schemas/             # Pydantic models
│       ├── __init__.py      # Base response models
│       ├── user.py          # User schemas
│       ├── product.py       # Product schemas
│       └── order.py         # Order schemas
├── alembic/                 # Database migrations (to be initialized)
├── requirements.txt
├── Dockerfile               # Production Docker image
└── Dockerfile.dev           # Development Docker image
│   │   ├── user.py
│   │   ├── product.py
```

## Features

### Authentication & Authorization
- JWT-based authentication with access and refresh tokens
- Role-Based Access Control (RBAC) with granular permissions
- Password hashing with bcrypt (12 rounds)
- Token refresh rotation for security

### Product Catalog
- Flexible product management with categories and catalogs
- Product images with primary/secondary designation
- Filament/color options with swatch management
- SEO-friendly metadata (meta title, description)
- Featured products support

### Parts Inventory System (Admin Only)
- Parts management with stock tracking
- Low stock alerts and reorder quantities
- Flexible product-parts relationships:
  - Simple required parts: Product A needs Part 1 AND Part 2
  - Single part: Product C needs Part 1
  - Alternative parts: Product B needs Part 1 AND (Part 2 OR Part 3)
- Automatic stock calculation based on parts availability
- Cost tracking for profit margin analysis

### Shopping Cart
- Support for both authenticated and guest users
- Session-based cart for guests (X-Session-ID header)
- User-based cart for authenticated users
- Cart merging when guest logs in
- Product customization support
- Real-time stock validation

### Order Management (Coming Soon)
- Order creation with PayPal integration
- Order status tracking (7 states)
- Order history audit trail
- Shipping address management
- Tax and shipping calculation

## Getting Started

### Prerequisites

- Python 3.11+
- SQLite (included with Python, for development)
- MySQL 8.0+ (for production)
- Docker (optional but recommended)

### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd Kumpe3D/backend
```

2. Create virtual environment:
```bash
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
```

3. Install dependencies:
```bash
pip install -r requirements.txt
```

4. Set up environment variables:
```bash
cp ../.env.example .env
# Edit .env with your configuration
```

5. Initialize database (when Alembic is set up):
```bash
alembic upgrade head
```

6. Start the development server:
```bash
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

The API will be available at:
- API: http://localhost:8000
- Swagger docs: http://localhost:8000/docs
- ReDoc: http://localhost:8000/redoc

### Docker Development

Run with Docker Compose (recommended):
```bash
cd ..  # Go to project root
docker-compose up
```

This starts:
- Backend API on port 8000 with hot-reload
- SQLite database in `./backend/data/` directory

To stop:
```bash
docker-compose down
```

To rebuild after code changes:
```bash
docker-compose up --build
```

## Environment Variables

See `.env.example` for all available configuration options.

### Required Variables

```bash
# Database (Development - SQLite)
DATABASE_URL=sqlite+aiosqlite:///./data/kumpe3d.db

# Database (Production - MySQL)
# DATABASE_URL=mysql+aiomysql://user:password@localhost:3306/kumpe3d

# Security
SECRET_KEY=<generate-with-openssl-rand-hex-32>

# CORS
CORS_ORIGINS=http://localhost:4200,https://kumpe3d.com
```

##MySQL Production Settings (for Docker)
MYSQL_ROOT_PASSWORD=change-in-production
MYSQL_DATABASE=kumpe3d
MYSQL_USER=kumpe3d
MYSQL_PASSWORD=change-in-production

# # Optional Variables

```bash
# PayPal
PAYPAL_CLIENT_ID=your-paypal-client-id
PAYPAL_SECRET=your-paypal-secret

# Email
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password

# Pushover notifications
PUSHOVER_TOKEN=your-pushover-token
PUSHOVER_USER_KEY=your-pushover-user-key
```

## API Endpoints

### Authentication (`/api/v1/auth`)
- `POST /register` - Register new user
- `POST /login` - Login and get tokens
- `POST /refresh` - Refresh access token
- `POST /logout` - Logout (client-side token deletion)
- `POST /change-password` - Change user password
- `GET /me` - Get current user info

### Products (`/api/v1/products`)
- `GET /` - List products (public, paginated)
- `GET /{id}` - Get product details
- `POST /` - Create product (admin)
- `PUT /{id}` - Update product (admin)
- `DELETE /{id}` - Soft delete product (admin)
- `GET /admin/parts` - List parts inventory (admin)
- `POST /admin/parts` - Create part (admin)
- `PUT /admin/parts/{id}` - Update part (admin)

### Cart (`/api/v1/cart`)
- `GET /` - Get cart items
- `POST /items` - Add item to cart
- `PUT /items/{id}` - Update cart item quantity
- `DELETE /items/{id}` - Remove cart item
- `DELETE /` - Clear entire cart
- `POST /merge` - Merge guest cart into user cart

### Orders (`/api/v1/orders`) - Coming Soon
- `POST /checkout` - Calculate checkout totals
- `POST /` - Create order
- `GET /` - List user orders
- `GET /{id}` - Get order details
- `PUT /{id}/status` - Update order status (admin)

## Development

### Running Tests
```bash
pytest
pytest --cov=app tests/  # With coverage
```

### Code Quality
```bash
# Format code
black app/

# Lint code
ruff check app/

# Type checking
mypy app/
```

### Database Migrations (when initialized)

Create new migration:
```bash
alembic revision --autogenerate -m "Description of changes"
```

Apply migrations:
```bash
alembic upgrade head
```

Rollback migration:
```bash
alembic downgrade -1
```

Check current version:
```bash
alembic current
```

## API Response Format

All API responses follow this structure:

```json
{
  "data": {},           // Response data (null on error)
  "meta": {             // Metadata (pagination, etc.)
    "page": 1,
    "per_page": 20,
    "total": 100,
    "total_pages": 5
  },
  "error": {            // Error details (null on success)
    "code": "ERROR_CODE",
    "message": "Human readable message",
    "details": {}
  }
}
```

## Authentication

The API uses JWT tokens for authentication:

1. **Login** to get access and refresh tokens
2. **Access token** (15 min expiration) - use for API requests
3. **Refresh token** (7 days expiration) - use to get new access token
4. Send access token in `Authorization: Bearer <token>` header

### Example Authentication Flow

```bash
# 1. Register
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'

# 2. Login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'

# 3. Use access token
curl http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer <access_token>"

# 4. Refresh token
curl -X POST http://localhost:8000/api/v1/auth/refresh \
  -H "Content-Type: application/json" \
  -d '{"refresh_token":"<refresh_token>"}'
```

## Guest Cart (Session-Based)

For guest users, the cart is tracked by session ID:

```bash
# Generate a session ID (UUID)
SESSION_ID=$(uuidgen)

# Add to cart
curl -X POST http://localhost:8000/api/v1/cart/items \
  -H "Content-Type: application/json" \
  -H "X-Session-ID: $SESSION_ID" \
  -d '{"sku":"PROD-001","quantity":2}'

# Get cart
curl http://localhost:8000/api/v1/cart \
  -H "X-Session-ID: $SESSION_ID"
```

When the user logs in, call `/api/v1/cart/merge` to merge the guest cart into their user cart.

## Deployment

### Production with Docker

```bash
# Build production image
docker build -t kumpe3d-backend -f Dockerfile .

# Run container
docker run -d \
  -p 8000:8000 \
  --env-file .env \
  --name kumpe3d-backend \
  kumpe3d-backend
```

### Production with Docker Compose

```bash
docker-compose -f docker-compose.prod.yml up -d
```

### Health Check

The API includes a health check endpoint at `/health`:

```bash
curl http://localhost:8000/health
# Response: {"status": "healthy"}
```

This endpoint is used by:
- Docker HEALTHCHECK
- Load balancers
- Monitoring systems

## Security Best Practices

- ✅ Passwords hashed with bcrypt (12 rounds)
- ✅ JWT tokens with short expiration (15 min access, 7 day refresh)
- ✅ CORS properly configured for frontend domains
- ✅ Input validation with Pydantic models
- ✅ SQL injection prevention (ORM parameterized queries)
- ✅ Non-root user in Docker containers
- ✅ Environment variables for secrets (no hardcoded credentials)
- ✅ RBAC with granular permissions
- 🔄 Rate limiting (coming soon)
- 🔄 Token blacklist/revocation (coming soon)

## Monitoring

### Structured Logging

All logs are structured JSON with:
- Timestamp
- Level (DEBUG, INFO, WARNING, ERROR, CRITICAL)
- Message
- Context (user, request ID, etc.)

Example:
```json
{
  "timestamp": "2026-01-01T12:00:00.000Z",
  "level": "INFO",
  "message": "User logged in: user@example.com",
  "context": {
    "user_id": 123,
    "email": "user@example.com"
  }
}
```

### Key Metrics to Monitor

- API response times (p50, p95, p99)
- Error rates by endpoint
- Database query performance
- Active sessions/users
- Cart conversion rate
- Stock levels (low stock alerts)
Development (SQLite)
# Check if database file exists
ls -la backend/data/kumpe3d.db

# Production (MySQL with Docker)
# Check database is running
docker-compose -f docker-compose.prod.yml ps db

# Check MySQL logs
docker-compose -f docker-compose.prod.yml logs db

# Connect to MySQL from container
docker-compose -f docker-compose.prod.yml exec db mysql -u kumpe3d -p kumpe3d

# Check connection from container
docker-compose exec backend python -c "from app.db.session import engine; print(engine)"

# Check PostgreSQL logs
docker-compose logs db
```

### Port Already in Use

```bash
# Kill process using port 8000
lsof -ti:8000 | xargs kill -9

# Or use different port
uvicorn app.main:app --reload --port 8001
```

### Import Errors

```bash
# Ensure you're in the backend directory
cd backend

# Run from backend directory with module syntax
python -m app.main

# Or set PYTHONPATH
export PYTHONPATH="${PYTHONPATH}:$(pwd)"

```

## Contributing

1. Create feature branch from `dev`
2. Make changes with tests
3. Run code quality checks
4. Submit pull request to `dev` branch

## License

See LICENSE file in the project root.

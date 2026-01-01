# Kumpe3D E-commerce Platform

Modern e-commerce platform for 3D printed products, built with Angular and FastAPI.

## Project Overview

Kumpe3D is a complete redesign of an e-commerce platform specializing in 3D printed products. The platform features a modern Angular frontend with an admin interface, a FastAPI backend with SQLAlchemy ORM, and Docker containers for easy deployment.

### Key Features

**Customer Features**:
- Product catalog with search and filters
- Category and catalog navigation
- Filament color options for 3D printed products
- Shopping cart (guest and user)
- PayPal checkout integration
- Order tracking and history
- User registration and authentication

**Admin Features**:
- Role-Based Access Control (RBAC)
- Product management (CRUD operations)
- Order management and tracking
- User management
- Label printing (case, square, shelf)
- Site configuration
- Category and catalog management
- Webhook integrations (Zoho Books, Shippo)

## Technology Stack

### Frontend
- **Framework**: Angular 17+
- **UI Library**: Angular Material
- **State Management**: NgRx or Signals
- **HTTP Client**: Angular HttpClient with interceptors
- **Build Tool**: Angular CLI
- **Testing**: Jasmine, Karma, Cypress

### Backend
- **Language**: Python 3.11+
- **Framework**: FastAPI
- **ORM**: SQLAlchemy 2.0
- **Migrations**: Alembic
- **Authentication**: JWT (python-jose)
- **Validation**: Pydantic
- **Testing**: pytest

### Database
- **Primary**: PostgreSQL 15+
- **Compatible**: MySQL 8.0+
- **In-memory**: SQLite (development/testing)

### Infrastructure
- **Containers**: Docker
- **Orchestration**: Docker Compose
- **Web Server**: Nginx (frontend proxy)
- **ASGI Server**: Uvicorn (backend)

### External Services
- **Payments**: PayPal
- **Email**: SMTP (SendPulse)
- **Notifications**: Pushover
- **Accounting**: Zoho Books (webhook)
- **Shipping**: Shippo (webhook)

## Project Structure

```
Kumpe3D/
├── .github/
│   └── copilot-instructions.md    # GitHub Copilot instructions
├── backend/                        # FastAPI backend
│   ├── alembic/                    # Database migrations
│   ├── app/
│   │   ├── api/                    # API routes
│   │   │   ├── v1/
│   │   │   │   ├── auth.py
│   │   │   │   ├── products.py
│   │   │   │   ├── cart.py
│   │   │   │   ├── orders.py
│   │   │   │   └── admin.py
│   │   │   └── deps.py             # Dependencies
│   │   ├── core/                   # Core functionality
│   │   │   ├── config.py
│   │   │   ├── security.py
│   │   │   └── logging.py
│   │   ├── db/                     # Database
│   │   │   ├── base.py
│   │   │   ├── session.py
│   │   │   └── models/             # SQLAlchemy models
│   │   ├── schemas/                # Pydantic schemas
│   │   ├── services/               # Business logic
│   │   └── main.py                 # FastAPI app
│   ├── tests/                      # Backend tests
│   ├── Dockerfile
│   ├── requirements.txt
│   └── README.md
├── frontend/                       # Angular frontend
│   ├── src/
│   │   ├── app/
│   │   │   ├── core/               # Core services
│   │   │   ├── shared/             # Shared components
│   │   │   ├── features/           # Feature modules
│   │   │   │   ├── auth/
│   │   │   │   ├── products/
│   │   │   │   ├── cart/
│   │   │   │   ├── checkout/
│   │   │   │   ├── orders/
│   │   │   │   └── admin/
│   │   │   └── app.component.ts
│   │   ├── assets/                 # Static assets
│   │   ├── environments/           # Environment configs
│   │   └── index.html
│   ├── nginx/                      # Nginx configuration
│   ├── Dockerfile
│   ├── package.json
│   └── README.md
├── legacy/                         # Legacy PHP site (reference)
├── docker-compose.yml              # Docker orchestration
├── docker-compose.dev.yml          # Development overrides
├── .env.example                    # Environment variables template
├── .gitignore
├── DATABASE_SCHEMA.md              # Database design
├── ROADMAP.md                      # Project roadmap
├── SECURITY.md                     # Security policy
└── README.md                       # This file
```

## Getting Started

### Prerequisites

- Docker Desktop or Docker Engine + Docker Compose
- Node.js 18+ (for local frontend development)
- Python 3.11+ (for local backend development)
- PostgreSQL 15+ or MySQL 8.0+ (if not using Docker)

### Environment Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/kumpeapps/Kumpe3D.git
   cd Kumpe3D
   ```

2. **Copy environment template**:
   ```bash
   cp .env.example .env
   ```

3. **Configure environment variables**:
   Edit `.env` with your configuration:
   ```bash
   # Database
   DATABASE_URL=postgresql://user:password@db:5432/kumpe3d
   
   # Security
   SECRET_KEY=<generate-random-key>
   
   # PayPal
   PAYPAL_CLIENT_ID=<your-client-id>
   PAYPAL_SECRET=<your-secret>
   
   # Email
   SMTP_HOST=<smtp-host>
   SMTP_USERNAME=<username>
   SMTP_PASSWORD=<password>
   ```

### Development with Docker

1. **Start all services**:
   ```bash
   docker-compose -f docker-compose.yml -f docker-compose.dev.yml up
   ```

2. **Access the application**:
   - Frontend: https://localhost (with self-signed SSL)
   - Backend API: http://localhost:8000
   - API Documentation: http://localhost:8000/docs

3. **Run database migrations**:
   ```bash
   docker-compose exec backend alembic upgrade head
   ```

### Local Development

#### Backend

```bash
cd backend

# Create virtual environment
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Run migrations
alembic upgrade head

# Start development server
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

#### Frontend

```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm start

# Access at http://localhost:4200
```

### Running Tests

#### Backend Tests
```bash
cd backend
pytest
pytest --cov=app --cov-report=html  # With coverage
```

#### Frontend Tests
```bash
cd frontend
npm test                # Unit tests
npm run e2e             # E2E tests
```

## API Documentation

The FastAPI backend automatically generates interactive API documentation:

- **Swagger UI**: http://localhost:8000/docs
- **ReDoc**: http://localhost:8000/redoc
- **OpenAPI JSON**: http://localhost:8000/openapi.json

## Database Migrations

### Create a new migration
```bash
cd backend
alembic revision --autogenerate -m "Description of changes"
```

### Apply migrations
```bash
alembic upgrade head
```

### Rollback migration
```bash
alembic downgrade -1
```

### View migration history
```bash
alembic history
```

## Deployment

### Production Deployment

1. **Configure production environment**:
   ```bash
   cp .env.example .env.production
   # Edit .env.production with production values
   ```

2. **Build and start containers**:
   ```bash
   docker-compose -f docker-compose.yml up -d
   ```

3. **Run migrations**:
   ```bash
   docker-compose exec backend alembic upgrade head
   ```

4. **Create admin user** (via backend shell):
   ```bash
   docker-compose exec backend python -m app.scripts.create_admin
   ```

### SSL/TLS Configuration

The frontend Nginx container supports custom SSL certificates:

```bash
# Set environment variables
SSL_CERT_PATH=/certs/cert.pem
SSL_KEY_PATH=/certs/key.pem

# Mount certificates in docker-compose.yml
volumes:
  - /path/to/certs:/certs:ro
```

If not provided, a self-signed certificate is automatically generated.

## Authentication & Authorization

### User Roles
- **Guest**: Public access only
- **User**: Registered customer access
- **Admin**: Full system access

### JWT Tokens
- **Access Token**: 15 minutes expiration
- **Refresh Token**: 7 days expiration
- Tokens include user ID, email, and roles

### Protected Routes
Backend endpoints use dependency injection for auth:
```python
from app.api.deps import get_current_user, require_admin

@router.get("/profile")
async def get_profile(current_user: User = Depends(get_current_user)):
    return current_user

@router.post("/products")
async def create_product(user: User = Depends(require_admin)):
    # Only admins can access
```

Frontend routes use Angular guards:
```typescript
{
  path: 'admin',
  canActivate: [AuthGuard, AdminGuard],
  loadChildren: () => import('./features/admin/admin.module')
}
```

## Contributing

### Branch Strategy
- `main` - Production-ready code
- `dev` - Development branch
- `feature/*` - Feature branches
- `hotfix/*` - Critical fixes

### Commit Messages
Follow conventional commits:
```
feat(products): add image upload functionality
fix(cart): resolve quantity update bug
docs(readme): update deployment instructions
```

### Code Review
- All changes require PR review
- Run tests before submitting PR
- Update documentation with changes
- Check for security issues

## Security

### Reporting Security Issues
Please see [SECURITY.md](SECURITY.md) for information on reporting security vulnerabilities.

### Best Practices
- Never commit secrets to the repository
- Use environment variables for configuration
- Keep dependencies updated
- Run security scans regularly
- Use HTTPS in production
- Implement rate limiting
- Validate all user inputs

## Documentation

- **Project Roadmap**: [ROADMAP.md](ROADMAP.md)
- **Database Schema**: [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)
- **Copilot Instructions**: [.github/copilot-instructions.md](.github/copilot-instructions.md)
- **Security Policy**: [SECURITY.md](SECURITY.md)
- **Backend README**: [backend/README.md](backend/README.md)
- **Frontend README**: [frontend/README.md](frontend/README.md)

## Monitoring & Logging

### Application Logs
- Backend logs to stdout (structured JSON)
- Frontend logs errors to console
- Use correlation IDs for request tracing

### Health Checks
- Backend: `/health` endpoint
- Database connectivity check
- External service availability

### Metrics (Future)
- API endpoint latency
- Error rates
- Active users
- Order conversion rates

## Support

- **Issues**: https://github.com/kumpeapps/Kumpe3D/issues
- **Discussions**: https://github.com/kumpeapps/Kumpe3D/discussions
- **Email**: support@kumpe3d.com

## License

This project is proprietary software owned by KumpeApps LLC.

## Acknowledgments

- Original legacy site built with PHP
- Legacy API built with Flask
- Redesigned with modern technologies

---

**Current Status**: Phase 0 - Planning & Analysis  
**Version**: 0.1.0-alpha  
**Last Updated**: January 1, 2026

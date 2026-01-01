# GitHub Copilot Instructions for Kumpe3D Project

## Project Overview
Kumpe3D is a complete redesign of an e-commerce platform for 3D printed products. The project consists of:
- **Frontend**: Angular-based SPA with admin interface
- **Backend**: Python FastAPI REST API
- **Database**: Database-agnostic using SQLAlchemy ORM
- **Infrastructure**: Docker containers for both frontend and backend

## Architecture Principles

### Backend (Python/FastAPI)
- **Framework**: FastAPI with async/await patterns
- **ORM**: SQLAlchemy 2.0+ with declarative models
- **Migrations**: Alembic with idempotent migrations
- **Database Agnostic**: Support for PostgreSQL, MySQL, SQLite
- **Authentication**: JWT tokens with refresh token rotation
- **Authorization**: Role-Based Access Control (RBAC)
- **API Standards**: RESTful design with OpenAPI/Swagger documentation
- **Error Handling**: Consistent error responses with proper HTTP status codes
- **Logging**: Structured logging with correlation IDs
- **Testing**: pytest with >80% coverage target

### Frontend (Angular)
- **Framework**: Angular 21+ with standalone components
- **State Management**: NgRx or Signals for reactive state
- **UI Library**: Angular Material or PrimeNG
- **Authentication**: Token-based with interceptors
- **Routing**: Lazy-loaded modules for performance
- **Forms**: Reactive forms with validation
- **Admin Interface**: Separate route with RBAC guards
- **Testing**: Jasmine/Karma with Angular Testing Library

### Docker & Infrastructure
- **Backend Container**:
  - Multi-stage build for optimization
  - Non-root user for security
  - Health checks
  - Environment-based configuration
  
- **Frontend Container**:
  - Nginx for serving static files
  - SSL/TLS support (custom or self-signed)
  - API proxy to `/api` endpoint
  - Configurable via environment variables
  
- **Docker Compose**:
  - Development and production profiles
  - Volume mounts for development
  - Network isolation
  - Database service (PostgreSQL)

## Code Style & Conventions

### Python
- **Style**: PEP 8 compliance, enforced by ruff
- **Type Hints**: Required for all functions and methods
- **Docstrings**: Google style docstrings
- **Naming**: snake_case for functions/variables, PascalCase for classes
- **Imports**: Organized with isort (stdlib, third-party, local)
- **Async**: Use async/await for I/O operations
- **Error Handling**: Custom exceptions with proper error codes

### TypeScript/Angular
- **Style**: Angular style guide compliance
- **Type Safety**: Strict mode enabled, no `any` types
- **Naming**: camelCase for variables/functions, PascalCase for classes/interfaces
- **Components**: Small, focused, single-responsibility
- **Services**: Injectable services for business logic
- **Observables**: RxJS best practices, unsubscribe pattern
- **Templates**: OnPush change detection where possible

### SQL/Database
- **Migrations**: Forward-only, idempotent operations
- **Naming**: snake_case for tables and columns
- **Indexes**: Add indexes for foreign keys and query columns
- **Constraints**: Use database constraints (NOT NULL, UNIQUE, FK)
- **Seeds**: Separate seed data from migrations

## Security Requirements

### Authentication & Authorization
- JWT tokens with short expiration (15 min access, 7 day refresh)
- Password hashing with bcrypt (12 rounds minimum)
- RBAC with granular permissions (admin, user, guest)
- Rate limiting on authentication endpoints
- Account lockout after failed attempts

### API Security
- CORS properly configured for frontend domain
- Input validation using Pydantic models
- SQL injection prevention (ORM parameterized queries)
- XSS prevention (proper escaping)
- CSRF tokens for state-changing operations
- Content Security Policy headers

### Infrastructure Security
- No secrets in code or containers
- Environment variables for configuration
- TLS/SSL for all production traffic
- Container security scanning
- Minimal base images (Alpine/Distroless)
- Regular dependency updates

## API Design Patterns

### Endpoints Structure
```
/api/v1/
  /auth/
    POST /login
    POST /refresh
    POST /logout
  /products/
    GET    /          - List products (paginated)
    GET    /{id}      - Get single product
    POST   /          - Create product (admin)
    PUT    /{id}      - Update product (admin)
    DELETE /{id}      - Delete product (admin)
    GET    /{id}/images
  /cart/
    GET    /          - Get cart
    POST   /items     - Add item
    PUT    /items/{id} - Update quantity
    DELETE /items/{id} - Remove item
  /orders/
    GET    /          - List orders
    POST   /          - Create order
    GET    /{id}      - Get order details
  /admin/
    GET /users        - List users
    PUT /users/{id}/roles - Update user roles
```

### Response Format
```json
{
  "data": {},          // Success response data
  "meta": {            // Metadata for pagination, etc.
    "page": 1,
    "per_page": 20,
    "total": 100
  },
  "error": {           // Error details (if applicable)
    "code": "ERROR_CODE",
    "message": "Human readable message",
    "details": {}
  }
}
```

## Database Schema Naming

### Tables
- `users` - User accounts
- `roles` - User roles (admin, user, guest)
- `permissions` - Granular permissions
- `user_roles` - Many-to-many relationship
- `products` - Product catalog
- `product_images` - Product photos
- `filament` - Filament/color options
- `categories` - Product categories
- `catalogs` - Product catalogs
- `cart_items` - Shopping cart items
- `orders` - Customer orders
- `order_items` - Order line items
- `order_history` - Order status tracking
- `addresses` - Shipping addresses
- `countries` - Country data
- `zip_codes` - Zip code database
- `site_parameters` - Site configuration

## Environment Variables

### Backend
```bash
# Database
DATABASE_URL=postgresql://user:pass@localhost/kumpe3d
DB_ECHO=false

# Security
SECRET_KEY=<random-key>
ALGORITHM=HS256
ACCESS_TOKEN_EXPIRE_MINUTES=15
REFRESH_TOKEN_EXPIRE_DAYS=7

# CORS
CORS_ORIGINS=http://localhost:4200,https://kumpe3d.com

# External APIs
PAYPAL_CLIENT_ID=
PAYPAL_SECRET=
PAYPAL_API_URL=https://api-m.paypal.com

# Email
SMTP_HOST=
SMTP_PORT=587
SMTP_USERNAME=
SMTP_PASSWORD=
SMTP_FROM_EMAIL=

# App
APP_ENV=development
LOG_LEVEL=INFO
```

### Frontend
```bash
API_URL=http://localhost:8000
ENABLE_SSL=true
SSL_CERT_PATH=/certs/cert.pem
SSL_KEY_PATH=/certs/key.pem
```

## Testing Requirements

### Backend Tests
- Unit tests for business logic
- Integration tests for API endpoints
- Database transaction rollback in tests
- Mock external API calls
- Test authentication/authorization

### Frontend Tests
- Component unit tests
- Service tests with mocked HTTP
- Integration tests for user flows
- E2E tests for critical paths (Cypress/Playwright)

## Documentation Requirements

### Code Documentation
- README.md in each major folder
- API documentation via OpenAPI/Swagger
- Inline comments for complex logic
- Type hints and docstrings

### User Documentation
- API usage guide
- Admin interface guide
- Deployment instructions
- Environment setup guide

## Migration from Legacy

### Data Migration Strategy
1. Export legacy database schema
2. Create migration scripts for data transformation
3. Validate data integrity
4. Provide rollback procedures

### Feature Parity
- All legacy API endpoints must be replicated
- Shopping cart functionality
- Product catalog with images
- Filament color options
- Checkout with PayPal integration
- Order management
- Label printing (case, square, shelf labels)
- Shipping calculations
- Tax calculations
- Webhook integrations (Zoho, Shippo)

## Development Workflow

### Branch Strategy
- `main` - Production-ready code
- `dev` - Development branch
- `feature/*` - Feature branches
- `hotfix/*` - Critical fixes

### Commit Messages
- Follow conventional commits
- Format: `type(scope): description`
- Types: feat, fix, docs, style, refactor, test, chore

### Code Review
- All changes require PR review
- Run tests before merging
- Update documentation with changes
- Check for security issues

## Performance Targets

### Backend
- API response time: <200ms (p95)
- Database queries: <50ms (p95)
- Concurrent users: 100+
- Rate limit: 100 req/min per IP

### Frontend
- Initial load: <3s
- Time to interactive: <5s
- Lighthouse score: >90
- Bundle size: <500KB (main)

## Monitoring & Observability

### Logging
- Structured JSON logs
- Correlation IDs for request tracing
- Log levels: DEBUG, INFO, WARNING, ERROR, CRITICAL

### Metrics
- API endpoint latency
- Error rates
- Database query performance
- Active users

### Health Checks
- Backend: `/health` endpoint
- Database connectivity check
- External service availability

## Notes for AI Pair Programming

When generating code for this project:
1. **Always** include proper error handling
2. **Always** add type hints/types
3. **Consider** performance implications
4. **Follow** the naming conventions strictly
5. **Add** tests for new functionality
6. **Update** documentation when adding features
7. **Use** async patterns for I/O operations
8. **Validate** all user inputs
9. **Check** authorization before sensitive operations
10. **Log** important events and errors

When suggesting changes:
- Explain the rationale
- Consider backward compatibility
- Highlight security implications
- Suggest test cases
- Reference relevant documentation

# Kumpe3D Redesign Roadmap

## Project Overview
Complete redesign of Kumpe3D e-commerce platform from legacy PHP to modern Angular + FastAPI architecture.

**Start Date**: January 1, 2026  
**Target Completion**: TBD  
**Current Phase**: Phase 1 - Foundation

---

## Architecture

### Current (Legacy)
- **Frontend**: PHP server-rendered pages with jQuery
- **Backend**: PHP with direct MySQL queries
- **Database**: MySQL (Web_3dprints)
- **API**: Flask RESTful API (separate repo: kumpeapps/Kumpe3D-API)

### Target (New)
- **Frontend**: Angular 17+ SPA with Material UI
- **Backend**: FastAPI with SQLAlchemy ORM
- **Database**: PostgreSQL (or MySQL with SQLAlchemy)
- **Infrastructure**: Docker containers with Docker Compose
- **Admin**: RBAC-based admin interface

---

## Phases

### ✅ Phase 0: Planning & Analysis (Current)
**Status**: In Progress  
**Duration**: 1-2 days

- [x] Analyze legacy codebase structure
- [x] Review existing API endpoints
- [x] Create Copilot instructions
- [x] Create project roadmap
- [ ] Define database schema
- [ ] Create project structure
- [ ] Set up development environment

**Deliverables**:
- `.github/copilot-instructions.md`
- `ROADMAP.md` (this file)
- Database schema design document
- Project structure documentation

---

### Phase 1: Backend Foundation
**Status**: Not Started  
**Duration**: 1-2 weeks  
**Dependencies**: Phase 0

#### 1.1 Project Setup
- [ ] Create FastAPI project structure
- [ ] Set up SQLAlchemy with Alembic
- [ ] Configure database connections (PostgreSQL/MySQL)
- [ ] Set up logging and error handling
- [ ] Create Docker container for backend
- [ ] Set up pytest framework

#### 1.2 Database Models
- [ ] User and authentication models
- [ ] Role and permission models (RBAC)
- [ ] Product catalog models
- [ ] Shopping cart models
- [ ] Order models
- [ ] Address and shipping models
- [ ] Site configuration models

#### 1.3 Core Authentication
- [ ] JWT token generation and validation
- [ ] User registration endpoint
- [ ] Login/logout endpoints
- [ ] Password hashing (bcrypt)
- [ ] Refresh token mechanism
- [ ] RBAC middleware

#### 1.4 Alembic Migrations
- [ ] Initial migration with all tables
- [ ] Seed data migration (roles, permissions)
- [ ] Foreign key relationships
- [ ] Indexes for performance
- [ ] Data validation constraints

**Deliverables**:
- `backend/` folder with FastAPI application
- Database migration scripts
- Docker container for backend
- API documentation (Swagger)
- Unit tests for core functionality

---

### Phase 2: Backend API - Core Features
**Status**: Not Started  
**Duration**: 2-3 weeks  
**Dependencies**: Phase 1

#### 2.1 Product Management API
- [ ] GET `/api/v1/products` - List products (paginated, filtered)
- [ ] GET `/api/v1/products/{id}` - Get product details
- [ ] POST `/api/v1/products` - Create product (admin)
- [ ] PUT `/api/v1/products/{id}` - Update product (admin)
- [ ] DELETE `/api/v1/products/{id}` - Delete product (admin)
- [ ] GET `/api/v1/products/{id}/images` - Get product images
- [ ] GET `/api/v1/products/{id}/filament-options` - Get filament colors
- [ ] GET `/api/v1/products/categories` - List categories
- [ ] GET `/api/v1/products/catalogs` - List catalogs

#### 2.2 Shopping Cart API
- [ ] GET `/api/v1/cart` - Get cart items
- [ ] POST `/api/v1/cart/items` - Add item to cart
- [ ] PUT `/api/v1/cart/items/{id}` - Update quantity
- [ ] DELETE `/api/v1/cart/items/{id}` - Remove item
- [ ] PATCH `/api/v1/cart/merge` - Merge guest to user cart

#### 2.3 Checkout & Orders API
- [ ] POST `/api/v1/checkout/calculate` - Calculate totals with tax/shipping
- [ ] POST `/api/v1/orders` - Create order
- [ ] GET `/api/v1/orders` - List orders (user's orders)
- [ ] GET `/api/v1/orders/{id}` - Get order details
- [ ] GET `/api/v1/shipping/countries` - Get shipping countries
- [ ] GET `/api/v1/tax/calculate` - Calculate sales tax
- [ ] GET `/api/v1/zipcodes` - Zipcode lookup

#### 2.4 External Integrations
- [ ] PayPal payment verification
- [ ] Email notifications (order confirmation)
- [ ] Pushover notifications (admin alerts)
- [ ] Webhook handler for Zoho Books
- [ ] Webhook handler for Shippo

**Deliverables**:
- Complete REST API for core e-commerce features
- Integration tests for all endpoints
- OpenAPI documentation
- Rate limiting implementation

---

### Phase 3: Frontend Foundation
**Status**: Not Started  
**Duration**: 2 weeks  
**Dependencies**: Phase 2

#### 3.1 Angular Project Setup
- [ ] Create Angular project (standalone components)
- [ ] Set up Angular Material or PrimeNG
- [ ] Configure routing with lazy loading
- [ ] Set up HttpClient with interceptors
- [ ] Create authentication service
- [ ] Create state management (NgRx/Signals)
- [ ] Set up environment configurations

#### 3.2 Core Components
- [ ] Header with navigation
- [ ] Footer
- [ ] Home page
- [ ] Product listing page
- [ ] Product detail page
- [ ] Shopping cart page
- [ ] Login/register pages
- [ ] 404 page

#### 3.3 Authentication & Guards
- [ ] Login form with validation
- [ ] Registration form
- [ ] JWT token storage and management
- [ ] HTTP interceptor for auth headers
- [ ] Route guards for protected pages
- [ ] Auto-refresh token logic

#### 3.4 Docker Container
- [ ] Multi-stage Docker build
- [ ] Nginx configuration for SPA
- [ ] Environment variable injection
- [ ] SSL/TLS support (custom + self-signed)
- [ ] API proxy to `/api` endpoint

**Deliverables**:
- `frontend/` folder with Angular application
- Responsive UI matching legacy design
- Docker container with Nginx
- Environment-based configuration

---

### Phase 4: Frontend - E-commerce Features
**Status**: Not Started  
**Duration**: 2-3 weeks  
**Dependencies**: Phase 3

#### 4.1 Product Catalog
- [ ] Product grid with filters
- [ ] Category navigation
- [ ] Catalog navigation
- [ ] Search functionality
- [ ] Product card component
- [ ] Product detail view
- [ ] Image gallery with lightbox
- [ ] Filament color selector

#### 4.2 Shopping Experience
- [ ] Add to cart functionality
- [ ] Cart preview in header
- [ ] Cart page with quantity controls
- [ ] Guest cart (session-based)
- [ ] Merge cart on login
- [ ] Remove item confirmation

#### 4.3 Checkout Flow
- [ ] Checkout page (multi-step)
- [ ] Shipping address form
- [ ] Address validation
- [ ] Shipping method selection
- [ ] Tax calculation display
- [ ] Order summary
- [ ] PayPal integration
- [ ] Order confirmation page
- [ ] Email receipt

#### 4.4 User Account
- [ ] Order history page
- [ ] Order details page
- [ ] Profile management
- [ ] Password change

**Deliverables**:
- Complete e-commerce user experience
- Responsive design matching legacy site
- Payment integration
- User account features

---

### Phase 5: Admin Interface
**Status**: Not Started  
**Duration**: 2-3 weeks  
**Dependencies**: Phase 4

#### 5.1 Admin Authentication & RBAC
- [ ] Admin login page
- [ ] Role-based route guards
- [ ] Permission checking service
- [ ] Admin layout component
- [ ] Admin navigation menu

#### 5.2 User Management
- [ ] List users (paginated, searchable)
- [ ] View user details
- [ ] Edit user roles
- [ ] Assign permissions
- [ ] Deactivate/reactivate users
- [ ] User activity logs

#### 5.3 Product Management
- [ ] List products (with filters)
- [ ] Create product form
- [ ] Edit product form
- [ ] Upload product images
- [ ] Manage filament options
- [ ] Bulk operations
- [ ] Product status (active/inactive)

#### 5.4 Order Management
- [ ] List orders (with filters)
- [ ] View order details
- [ ] Update order status
- [ ] Order status history
- [ ] Print packing slip
- [ ] Print labels (case, square, shelf)
- [ ] Shipping label generation
- [ ] Tracking number updates

#### 5.5 Site Configuration
- [ ] Manage site parameters
- [ ] Category management
- [ ] Catalog management
- [ ] Shipping settings
- [ ] Tax settings

**Deliverables**:
- Complete admin interface
- RBAC implementation
- Admin user guide documentation

---

### Phase 6: Advanced Features & Polish
**Status**: Not Started  
**Duration**: 2 weeks  
**Dependencies**: Phase 5

#### 6.1 Label Printing
- [ ] Case label generation endpoint
- [ ] Square label generation endpoint
- [ ] Shelf label generation endpoint
- [ ] Label preview in admin
- [ ] Barcode/QR code generation

#### 6.2 Reporting & Analytics
- [ ] Sales dashboard
- [ ] Order statistics
- [ ] Product performance
- [ ] User analytics
- [ ] Export capabilities

#### 6.3 Performance Optimization
- [ ] Frontend bundle optimization
- [ ] Image lazy loading
- [ ] API response caching
- [ ] Database query optimization
- [ ] CDN setup for static assets

#### 6.4 Testing & Quality
- [ ] E2E tests (Cypress/Playwright)
- [ ] Accessibility audit (WCAG 2.1)
- [ ] Security audit
- [ ] Performance testing
- [ ] Load testing

**Deliverables**:
- Label printing system
- Analytics dashboard
- Performance improvements
- Test coverage >80%

---

### Phase 7: Migration & Deployment
**Status**: Not Started  
**Duration**: 1-2 weeks  
**Dependencies**: Phase 6

#### 7.1 Data Migration
- [ ] Export legacy database
- [ ] Create migration scripts
- [ ] Transform data to new schema
- [ ] Validate migrated data
- [ ] Test with production data copy

#### 7.2 Deployment Setup
- [ ] Production Docker Compose
- [ ] CI/CD pipeline (GitHub Actions)
- [ ] Production database setup
- [ ] SSL certificates
- [ ] Environment variables
- [ ] Monitoring setup (logging, metrics)
- [ ] Backup strategy

#### 7.3 Cutover Planning
- [ ] Parallel run (legacy + new)
- [ ] User acceptance testing
- [ ] Performance validation
- [ ] Rollback procedure
- [ ] DNS cutover plan
- [ ] Post-deployment monitoring

#### 7.4 Documentation
- [ ] API documentation
- [ ] Admin user guide
- [ ] Deployment guide
- [ ] Troubleshooting guide
- [ ] Architecture documentation

**Deliverables**:
- Production-ready application
- Migrated data
- Deployment documentation
- Monitoring dashboards

---

### Phase 8: Post-Launch
**Status**: Not Started  
**Duration**: Ongoing  
**Dependencies**: Phase 7

#### 8.1 Monitoring
- [ ] Error tracking
- [ ] Performance monitoring
- [ ] User behavior analytics
- [ ] Security monitoring

#### 8.2 Optimization
- [ ] Performance tuning based on metrics
- [ ] Bug fixes
- [ ] UI/UX improvements
- [ ] Feature enhancements

#### 8.3 Maintenance
- [ ] Dependency updates
- [ ] Security patches
- [ ] Database maintenance
- [ ] Backup verification

**Deliverables**:
- Stable, performant production system
- Regular updates and improvements

---

## Key Features from Legacy

### Public Features
- ✅ Product catalog browsing
- ✅ Product search and filtering
- ✅ Category navigation
- ✅ Product details with images
- ✅ Filament color options
- ✅ Shopping cart
- ✅ Guest checkout
- ✅ PayPal payment
- ✅ Order confirmation email
- ✅ User registration and login
- ✅ Order history

### Admin Features
- ✅ Product management (CRUD)
- ✅ Order management
- ✅ Order status updates
- ✅ Label printing (case, square, shelf)
- ✅ User management with RBAC
- ✅ Site configuration
- ✅ Category/catalog management
- ✅ Webhook integrations (Zoho, Shippo)

### Technical Features
- ✅ JWT authentication
- ✅ RBAC authorization
- ✅ Database migrations
- ✅ Docker containers
- ✅ SSL/TLS support
- ✅ API documentation
- ✅ Error handling
- ✅ Logging

---

## Technology Stack

### Backend
- **Language**: Python 3.11+
- **Framework**: FastAPI
- **ORM**: SQLAlchemy 2.0
- **Migrations**: Alembic
- **Auth**: JWT (python-jose)
- **Validation**: Pydantic
- **Testing**: pytest
- **Database**: PostgreSQL (primary), MySQL (compatible)

### Frontend
- **Framework**: Angular 17+
- **UI Library**: Angular Material
- **State**: NgRx or Signals
- **HTTP**: HttpClient with interceptors
- **Forms**: Reactive Forms
- **Testing**: Jasmine/Karma, Cypress
- **Build**: Angular CLI

### Infrastructure
- **Containers**: Docker
- **Orchestration**: Docker Compose
- **Web Server**: Nginx (frontend)
- **ASGI Server**: Uvicorn (backend)
- **Database**: PostgreSQL 15+
- **SSL/TLS**: Self-signed or custom certificates

### External Services
- **Payments**: PayPal
- **Email**: SMTP (SendPulse)
- **Notifications**: Pushover
- **Accounting**: Zoho Books
- **Shipping**: Shippo
- **Analytics**: Google Analytics

---

## Success Criteria

### Functional
- [ ] All legacy features replicated
- [ ] Admin interface with RBAC
- [ ] Payment processing works
- [ ] Email notifications sent
- [ ] Webhooks handled correctly
- [ ] Labels print correctly

### Performance
- [ ] API response <200ms (p95)
- [ ] Page load <3s
- [ ] Lighthouse score >90
- [ ] Support 100+ concurrent users

### Quality
- [ ] Test coverage >80%
- [ ] No critical security issues
- [ ] WCAG 2.1 AA compliance
- [ ] Zero data loss in migration

### Operational
- [ ] Docker containers build successfully
- [ ] Automated deployments
- [ ] Monitoring and alerting
- [ ] Documentation complete

---

## Risk Management

### Technical Risks
- **Database Migration**: Complex data transformation
  - *Mitigation*: Extensive testing with production data copy
- **Payment Integration**: PayPal API changes
  - *Mitigation*: Use official SDKs, thorough testing
- **Performance**: New stack may have different characteristics
  - *Mitigation*: Load testing, performance profiling

### Business Risks
- **Feature Parity**: Missing legacy features
  - *Mitigation*: Comprehensive feature audit
- **Downtime**: Migration cutover
  - *Mitigation*: Parallel run, quick rollback plan
- **User Adoption**: Different UI/UX
  - *Mitigation*: Match legacy design closely, UAT

---

## Team & Resources

### Required Skills
- Python/FastAPI development
- Angular/TypeScript development
- Docker/DevOps
- PostgreSQL/MySQL database design
- UI/UX design (matching legacy)

### Tools & Services
- GitHub (code repository)
- Docker Hub (container registry)
- VS Code (IDE)
- Postman (API testing)
- pgAdmin (database management)

---

## Communication & Updates

### Status Updates
- Update this roadmap as phases complete
- Mark items with ✅ when done
- Update dates and durations
- Document blockers and decisions

### Documentation
- Keep Copilot instructions updated
- Document architectural decisions
- Maintain API documentation
- Update deployment guides

---

## Next Steps

**Immediate Actions**:
1. ✅ Create project structure directories
2. Complete database schema design
3. Set up backend project with FastAPI
4. Create initial Alembic migrations
5. Set up Angular project
6. Create Docker configurations

**This Week**:
- Complete Phase 0 (Planning)
- Start Phase 1.1 (Backend setup)
- Create database models
- Set up authentication

---

## Notes

- Keep legacy site running during development
- Test extensively before cutover
- Maintain feature parity with legacy
- Focus on security and performance
- Document everything for future maintenance

---

**Last Updated**: January 1, 2026  
**Current Phase**: Phase 0 - Planning & Analysis  
**Next Milestone**: Complete backend foundation (Phase 1)

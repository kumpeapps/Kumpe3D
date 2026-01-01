# Kumpe3D Frontend

Angular 17 frontend for the Kumpe3D e-commerce platform.

## Tech Stack

- **Framework**: Angular 17 with standalone components
- **UI Library**: Angular Material
- **State Management**: NgRx Store & Effects
- **HTTP Client**: Angular HttpClient with interceptors
- **Styling**: SCSS with Material themes
- **Build Tool**: Angular CLI

## Features

- ✅ Standalone components (no NgModules)
- ✅ Lazy-loaded routes for performance
- ✅ JWT authentication with token refresh
- ✅ HTTP interceptors for auth and error handling
- ✅ Route guards for authentication and RBAC
- ✅ Angular Material UI components
- ✅ Responsive design
- ✅ TypeScript strict mode
- ✅ Path aliases for clean imports

## Project Structure

```
frontend/
├── src/
│   ├── app/
│   │   ├── core/                      # Core module (singleton services)
│   │   │   ├── components/            # Shared layout components
│   │   │   │   ├── header/
│   │   │   │   └── footer/
│   │   │   ├── guards/                # Route guards
│   │   │   │   ├── auth.guard.ts
│   │   │   │   └── admin.guard.ts
│   │   │   ├── interceptors/          # HTTP interceptors
│   │   │   │   ├── auth.interceptor.ts
│   │   │   │   └── error.interceptor.ts
│   │   │   ├── models/                # TypeScript interfaces
│   │   │   │   ├── auth.model.ts
│   │   │   │   ├── product.model.ts
│   │   │   │   └── order.model.ts
│   │   │   └── services/              # Core services
│   │   │       └── auth.service.ts
│   │   ├── features/                  # Feature modules (lazy-loaded)
│   │   │   ├── home/
│   │   │   ├── auth/
│   │   │   │   ├── login/
│   │   │   │   ├── register/
│   │   │   │   └── auth.routes.ts
│   │   │   ├── products/
│   │   │   ├── cart/
│   │   │   ├── checkout/
│   │   │   ├── orders/
│   │   │   └── admin/
│   │   ├── app.component.ts
│   │   ├── app.config.ts
│   │   └── app.routes.ts
│   ├── environments/
│   │   ├── environment.ts             # Development config
│   │   └── environment.prod.ts        # Production config
│   ├── index.html
│   ├── main.ts
│   └── styles.scss
├── angular.json
├── package.json
├── tsconfig.json
├── Dockerfile                          # Production build
├── Dockerfile.dev                      # Development build
└── nginx.conf                          # Nginx configuration
```

## Getting Started

### Prerequisites

- Node.js 20+
- npm or yarn

### Installation

1. Install dependencies:
```bash
npm install
```

2. Start development server:
```bash
npm start
```

The application will be available at http://localhost:4200

### Development with Docker

```bash
# From project root
docker-compose up frontend
```

## Available Scripts

```bash
npm start              # Start dev server (ng serve)
npm run build          # Build for production
npm run build:prod     # Build with production config
npm test               # Run unit tests
npm run lint           # Lint code
npm run watch          # Build and watch for changes
```

## Environment Configuration

### Development
- API URL: `http://localhost:8000/api/v1`
- Configured in [src/environments/environment.ts](src/environments/environment.ts)

### Production
- API URL: `/api/v1` (proxied by Nginx)
- Configured in [src/environments/environment.prod.ts](src/environments/environment.prod.ts)

## Authentication

The app uses JWT-based authentication:

1. User logs in with email/password
2. Backend returns access token (15 min) and refresh token (7 days)
3. Access token stored in localStorage
4. Auth interceptor adds `Authorization: Bearer <token>` to all API requests
5. On 401 error, automatically attempts token refresh
6. On auth failure, redirects to login page

### Auth Service

```typescript
import { AuthService } from '@core/services/auth.service';

// Login
authService.login({ email, password }).subscribe();

// Check auth status
const isAuthenticated = authService.isAuthenticated();
const isAdmin = authService.isAdmin();

// Get current user
authService.currentUser$.subscribe(user => {
  console.log(user);
});

// Logout
authService.logout();
```

## Route Guards

### Auth Guard
Protects routes that require authentication:

```typescript
{
  path: 'orders',
  canActivate: [authGuard],
  loadChildren: () => import('./features/orders/orders.routes')
}
```

### Admin Guard
Protects admin-only routes (checks for admin role):

```typescript
{
  path: 'admin',
  canActivate: [authGuard, adminGuard],
  loadChildren: () => import('./features/admin/admin.routes')
}
```

## Routing

The app uses lazy-loaded routes for optimal performance:

```typescript
// app.routes.ts
export const routes: Routes = [
  { path: '', loadComponent: () => import('./features/home/home.component') },
  { path: 'auth', loadChildren: () => import('./features/auth/auth.routes') },
  { path: 'products', loadChildren: () => import('./features/products/products.routes') },
  { path: 'cart', loadComponent: () => import('./features/cart/cart.component') },
  { path: 'checkout', loadComponent: () => import('./features/checkout/checkout.component') },
  { path: 'orders', canActivate: [authGuard], loadChildren: () => import('./features/orders/orders.routes') },
  { path: 'admin', canActivate: [authGuard, adminGuard], loadChildren: () => import('./features/admin/admin.routes') },
];
```

## HTTP Interceptors

### Auth Interceptor
Automatically adds JWT token to requests:

```typescript
// Adds: Authorization: Bearer <token>
```

### Error Interceptor
Handles HTTP errors globally:

```typescript
// 401 Unauthorized -> Redirect to login
// 403 Forbidden -> Show error
// Other errors -> Log to console
```

## Material Theme

The app uses Angular Material with the Indigo-Pink theme. Customize in [src/styles.scss](src/styles.scss).

## API Integration

All API calls use the environment-based API URL:

```typescript
import { environment } from '@environments/environment';

const apiUrl = `${environment.apiUrl}/endpoint`;
```

## Docker Deployment

### Development
```bash
docker-compose up
```
- Frontend: http://localhost:4200
- Backend API: http://localhost:8000
- Hot-reload enabled

### Production
```bash
docker-compose -f docker-compose.prod.yml up -d
```
- Frontend: http://localhost (or https://localhost:443)
- Nginx serves static files
- API proxied to `/api`
- Self-signed SSL certificate generated automatically

### Custom SSL Certificates

Mount your SSL certificates:

```bash
# Set environment variable
export SSL_CERT_DIR=/path/to/your/ssl/certs

# Ensure you have:
# - cert.pem (certificate)
# - key.pem (private key)

docker-compose -f docker-compose.prod.yml up -d
```

## Building for Production

```bash
# Build Angular app
npm run build:prod

# Output in dist/kumpe3d-frontend/
```

## Testing

```bash
# Run unit tests
npm test

# Run with coverage
npm test -- --code-coverage
```

## Code Style

- Use TypeScript strict mode
- Follow Angular style guide
- Use standalone components
- Prefer OnPush change detection
- Use reactive forms over template-driven
- Unsubscribe from observables (use async pipe or takeUntil)

## Contributing

1. Create feature branch from `dev`
2. Make changes with tests
3. Run linter
4. Submit pull request to `dev`

## License

See LICENSE file in project root.

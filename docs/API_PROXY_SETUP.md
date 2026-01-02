# API Proxy Setup - Complete Guide

## Overview

The Kumpe3D application uses different proxy configurations for development and production environments to enable seamless frontend-backend communication.

## Architecture

```
Development:
  Browser → http://localhost:4200 → Angular Dev Server (proxy) → http://backend:8000 → FastAPI

Production:
  Browser → https://localhost → Nginx (proxy + SSL) → http://backend:8000 → FastAPI
```

## Development Mode

### Proxy Configuration

The Angular development server uses `proxy.conf.json` to proxy API requests:

```json
{
  "/api": {
    "target": "http://backend:8000",
    "secure": false,
    "changeOrigin": true,
    "logLevel": "debug",
    "pathRewrite": {
      "^/api": "/api"
    }
  }
}
```

### How It Works

1. **Frontend makes request**: `fetch('/api/v1/products/')`
2. **Angular dev server intercepts**: Request to `/api/*`
3. **Proxies to backend**: `http://backend:8000/api/v1/products/`
4. **Returns response**: Backend responds, proxy forwards to frontend

### Environment Configuration

**src/environments/environment.ts**:
```typescript
export const environment = {
  production: false,
  apiUrl: '/api/v1',  // Relative URL for proxy
  // ...
};
```

### Docker Configuration

**Dockerfile.dev**:
```dockerfile
CMD ["ng", "serve", "--host", "0.0.0.0", "--poll", "1000", "--proxy-config", "proxy.conf.json"]
```

**docker-compose.yml**:
```yaml
services:
  backend:
    container_name: kumpe3d-backend
    ports:
      - "8000:8000"
    networks:
      - kumpe3d-network

  frontend:
    container_name: kumpe3d-frontend
    ports:
      - "4200:4200"
    depends_on:
      - backend
    networks:
      - kumpe3d-network

networks:
  kumpe3d-network:
    driver: bridge
```

## Production Mode

### Nginx Reverse Proxy

**nginx.conf** (simplified):
```nginx
# HTTP server - redirect to HTTPS
server {
    listen 80;
    server_name _;
    return 301 https://$host$request_uri;
}

# HTTPS server
server {
    listen 443 ssl http2;
    server_name _;

    # SSL Configuration
    ssl_certificate /etc/nginx/ssl/cert.pem;
    ssl_certificate_key /etc/nginx/ssl/key.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    # Root directory for Angular app
    root /usr/share/nginx/html;
    index index.html;

    # API proxy to backend
    location /api/ {
        proxy_pass http://backend:8000/api/;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_redirect off;
    }

    # SPA routing
    location / {
        try_files $uri $uri/ /index.html;
    }
}
```

### SSL Certificate Generation

**generate-ssl.sh**:
```bash
#!/bin/sh

# Check if certificates already exist
if [ -f "/etc/nginx/ssl/cert.pem" ] && [ -f "/etc/nginx/ssl/key.pem" ]; then
    echo "SSL certificates found. Using existing certificates."
else
    echo "No SSL certificates found. Generating self-signed certificate..."
    
    SSL_CN="${SSL_CN:-localhost}"
    
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/nginx/ssl/key.pem \
        -out /etc/nginx/ssl/cert.pem \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=${SSL_CN}"
    
    echo "Self-signed certificate generated successfully."
fi

# Start Nginx
exec nginx -g 'daemon off;'
```

### Production Dockerfile

**Dockerfile**:
```dockerfile
# Build stage
FROM node:20-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Production stage
FROM nginx:alpine
COPY --from=builder /app/dist/frontend/browser /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY generate-ssl.sh /docker-entrypoint.d/00-generate-ssl.sh
RUN chmod +x /docker-entrypoint.d/00-generate-ssl.sh
RUN mkdir -p /etc/nginx/ssl
EXPOSE 80 443
ENTRYPOINT ["/docker-entrypoint.d/00-generate-ssl.sh"]
```

## Testing the Proxy

### Development Tests

```bash
# Test backend directly
curl http://localhost:8000/health
# Expected: {"status":"healthy","environment":"development","version":"1.0.0"}

# Test backend API directly
curl http://localhost:8000/api/v1/products/
# Expected: {"data":[...],"meta":{...}}

# Test through Angular proxy
curl http://localhost:4200/api/v1/products/
# Expected: {"data":[...],"meta":{...}}

# Test with query parameters
curl "http://localhost:4200/api/v1/products/?page=1&per_page=5"
# Expected: {"data":[5 products],"meta":{"page":1,"per_page":5,...}}
```

### Production Tests (docker-compose.prod.yml)

```bash
# Test HTTPS endpoint
curl -k https://localhost/api/v1/products/
# Expected: {"data":[...],"meta":{...}}

# Test HTTP redirect
curl -I http://localhost
# Expected: HTTP/1.1 301 Moved Permanently
#           Location: https://...

# Test frontend
curl -k https://localhost
# Expected: Angular app HTML
```

## Troubleshooting

### Issue: 404 Not Found on API requests

**Symptom**: `curl http://localhost:4200/api/v1/products/` returns 404

**Solutions**:
1. Check proxy.conf.json exists in frontend root
2. Verify Dockerfile.dev includes `--proxy-config proxy.conf.json`
3. Check Docker network - both containers must be on same network
4. Verify backend is running: `docker compose logs backend`

### Issue: Internal Server Error

**Symptom**: API returns 500 Internal Server Error

**Solutions**:
1. Check backend logs: `docker compose logs backend --tail=50`
2. Common causes:
   - Missing relationship loading (selectinload)
   - Database connection issues
   - Missing environment variables

### Issue: CORS errors in browser console

**Symptom**: `Access-Control-Allow-Origin` errors

**Solutions**:
1. In development, proxy should handle this
2. Check proxy is configured correctly
3. Verify backend CORS settings in `app/core/config.py`

### Issue: MissingGreenlet error

**Symptom**: `MissingGreenlet: greenlet_spawn has not been called`

**Cause**: Accessing lazy-loaded relationships without eager loading

**Solution**: Add selectinload for relationships:
```python
query = query.options(
    selectinload(Product.images),
    selectinload(Product.categories),
    selectinload(Product.parts).selectinload(ProductPart.part),
)
```

## Network Flow

### Development Request Flow

1. **Browser**: `GET http://localhost:4200/api/v1/products/`
2. **Angular Dev Server** (port 4200):
   - Matches `/api/*` in proxy.conf.json
   - Forwards to `http://backend:8000/api/v1/products/`
3. **Backend** (port 8000):
   - Receives request
   - Queries database
   - Returns JSON response
4. **Angular Dev Server**:
   - Receives response from backend
   - Forwards to browser
5. **Browser**: Receives JSON data

### Production Request Flow

1. **Browser**: `GET https://localhost/api/v1/products/`
2. **Nginx** (port 443):
   - SSL/TLS termination
   - Matches `/api/` location
   - Forwards to `http://backend:8000/api/v1/products/`
3. **Backend** (port 8000):
   - Receives request (no SSL, internal network)
   - Queries database
   - Returns JSON response
4. **Nginx**:
   - Receives response from backend
   - Encrypts with SSL/TLS
   - Forwards to browser
5. **Browser**: Receives JSON data

## Best Practices

1. **Always use relative URLs** in frontend: `/api/v1/...` not `http://localhost:8000/api/v1/...`
2. **Eager load relationships** in backend queries to avoid lazy loading issues
3. **Use selectinload** for one-to-many and many-to-many relationships
4. **Test both proxy paths**:
   - Direct backend: `http://localhost:8000/api/v1/...`
   - Proxied: `http://localhost:4200/api/v1/...`
5. **Check Docker networking**: Both containers must be on the same network
6. **Monitor logs** when debugging: `docker compose logs -f backend frontend`

## Common Commands

```bash
# Start development environment
docker compose up -d --build

# Check container status
docker compose ps

# View logs
docker compose logs -f

# Restart specific service
docker compose restart frontend
docker compose restart backend

# Test connectivity between containers
docker compose exec frontend wget -O- http://backend:8000/health

# Stop all containers
docker compose down

# Start production environment
docker compose -f docker-compose.prod.yml up -d --build
```

## Status

✅ **Proxy Configuration**: Complete
- Development proxy.conf.json created
- Production nginx.conf configured
- SSL auto-generation implemented

✅ **Docker Networking**: Complete
- Both containers on kumpe3d-network
- Frontend depends on backend
- Service discovery working

✅ **API Communication**: Working
- Products list endpoint: ✅
- Health check: ✅ (direct backend only)
- Relationship loading: ✅ Fixed with selectinload

⚠️ **Known Issues**:
- Auth endpoints need debugging (create_access_token parameter issue)
- Product detail endpoint requires authentication (needs JWT fix)

## Next Steps

1. Fix authentication JWT token creation
2. Test full user flow (login → browse → add to cart)
3. Implement cart page
4. Implement checkout with PayPal
5. Test production deployment with real SSL certificates

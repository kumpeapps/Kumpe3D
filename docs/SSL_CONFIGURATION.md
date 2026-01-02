# SSL Configuration Guide

## Overview

The Kumpe3D frontend uses HTTPS with automatic SSL certificate generation. By default, a self-signed certificate is generated if no custom certificate is provided.

## Development Mode

In development mode (docker-compose.yml), the Angular dev server proxies API requests to the backend:

```yaml
# Frontend proxies /api/* to http://backend:8000/api/*
# Configure in frontend/proxy.conf.json
```

Access the application at: `http://localhost:4200`

## Production Mode

In production (docker-compose.prod.yml), Nginx serves the frontend with HTTPS and proxies API requests:

### Using Self-Signed Certificates (Default)

The production container automatically generates self-signed SSL certificates on startup:

```bash
docker compose -f docker-compose.prod.yml up -d
```

Access the application at:
- HTTP: `http://localhost` (redirects to HTTPS)
- HTTPS: `https://localhost` (self-signed certificate)

**Note**: Browsers will show a security warning for self-signed certificates. This is expected for local development.

### Using Custom SSL Certificates

To use your own SSL certificates, place them in a local `ssl/` directory:

```bash
mkdir -p ssl
# Copy your certificates
cp /path/to/your/cert.pem ssl/
cp /path/to/your/key.pem ssl/
```

Update `docker-compose.prod.yml` to uncomment the volume mount:

```yaml
frontend:
  volumes:
    # Uncomment to provide custom SSL certificates
    - ./ssl/cert.pem:/etc/nginx/ssl/cert.pem:ro
    - ./ssl/key.pem:/etc/nginx/ssl/key.pem:ro
```

### Generating Your Own SSL Certificates

#### Self-Signed Certificate (for testing)

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout ssl/key.pem \
  -out ssl/cert.pem \
  -subj "/C=US/ST=State/L=City/O=Kumpe3D/CN=kumpe3d.com"
```

#### Let's Encrypt Certificate (for production)

For public-facing production deployment, use Let's Encrypt with certbot:

```bash
# Install certbot
sudo apt-get install certbot

# Generate certificate
sudo certbot certonly --standalone -d yourdomain.com -d www.yourdomain.com

# Copy to ssl directory
sudo cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem ssl/cert.pem
sudo cp /etc/letsencrypt/live/yourdomain.com/privkey.pem ssl/key.pem
```

#### Certificate from Certificate Authority

If you have certificates from a CA (e.g., Comodo, DigiCert):

1. Place the certificate file as `ssl/cert.pem`
2. Place the private key as `ssl/key.pem`
3. If you have intermediate certificates, concatenate them:

```bash
cat your-cert.crt intermediate.crt ca-bundle.crt > ssl/cert.pem
```

## Environment Variables

Configure SSL settings via environment variables:

```bash
# In .env file
SSL_CN=kumpe3d.com              # Certificate Common Name
SSL_CERT_DIR=./ssl              # Custom certificate directory
```

## Nginx Configuration

The Nginx configuration (`frontend/nginx.conf`) includes:

- HTTP to HTTPS redirect
- SSL/TLS 1.2 and 1.3 support
- Modern cipher suite
- Security headers (X-Frame-Options, X-Content-Type-Options, etc.)
- API proxy to backend at `/api/*`
- Static asset caching
- SPA routing support

## Troubleshooting

### Certificate Not Found

If you get certificate errors, verify:

```bash
docker compose exec frontend ls -la /etc/nginx/ssl/
```

You should see:
```
cert.pem
key.pem
```

### Connection Refused

Check if Nginx is running:

```bash
docker compose logs frontend
docker compose ps
```

### API Requests Failing

Verify backend is accessible:

```bash
# From within frontend container
docker compose exec frontend wget -O- http://backend:8000/health

# Check proxy configuration
docker compose exec frontend cat /etc/nginx/nginx.conf | grep -A 10 "location /api"
```

### Browser Security Warnings

For self-signed certificates:
1. Click "Advanced" in the browser warning
2. Click "Proceed to localhost (unsafe)"
3. Or add exception for localhost

For production, always use valid CA-signed certificates.

## Network Architecture

```
Browser → Nginx (Frontend Container) → Backend Container
  ↓
HTTPS/SSL (443)
HTTP (80, redirects to 443)
  ↓
/api/* → Proxied to http://backend:8000/api/*
/*     → Served from /usr/share/nginx/html
```

## Security Best Practices

1. **Never commit SSL certificates** to version control
2. **Use strong passwords** for SSL key encryption
3. **Rotate certificates** before expiration (typically 90 days for Let's Encrypt)
4. **Use HTTP/2** (enabled by default in nginx.conf)
5. **Enable HSTS** in production (uncomment in nginx.conf if needed)
6. **Monitor certificate expiration** with automated tools

## Additional Resources

- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)
- [Mozilla SSL Configuration Generator](https://ssl-config.mozilla.org/)
- [Nginx SSL Documentation](https://nginx.org/en/docs/http/configuring_https_servers.html)

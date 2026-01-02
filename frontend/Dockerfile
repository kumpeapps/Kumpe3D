# Multi-stage build for production
FROM node:20-alpine as builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci

# Copy source code
COPY . .

# Build application
RUN npm run build:prod

# Production stage with Nginx
FROM nginx:alpine

# Copy custom nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Copy built application
COPY --from=builder /app/dist/kumpe3d-frontend /usr/share/nginx/html

# Copy SSL generation script
COPY generate-ssl.sh /usr/local/bin/generate-ssl.sh
RUN chmod +x /usr/local/bin/generate-ssl.sh

# Install OpenSSL for SSL certificate generation
RUN apk add --no-cache openssl

# Create entrypoint script
RUN echo '#!/bin/sh' > /docker-entrypoint.sh && \
    echo '/usr/local/bin/generate-ssl.sh' >> /docker-entrypoint.sh && \
    echo 'exec nginx -g "daemon off;"' >> /docker-entrypoint.sh && \
    chmod +x /docker-entrypoint.sh

# Expose ports
EXPOSE 80 443

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD wget --quiet --tries=1 --spider http://localhost/health || exit 1

# Start with entrypoint that generates SSL if needed
ENTRYPOINT ["/docker-entrypoint.sh"]

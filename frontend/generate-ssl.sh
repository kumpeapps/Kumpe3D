#!/bin/sh
# Generate self-signed SSL certificates if not provided

SSL_CERT="/etc/nginx/ssl/cert.pem"
SSL_KEY="/etc/nginx/ssl/key.pem"

# Check if certificates already exist (mounted from host)
if [ -f "$SSL_CERT" ] && [ -f "$SSL_KEY" ]; then
    echo "SSL certificates found, using existing certificates"
    exit 0
fi

echo "Generating self-signed SSL certificate..."
mkdir -p /etc/nginx/ssl

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout "$SSL_KEY" \
    -out "$SSL_CERT" \
    -subj "/C=US/ST=State/L=City/O=Kumpe3D/CN=${SSL_CN:-localhost}" \
    2>/dev/null

if [ $? -eq 0 ]; then
    echo "Self-signed SSL certificate generated successfully"
else
    echo "Error generating SSL certificate"
    exit 1
fi

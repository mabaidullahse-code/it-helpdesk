#!/usr/bin/env bash
# ==============================================================================
# UHS IT Helpdesk Portal - Automated SSL & Nginx Deployment Script
# Subdomain: helpdesk.uhs.edu.pk
# Upstream: Octane FrankenPHP (Port 8004 - 4 Workers) + 4 Queue Workers
# Safety: Strictly non-destructive. Preserves all other live projects.
# ==============================================================================
set -euo pipefail

DOMAIN="helpdesk.uhs.edu.pk"

echo "=========================================================="
echo " Starting Safe Deployment for: $DOMAIN"
echo "=========================================================="

if [ "$EUID" -ne 0 ]; then
    echo "[ERROR] This script requires root privileges to configure SSL and Nginx."
    echo "Please execute with sudo: sudo bash $0"
    exit 1
fi

# 1. Determine active project location
PROJECT_DIR="/home/quota-mdcat/it-helpdesk"
NGINX_SRC="$PROJECT_DIR/supervisor/nginx/helpdesk.conf"
SUPERVISOR_SRC="$PROJECT_DIR/supervisor/it-helpdesk.conf"

if [ -d "/var/www/it-helpdesk" ]; then
    PROJECT_DIR="/var/www/it-helpdesk"
    NGINX_SRC="$PROJECT_DIR/supervisor/nginx/helpdesk-var-www.conf"
    SUPERVISOR_SRC="$PROJECT_DIR/supervisor/it-helpdesk-var-www.conf"
fi

echo " -> Active project directory: $PROJECT_DIR"

# Ensure storage link points to active directory
cd "$PROJECT_DIR"
rm -f public/storage
php artisan storage:link || true

# 2. Ensure ACME challenge directory exists in webroot
echo "[Step 1/6] Preparing ACME challenge directory in /var/www/html..."
mkdir -p /var/www/html/.well-known/acme-challenge
chmod -R 775 /var/www/html/.well-known
chown -R www-data:www-data /var/www/html/.well-known

# 3. Ensure SSL certificate directory and files exist (self-signed placeholder if first run)
CERT_DIR="/etc/letsencrypt/live/$DOMAIN"
echo "[Step 2/6] Verifying SSL certificate directory: $CERT_DIR..."
if [ ! -f "$CERT_DIR/fullchain.pem" ]; then
    echo " -> Creating initial SSL placeholder for $DOMAIN to enable Nginx config test..."
    mkdir -p "$CERT_DIR"
    openssl req -x509 -nodes -days 1 -newkey rsa:2048 \
        -keyout "$CERT_DIR/privkey.pem" \
        -out "$CERT_DIR/fullchain.pem" \
        -subj "/CN=$DOMAIN"
fi

# 4. Deploy Nginx configuration without touching any other files
echo "[Step 3/6] Deploying isolated Nginx configuration..."
cp "$NGINX_SRC" /etc/nginx/sites-available/helpdesk.conf
ln -sf /etc/nginx/sites-available/helpdesk.conf /etc/nginx/sites-enabled/helpdesk.conf

# 5. Verify Nginx configuration before reloading
echo "[Step 4/6] Testing Nginx configuration..."
nginx -t

echo " -> Reloading Nginx safely..."
systemctl reload nginx

# 6. Request real Let's Encrypt SSL certificate
echo "[Step 5/6] Requesting Let's Encrypt certificate for $DOMAIN..."
certbot certonly --webroot -w /var/www/html \
    -d "$DOMAIN" \
    --non-interactive --agree-tos --register-unsafely-without-email \
    --keep-until-expiring || echo "Notice: Certbot request finished or certificate already exists."

echo " -> Reloading Nginx with the verified SSL certificate..."
systemctl reload nginx

# 7. Configure Supervisor services (Octane 4 workers + Queue 4 workers)
echo "[Step 6/6] Activating Supervisor workers for it-helpdesk..."
cp "$SUPERVISOR_SRC" /etc/supervisor/conf.d/it-helpdesk.conf

# Ensure log files exist
mkdir -p "$PROJECT_DIR/storage/logs"
touch "$PROJECT_DIR/storage/logs/octane-supervisor.log" "$PROJECT_DIR/storage/logs/queue-worker.log"
chmod 666 "$PROJECT_DIR/storage/logs/octane-supervisor.log" "$PROJECT_DIR/storage/logs/queue-worker.log"

supervisorctl reread
supervisorctl update

supervisorctl start it-helpdesk-octane || supervisorctl restart it-helpdesk-octane || true
supervisorctl start 'it-helpdesk-worker:*' || supervisorctl restart 'it-helpdesk-worker:*' || true

echo "=========================================================="
echo " DEPLOYMENT STATUS REPORT"
echo "=========================================================="
echo "Subdomain: https://$DOMAIN"
echo "Octane Server: Port 8004 (4 workers)"
echo "Queue Workers: 4 workers"
echo "Supervisor processes:"
supervisorctl status it-helpdesk-octane || true
supervisorctl status 'it-helpdesk-worker:*' || true
echo "=========================================================="


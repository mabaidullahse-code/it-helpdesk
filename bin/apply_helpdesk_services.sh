#!/usr/bin/env bash
set -euo pipefail

echo "=========================================================="
echo " IT Helpdesk - Supervised Services Deployment"
echo " (FrankenPHP Octane: 4 workers | Queue: 4 workers)"
echo "=========================================================="

if [ "$EUID" -ne 0 ]; then
    echo "ERROR: This script requires root privileges to configure supervisor."
    echo "Please execute with sudo:"
    echo "  sudo bash $0"
    exit 1
fi

PROJECT_DIR="/home/quota-mdcat/it-helpdesk"
if [ ! -d "$PROJECT_DIR" ]; then
    PROJECT_DIR="/var/www/it-helpdesk"
fi

CONFIG_FILE="$PROJECT_DIR/supervisor/it-helpdesk.conf"
if [ "$PROJECT_DIR" = "/var/www/it-helpdesk" ]; then
    CONFIG_FILE="$PROJECT_DIR/supervisor/it-helpdesk-var-www.conf"
fi

echo "Deploying Supervisor configuration from: $CONFIG_FILE"
cp "$CONFIG_FILE" /etc/supervisor/conf.d/it-helpdesk.conf

# Ensure log files exist and have correct permissions
touch "$PROJECT_DIR/storage/logs/octane-supervisor.log"
touch "$PROJECT_DIR/storage/logs/queue-worker.log"
chmod 666 "$PROJECT_DIR/storage/logs/octane-supervisor.log" "$PROJECT_DIR/storage/logs/queue-worker.log"

echo "Updating Supervisor..."
supervisorctl reread
supervisorctl update

echo "Starting it-helpdesk services..."
supervisorctl start it-helpdesk-octane || supervisorctl restart it-helpdesk-octane || true
supervisorctl start 'it-helpdesk-worker:*' || supervisorctl restart 'it-helpdesk-worker:*' || true

echo "Current status of it-helpdesk services:"
supervisorctl status it-helpdesk-octane || true
supervisorctl status 'it-helpdesk-worker:*' || true

echo "=========================================================="
echo "Deployment complete."
echo "=========================================================="


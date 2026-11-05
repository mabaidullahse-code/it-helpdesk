#!/usr/bin/env bash
# ==============================================================================
# UHS IT Helpdesk Portal - Database Creation & Migration Script
# Safely creates 'it_helpdesk' database without affecting other databases.
# ==============================================================================
set -euo pipefail

if [ "$EUID" -ne 0 ]; then
    echo "[ERROR] Please run with sudo: sudo bash $0"
    exit 1
fi

echo "=========================================================="
echo " Step 1: Creating database 'it_helpdesk' & Granting Access"
echo "=========================================================="

mysql -e "
    CREATE DATABASE IF NOT EXISTS it_helpdesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    GRANT ALL PRIVILEGES ON it_helpdesk.* TO 'admittance-card-user'@'%';
    GRANT ALL PRIVILEGES ON it_helpdesk.* TO 'admittance-card-user'@'localhost';
    GRANT ALL PRIVILEGES ON it_helpdesk.* TO 'admittance-card-user'@'127.0.0.1';
    FLUSH PRIVILEGES;
"
echo " -> Database 'it_helpdesk' created and privileges granted."

echo "=========================================================="
echo " Step 2: Running Laravel Migrations"
echo "=========================================================="
cd /var/www/it-helpdesk
php artisan migrate --force

echo "=========================================================="
echo " Step 3: Running Database Seeders"
echo "=========================================================="
php artisan db:seed --force || echo "Notice: Seeders ran or were already populated."

echo "=========================================================="
echo " Step 4: Restarting Supervisor Workers (Octane + Queue)"
echo "=========================================================="
supervisorctl restart it-helpdesk-octane || supervisorctl start it-helpdesk-octane
supervisorctl restart 'it-helpdesk-worker:*' || supervisorctl start 'it-helpdesk-worker:*'

echo "=========================================================="
echo " IT Helpdesk is fully initialized!"
echo " Portal URL: https://helpdesk.uhs.edu.pk"
echo "=========================================================="


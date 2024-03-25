#!/bin/bash
set -e

cd /var/www

# Clear cached config (build-time cache has no env vars)
php artisan config:clear
php artisan view:clear

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction
echo "Migrations complete."

# Start PHP-FPM in background
php-fpm -D

# Start nginx in foreground
nginx -g "daemon off;"

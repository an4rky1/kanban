#!/bin/bash
set -e

cd /var/www

# Run migrations if DB is configured
if [ -n "$DB_URL" ] || [ -n "$DB_HOST" ]; then
    php artisan migrate --force --no-interaction 2>/dev/null || true
fi

# Start PHP-FPM in background
php-fpm -D

# Start nginx in foreground
nginx -g "daemon off;"

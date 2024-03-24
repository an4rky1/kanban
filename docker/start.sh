#!/bin/bash
set -e

cd /var/www

# Run migrations if DB is configured
if [ -n "$DB_URL" ] || [ -n "$DB_HOST" ]; then
    echo "Running migrations..."
    php artisan migrate --force --no-interaction
    echo "Migrations complete."
fi

# Start PHP-FPM in background
php-fpm -D

# Start nginx in foreground
nginx -g "daemon off;"

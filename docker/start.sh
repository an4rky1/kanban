#!/bin/bash
cd /var/www

# Clear caches from build
php artisan config:clear
php artisan view:clear

# Check critical env vars
if [ -z "$APP_KEY" ]; then
  echo "!!! ERROR: APP_KEY is missing. Add it to Render Environment variables."
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction || echo "Migration skipped/failed"

# Seed database (creates default board)
echo "Seeding database..."
php artisan db:seed --force || echo "Seeding skipped/failed"

# Start PHP-FPM
php-fpm -D

# Start Nginx
nginx -g "daemon off;"

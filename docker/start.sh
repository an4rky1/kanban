#!/bin/bash
cd /var/www

# Clear caches
php artisan config:clear
php artisan view:clear

# Fix permissions (ensure www-data owns storage)
chown -R www-data:www-data storage bootstrap/cache

# Check APP_KEY
if [ -z "$APP_KEY" ]; then
  echo "!!! ERROR: APP_KEY is missing. Add it to Render Environment variables."
fi

# Migrate
echo "=== Running Migrations ==="
php artisan migrate --force --no-interaction
echo "=== Migrations Done ==="

# Seed
echo "=== Seeding Database ==="
php artisan db:seed --force
echo "=== Seeding Done ==="

# Check Data
echo "=== Checking Data ==="
php artisan tinker --execute="echo 'Users: ' . \App\Models\User::count() . ', Boards: ' . \App\Models\Board::count();"
echo "=== Data Check Done ==="

# Start
php-fpm -D
nginx -g "daemon off;"

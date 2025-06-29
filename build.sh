#!/usr/bin/env bash

# Install dependencies
composer install --optimize-autoloader --no-dev

# Set proper permissions
chown -R www-data:www-data /var/www/html
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/uploads # if you have uploads directory

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
  php artisan key:generate
fi

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
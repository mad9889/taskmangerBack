#!/usr/bin/env bash

# Install composer dependencies
composer install --optimize-autoloader --no-dev

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
  php artisan key:generate
fi

# Set proper permissions
chmod -R 775 storage bootstrap/cache

# Optimize the application
php artisan optimize
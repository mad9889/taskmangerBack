#!/usr/bin/env bash
echo "Running composer"
composer global require hirak/prestissimo
composer install --optimize-autoloader --no-dev

echo "generating application key..."
php artisan key:generate --show

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force
echo "Running queue"
php artisan queue:work --tries=3 --timeout=90 &

chmod -R 775 storage bootstrap/cache
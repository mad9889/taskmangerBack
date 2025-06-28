#!/usr/bin/env bash
# exit on error
set -o errexit

composer install --no-dev
php artisan key:generate --force
php artisan storage:link
php artisan migrate --force
#!/usr/bin/env bash
# exit on error
set -o errexit

php artisan key:generate --force
php artisan storage:link
php artisan migrate --force
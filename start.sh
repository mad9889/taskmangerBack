#!/usr/bin/env bash

# Create PHP-FPM socket directory
mkdir -p /var/run/php

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"
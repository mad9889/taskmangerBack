#!/usr/bin/env bash

# Create PHP-FPM socket directory
mkdir -p /var/run/php

# Start PHP-FPM with explicit configuration
php-fpm --fpm-config /etc/php-fpm.conf -D

# Wait briefly to ensure PHP-FPM is ready
sleep 2

# Start Nginx in foreground
nginx -g "daemon off;"
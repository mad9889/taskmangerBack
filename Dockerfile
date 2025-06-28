# Stage 1: Build dependencies with Composer
FROM composer:2 as builder
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

# Stage 2: Set up production environment
FROM php:8.2-apache
WORKDIR /var/www/html

# Copy built dependencies
COPY --from=builder /app .

# Copy .env file (if exists)
COPY .env.production .env

# Apache configuration
COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache modules
RUN a2enmod rewrite

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Generate key only if .env doesn't exist
RUN if [ ! -f .env ]; then \
        cp .env.example .env && \
        php artisan key:generate --force; \
    fi

# Optimize
RUN php artisan optimize:clear
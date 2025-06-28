# Stage 1: Build dependencies with Composer
FROM composer:2 as builder
WORKDIR /app

# First copy only files needed for composer install
COPY composer.json composer.lock ./

# Install dependencies without .env file first
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Copy remaining files
COPY . .

# Stage 2: Set up production environment
FROM php:8.2-apache
WORKDIR /var/www/html

# Copy built dependencies
COPY --from=builder /app .

# Apache configuration
COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache modules and set permissions
RUN a2enmod rewrite && \
    chown -R www-data:www-data storage bootstrap/cache

# Create .env file from environment variables
RUN touch .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=${APP_URL:-http://localhost}" >> .env && \
    php artisan key:generate --force && \
    php artisan optimize:clear
# Stage 1: Build dependencies with Composer
FROM composer:2 as builder
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

# Stage 2: Set up the production environment
FROM php:8.2-apache
WORKDIR /var/www/html

# Copy built dependencies from builder
COPY --from=builder /app .

# Apache configuration
COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache modules
RUN a2enmod rewrite

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache

# Generate application key and optimize
RUN php artisan key:generate --force
RUN php artisan optimize:clear
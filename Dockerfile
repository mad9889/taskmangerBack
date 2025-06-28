# Stage 1: Build dependencies with Composer
FROM composer:2 as builder
WORKDIR /app
COPY . .
# Copy only the .env.example file for the build stage
COPY .env.example /app/.env
RUN composer install --no-dev --optimize-autoloader

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

# Environment setup (modified to be more robust)
RUN if [ ! -f .env ]; then \
        cp .env.example .env && \
        sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env && \
        sed -i 's/APP_ENV=.*/APP_ENV=production/' .env; \
    fi && \
    php artisan key:generate --force && \
    php artisan optimize:clear
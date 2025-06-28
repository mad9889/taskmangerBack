# Stage 1: Build dependencies with Composer
FROM composer:2 as builder
WORKDIR /app

# Install system dependencies first
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Copy only files needed for composer
COPY composer.json composer.lock ./

# Temporary .env for package discovery
RUN echo "APP_KEY=tempkeyfordiscovery" > .env && \
    composer install --no-dev --no-interaction --optimize-autoloader && \
    rm .env

# Copy remaining files
COPY . .

# Stage 2: Set up production environment
FROM php:8.2-apache
WORKDIR /var/www/html

# Install PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    gd \
    xml \
    && rm -rf /var/lib/apt/lists/*

# Copy built dependencies
COPY --from=builder /app .

# Apache configuration
COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Set permissions and optimize
RUN chown -R www-data:www-data storage bootstrap/cache && \
    echo "APP_ENV=production" > .env && \
    echo "APP_DEBUG=false" >> .env && \
    php artisan key:generate --force && \
    php artisan optimize:clear
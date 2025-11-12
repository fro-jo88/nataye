# syntax=docker/dockerfile:1

# Build stage: install PHP extensions and Composer
FROM php:8.2-cli-bullseye AS base

# Install system deps
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libsqlite3-dev \
        libonig-dev \
        libicu-dev \
        libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions commonly needed by Laravel
RUN docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        bcmath \
        exif \
        pcntl \
        gd \
        zip \
        mbstring \
        intl \
        xml

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set workdir
WORKDIR /app

# Copy composer files and install (faster layer caching)
COPY composer.json composer.lock* ./
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader --no-scripts || true

# Copy application code
COPY . .

# Ensure vendor is fully installed after copy
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader --no-scripts

# Optimize Laravel (package discovery will occur at runtime when env is fully available)
RUN php artisan config:clear || true \
 && php artisan route:clear || true \
 && php artisan view:clear || true

# Expose the port used by Render/Koyeb/etc
EXPOSE 8000

# Default environment vars (can be overridden by the platform)
ENV APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/tmp/database.sqlite

# Start script: create sqlite file, run migrations, then serve
CMD ["sh", "-lc", "touch /tmp/database.sqlite && php artisan migrate --force && php artisan serve --host 0.0.0.0 --port ${PORT:-8000}"]

FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpng-dev libonig-dev libxml2-dev \
    libzip-dev libicu-dev

# PHP extensions required by Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    intl \
    zip

# Enable Apache rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project
COPY . .

# IMPORTANT ✅ create env before composer
RUN cp .env.example .env || true

# Avoid Laravel artisan crash
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# Install packages WITHOUT scripts
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-scripts

# Generate key after install
RUN php artisan key:generate || true

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
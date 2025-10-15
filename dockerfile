# Use official PHP image with Composer preinstalled
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer globally
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy app files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy default nginx config
COPY .render/nginx.conf /etc/nginx/conf.d/default.conf

# Expose port
EXPOSE 10000

# Start both Nginx and PHP-FPM
CMD service nginx start && php-fpm

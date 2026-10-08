# ==============================================================================
# STAGE 1: Build Frontend Assets (Vite, Tailwind CSS v4, Alpine.js)
# ==============================================================================
FROM node:22-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci || npm install

COPY . .
RUN npm run build

# ==============================================================================
# STAGE 2: PHP 8.3 FPM + Nginx Application Server
# ==============================================================================
FROM php:8.3-fpm-alpine

# Install system dependencies & Nginx & Supervisord
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    curl \
    git \
    unzip \
    libzip-dev \
    icu-dev \
    sqlite-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_sqlite \
        pdo_mysql \
        bcmath \
        intl \
        zip \
        opcache \
        gd \
        mbstring

# Copy Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer manifest files first to leverage Docker layer caching
COPY composer.json composer.lock ./

# Install production PHP dependencies (ignoring scripts until application code is copied)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copy application source code
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build /var/www/html/public/build

# Finish composer autoload generation
RUN composer dump-autoload --optimize --no-dev

# Setup custom PHP configurations
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'memory_limit=256M'; \
        echo 'upload_max_filesize=64M'; \
        echo 'post_max_size=64M'; \
    } > /usr/local/etc/php/conf.d/custom.ini

# Setup Nginx configuration to listen on port 8005
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Setup Supervisord configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Setup Entrypoint script
COPY docker/entrypoint.sh /entrypoint.sh
RUN tr -d '\r' < /entrypoint.sh > /entrypoint_unix.sh && mv /entrypoint_unix.sh /entrypoint.sh \
    && chmod +x /entrypoint.sh

# Ensure storage, cache, and database directories exist with correct permissions
RUN mkdir -p /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/framework/cache \
             /var/www/html/storage/logs \
             /var/www/html/bootstrap/cache \
             /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Expose port 8005
EXPOSE 8005

ENTRYPOINT ["/entrypoint.sh"]

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

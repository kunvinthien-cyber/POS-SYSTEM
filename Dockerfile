FROM node:22-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

FROM php:8.4-cli-alpine

# TiDB Cloud validates TLS connections against the system CA bundle installed below.
ENV DB_SSL_CA=/etc/ssl/cert.pem

# Install system dependencies
RUN apk add --no-cache \
    ca-certificates \
    icu-dev \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev

# Configure & Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        dom \
        intl \
        zip \
        bcmath \
        gd \
        mbstring \
        opcache

# Get Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN rm -f bootstrap/cache/*.php

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && mkdir -p \
        storage/framework/views \
        storage/framework/cache \
        storage/framework/sessions \
        storage/logs \
        bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Expose Port
EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

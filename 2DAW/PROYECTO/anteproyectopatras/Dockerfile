# Stage 1: Dependencias PHP (Vendor)
FROM php:8.2-fpm-alpine as vendor-builder
WORKDIR /app

RUN apk add --no-cache git unzip libzip-dev
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.* ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# Stage 2: Construcción de Assets (Frontend)
FROM node:20-alpine as frontend-builder
WORKDIR /app

COPY package*.json ./
RUN npm ci --legacy-peer-deps

COPY . .
# Copiamos vendor desde la etapa anterior para que Ziggy esté disponible para Vite
COPY --from=vendor-builder /app/vendor /app/vendor
RUN npm run build

# Stage 3: Aplicación Final
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    unzip \
    git \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    nginx

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring exif pcntl bcmath xml intl zip

WORKDIR /var/www/html
COPY . .
COPY --from=vendor-builder /app/vendor ./vendor
COPY --from=frontend-builder /app/public/build ./public/build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
COPY ./docker/nginx/app.conf /etc/nginx/http.d/default.conf

EXPOSE 80

CMD php artisan config:cache && php artisan route:cache && php artisan view:cache && (php-fpm & nginx -g "daemon off;")
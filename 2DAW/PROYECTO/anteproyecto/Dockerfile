# Stage 1: Construcción de Assets (Frontend)
FROM node:20-alpine as frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Aplicación PHP + Nginx (Backend)
FROM php:8.2-fpm-alpine

# Instalación de dependencias del sistema y extensiones de PHP necesarias para Laravel
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

# Instalación de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .
# Copiar assets ya construidos desde la primera etapa
COPY --from=frontend-builder /app/public/build ./public/build

# Instalación de dependencias de Composer para producción
RUN composer install --no-dev --optimize-autoloader

# Configuración de permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configuración de Nginx
COPY ./docker/nginx/app.conf /etc/nginx/http.d/default.conf

# Exponer el puerto 80
EXPOSE 80

# Script de inicio para correr PHP-FPM y Nginx simultáneamente
CMD php artisan config:cache && php artisan route:cache && php artisan view:cache && (php-fpm & nginx -g "daemon off;")

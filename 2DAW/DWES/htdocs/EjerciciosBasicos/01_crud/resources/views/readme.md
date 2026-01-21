# Historial de Comandos del Proyecto CRUD Laravel + Filament

Este documento contiene la lista cronológica de comandos utilizados para configurar este proyecto, desde su creación hasta la generación del volcado de base de datos.

## 1. Creación del Proyecto
```bash
# Crear el proyecto Laravel 11 (usando la versión actual disponible)
composer create-project laravel/laravel .
```

## 2. Instalación de Filament
```bash
# Instalación de Filament Admin Panel (con --ignore-platform-reqs por la extensión intl inicial)
composer require filament/filament:"^3.2" -W --ignore-platform-reqs

# Instalación del panel de administración
php artisan filament:install --panels
```

## 3. Modelos y Base de Datos
```bash
# Crear modelo Category con migración
php artisan make:model Category -m

# Crear modelo Post con migración
php artisan make:model Post -m

# Ejecutar las migraciones para crear las tablas en SQLite
php artisan migrate
```

## 4. Datos de Prueba (Seeders)
```bash
# Editar database/seeders/DatabaseSeeder.php y luego ejecutar:
php artisan db:seed
```

## 5. Generación de Recursos Filament
```bash
# Generar el recurso para Categorías
php artisan make:filament-resource Category

# Generar el recurso para Posts
php artisan make:filament-resource Post
```

## 6. Servidor y Verificación
```bash
# Iniciar el servidor local
php artisan serve
```

## 7. Mantenimiento y Fixes
```bash
# Verificar extensiones de PHP cargadas
php -m

# Limpiar caché de configuración (opcional durante el proceso)
php artisan config:clear
```

## 8. Generación del Volcado SQL
Debido a que `sqlite3` no estaba disponible directamente en el terminal, se utilizó un script puente de PHP (`dump_db.php`) para generar el archivo:
```bash
# Ejecutar el script de volcado personalizado
php dump_db.php
```

---
*Nota: Se habilitó manualmente la extensión `extension=intl` en el archivo `php.ini` de XAMPP para permitir el funcionamiento de los formateadores de Filament.*

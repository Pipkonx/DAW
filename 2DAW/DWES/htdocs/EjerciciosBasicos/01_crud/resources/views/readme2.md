# Guía: Cómo agregar Filament a un Proyecto Laravel Existente

Si ya tienes un proyecto Laravel con una base de datos configurada, sigue estos pasos para integrar Filament y crear un panel de administración.

## 1. Requisitos Previos
Asegúrate de que tu entorno cumple con los requisitos de Filament:
- PHP 8.1 o superior.
- Extensión `intl` habilitada en tu `php.ini`.

## 2. Instalación de Filament
Ejecuta el siguiente comando para añadir Filament a tu proyecto:
```bash
composer require filament/filament:"^3.2" -W
```

## 3. Configurar el Panel de Administración
Instala el panel predeterminado (normalmente llamado "admin"):
```bash
php artisan filament:install --panels
```
*Esto creará un proveedor de servicios en `app/Providers/Filament/AdminPanelProvider.php`.*

## 4. Crear un Usuario Administrador
Para poder acceder al panel (`/admin`), necesitas un usuario en la tabla `users`. Puedes crearlo rápidamente con:
```bash
php artisan make:filament-user
```
*Sigue las instrucciones en el terminal para el nombre, email y contraseña.*

## 5. Generar Recursos CRUD
Si ya tienes modelos (por ejemplo, `Product`, `Order`, etc.), puedes generar la interfaz de administración automáticamente:

### Generar Recurso Básico
```bash
php artisan make:filament-resource NombreDelModelo
```
*Ejemplo: `php artisan make:filament-resource Product`*

### Generar con Formulario y Tabla Automática
Si quieres que Filament intente adivinar los campos basándose en tus columnas de la base de datos:
```bash
php artisan make:filament-resource NombreDelModelo --generate
```

## 6. Personalización
Los archivos generados se encuentran en `app/Filament/Resources/`.
- **Formulario**: Edita el método `form()` en `TuModeloResource.php` para añadir validaciones y tipos de input.
- **Tabla**: Edita el método `table()` para configurar columnas, filtros y acciones.

## 7. Acceso
Inicia tu servidor:
```bash
php artisan serve
```
Y accede a: `http://localhost:8000/admin`

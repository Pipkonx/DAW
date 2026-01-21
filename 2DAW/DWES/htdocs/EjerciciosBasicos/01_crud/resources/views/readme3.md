# Guía: Eloquent + Filament (Sin Migraciones)

Si prefieres trabajar con una base de datos ya existente o importar un archivo SQL manualmente en lugar de usar las migraciones de Laravel, este es el proceso:

### 1. Preparar la Base de Datos
Importa tu archivo `.sql` directamente en tu gestor (phpMyAdmin, TablePlus, línea de comandos, etc.). No necesitas ejecutar `php artisan migrate`.

### 2. Configurar la Conexión
Asegúrate de que tu archivo `.env` apunte a esa base de datos:
```env
DB_DATABASE=tu_base_de_datos
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Crear el Modelo Eloquent
Crea el modelo normalmente. Como no vas a usar migraciones, puedes omitir el flag `-m`.
```bash
php artisan make:model Post
```

**Importante**: Si el nombre de tu tabla en el SQL no sigue el estándar de Laravel (ej: la tabla se llama `mis_posts` en lugar de `posts`), debes indicarlo en el modelo `app/Models/Post.php`:
```php
protected $table = 'mis_posts';
```

### 4. Crear el Recurso de Filament
Una vez que la tabla existe en la base de datos y el modelo está creado, genera el recurso:
```bash
php artisan make:filament-resource Post --generate
```
*El flag `--generate` leerá las columnas directamente de la tabla que importaste manualmente.*

### Resumen de Componentes:
- **Modelo (Eloquent)**: `app/Models/Post.php` -> Gestiona la lógica y conexión a la tabla.
- **Controlador/Interfaz (Filament)**: `app/Filament/Resources/PostResource.php` -> Gestiona el panel administrativo.
- **Vistas**: Filament las genera dinámicamente, no necesitas archivos `.blade.php` para el CRUD.

### Solución al error de Tipo ($navigationIcon)
Si ves un error fatal sobre `$navigationIcon`, asegúrate de que la definición en tu Resource sea exactamente así:
`protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';`
Esto es necesario porque Filament v3 usa tipos de unión de PHP 8.

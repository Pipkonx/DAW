Pasar de breeze a laravel ui porque es más sencillo


Examen santi

Procesar un formulario validando las entradas, mostrando los errores, hacer consultas en una tabla (crud), usar las relaciones que nos trae laravel

relaciones
validacion
controlador

nos dara dos tablas con datos relacionadas y tendremos que mostrar la lista , paginarla , filtrarla,

nos dara el modelo de workbench , el esquema de proyecto y nos pondra la imagen

crud usando todas las funciones de laravel y sus relaciones, nos da la base de datos


# 🚀 GUÍA DE SUPERVIVENCIA: EXAMEN LARAVEL + FILAMENT (0 A 100)

Sigue este orden exacto para completar el examen en tiempo récord.

---

## 1. COMPROBACIONES INICIALES
Asegúrate de que el proyecto base es estable.
```bash
# Verificar conexión y tablas de la BD
php artisan migrate

# Ver rutas existentes (por si ya hay algo creado)
php artisan route:list

# Iniciar servidor
php artisan serve

## 2. Instalacion y acceso (filament)

# 1. Instalar core y dependencias
composer require filament/filament:"^3.2" -W

# 2. Instalar el Panel (Enter a todo)
php artisan filament:install --panels

# 3. Crear usuario para el examen (User/Pass)
php artisan make:filament-user

## 3. Configurracion de relaciones (Models)

# // Ejemplo: Producto pertenece a una Categoría
# public function categoria(): BelongsTo {
#     return $this->belongsTo(Categoria::class);
# }

# // Ejemplo: Producto tiene muchas Etiquetas (Muchos a Muchos)
# public function tags(): BelongsToMany {
#     return $this->belongsToMany(Tag::class);
# }

## 4. Generacion del crud automatico

# Sustituye "Producto" por el nombre de tu modelo
php artisan make:filament-resource Producto --generate

## 5. Requisitos de examen

Edita el archivo generado en app/Filament/Resources/ProductoResource.php:
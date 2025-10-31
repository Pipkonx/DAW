# Proyecto Mio — CRUD de Usuarios (PHP + PDO)

Trabajando con tabla en mySQL y php para gestionarlo

## Requisitos

- PHP 7.4 o superior (con extensión PDO habilitada)
- Servidor web (Apache/XAMPP) o servidor embebido de PHP
- MySQL/MariaDB

## Estructura

```
proyectoMio/
├── DB/
│   └── conexion.php          # Conexión PDO a MySQL
├── controlador/
│   └── Controlador.php       # Controlador principal de usuarios
├── modelo/
│   └── Usuario.php           # Modelo Usuario (CRUD con PDO)
├── vista/
│   ├── usuario_alta.php      # Formulario de alta
│   ├── usuario_borrar.php    # Confirmación de borrado
│   ├── usuario_lista.php     # Listado con acciones
│   └── usuario_modificar.php # Formulario de edición
└── index.php                 # Enrutador y bootstrap del proyecto
```

## Instalación

1. Configura tu conexión en `DB/conexion.php`:

```php
$user = "root";
$pass = "";     // Ajusta tu contraseña
$host = "localhost";
$dbname = "proyecto"; // Asegúrate de que exista esta BD
```

2. Crea la base de datos (si no existe) desde tu gestor (phpMyAdmin/CLI) con:

```sql
CREATE DATABASE IF NOT EXISTS usuarios CHARACTER SET utf8 COLLATE utf8_general_ci;
```

3. La tabla `usuarios` se crea automáticamente al cargar `index.php` (ver SQL más abajo).

## Ejecutar

- Con Apache/XAMPP: coloca el proyecto en `htdocs` y abre `http://localhost/proyectoMio/`.
- Con el servidor embebido de PHP (si tienes PHP en el PATH):

Luego abre `http://localhost:8000/`.

## Uso

- `?action=listar` muestra el listado de usuarios.
- `?action=alta` muestra el formulario para crear.
- `?action=modificar&id=ID` edita el usuario con ese `ID`.
- `?action=borrar&id=ID` confirma y elimina el usuario.

El menú básico está en `index.php` y las opciones de modificar/borrar aparecen en el listado.

## SQL de la tabla `usuarios`

`index.php` crea la tabla si no existe:

```sql
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    nif VARCHAR(9) NOT NULL UNIQUE,
    cp VARCHAR(10) NOT NULL,
    fecha_alta TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

```
## y ahora faltaría crear la tabla `tareas`

y procedemos a crear la tabla

```sql
CREATE TABLE `proyecto`.`tareas` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `nombreTarea` VARCHAR(30) NOT NULL ,
    `descripcion` VARCHAR(100) NOT NULL ,
    `estado` CHAR(1) NOT NULL ,
    `fecha_tarea` TIMESTAMP NOT NULL ,
    `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `anotaciones_anteriores` VARCHAR(250) NOT NULL ,
    `anotaciones_posteriores` VARCHAR(250) NOT NULL ,
    `operario_encargado` VARCHAR(50) NOT NULL ,
      PRIMARY KEY (`id`)) ENGINE = InnoDB; 
    ```
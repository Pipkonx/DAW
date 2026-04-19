# Guía de Instalación - Nosecaen S.L. (Problema 1)

### Pasos para poner en marcha la aplicación:

### 1. Requisitos previos
Asegúrate de tener instalados:
- PHP 8.2 o superior
- Composer (el gestor de paquetes de PHP)
- MySQL o MariaDB (la base de datos)
- Node.js y NPM (para la parte visual)

### 2. Configuración del entorno
1. Entra en esta carpeta (`Problema 1`) desde tu terminal.
2. Copia el archivo `.env.example` y cámbiale el nombre a `.env`.
3. Abre el archivo `.env` y configura los datos de tu base de datos:
   - `DB_DATABASE=nombre_de_tu_bd`
   - `DB_USERNAME=tu_usuario`
   - `DB_PASSWORD=tu_contraseña`

### 3. Instalación de dependencias
Ejecuta estos comandos en orden:
```bash
# Instala las librerías de PHP
composer install

# Genera la "llave" de seguridad de la aplicación
php artisan key:generate

# Instala las librerías de la parte visual (CSS/JS)
npm install
```

### 4. Preparación de la Base de Datos
Tienes dos opciones:
- **Opción A (Recomendada)**: Ejecuta las migraciones de Laravel para crear las tablas automáticamente:
  ```bash
  php artisan migrate --seed
  ```
- **Opción B**: Importar el archivo `bd.sql` que encontrarás en la raíz de esta carpeta usando phpMyAdmin o tu herramienta favorita.

### 5. Ejecución
Para ver la aplicación funcionando, abre dos terminales y ejecuta:
- **Terminal 1**: `php artisan serve` (levanta el servidor PHP)
- **Terminal 2**: `npm run dev` (compila la parte visual en tiempo real)

¡Y listo! Ya puedes entrar en `http://localhost:8000`.

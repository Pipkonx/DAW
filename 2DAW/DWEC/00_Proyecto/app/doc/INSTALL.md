# Instalación y ejecución

Este proyecto implementa una aplicación MVC para finanzas personales usando **PHP + MySQL** con frontend en **JavaScript** y gráficos con **Chart.js**.

## Requisitos
- `PHP 8+`
- `MySQL 5.7+`
- Servidor local: XAMPP, WAMP, Laragon o `php -S` (si tienes PHP instalado)

## 1. Crear la base de datos

Importa el script `sql/finanzas_db.sql` en tu MySQL:

```sql
-- En MySQL (CLI) o phpMyAdmin
SOURCE path/a/tu/proyecto/sql/finanzas_db.sql;
```

Estructura:
- `usuarios`: id, nombre, email (único), password_hash, fecha_registro
- `finanzas`: id, id_usuario (FK), tipo (ingreso|gasto), monto, descripcion, fecha_registro

## 2. Configurar la conexión

Edita `config/database.php` si necesitas cambiar credenciales (por defecto `localhost`, DB `finanzas_db`, usuario `root`, password vacío). También puedes usar variables de entorno:

```bash
setx DB_HOST localhost
setx DB_NAME finanzas_db
setx DB_USER root
setx DB_PASSWORD ""
```

## 3. Colocar el proyecto en el servidor

Opción A (XAMPP):
- Copia la carpeta del proyecto dentro de `C:\xampp\htdocs\finanzas`.
- Accede desde `http://localhost/finanzas/views/registro.php` o `login.php`.

Opción B (PHP embebido):
- En la raíz del proyecto:
  ```bash
  php -S localhost:8000
  ```
- Abre `http://localhost:8000/views/registro.php` o `login.php`.

## 4. Flujo de uso
- Regístrate en `views/registro.php` (se crea usuario y se inicia sesión).
- Accede al `views/dashboard.php` automáticamente tras registro/login.
- Añade ingresos/gastos desde el formulario.
- Consulta movimientos y gráficos (mensual y ahorro vs gastos).

## 5. Seguridad y buenas prácticas
- Contraseñas cifradas con `password_hash()` y verificadas con `password_verify()`.
- CSRF token en formularios y validado en controladores.
- Sesiones para proteger el `dashboard`.
- Validaciones en frontend y backend.

## 6. Estructura de carpetas
- `config/` → `database.php`
- `app/models/` → `Usuario.php`, `Finanzas.php`
- `app/controllers/` → `UsuarioController.php`, `FinanzasController.php`
- `views/` → `login.php`, `registro.php`, `dashboard.php`
- `public/js/` → `main.js`, `graficos.js`
- `sql/` → `finanzas_db.sql`

## 7. Notas
- Si ya tienes un servidor estático (`node dev-server.js`), recuerda que no procesa PHP. Para probar el backend, usa XAMPP/WAMP/Laragon o `php -S`.
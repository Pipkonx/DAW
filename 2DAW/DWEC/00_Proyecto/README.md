# Proyecto Finanzas – DWEC

Aplicación web sencilla para gestionar ingresos y gastos con PHP (MVC ligero), MySQL y un frontend en HTML/CSS/JS. Incluye registro/inicio de sesión, panel con historial de movimientos y gráficos (Chart.js).

## Requisitos

- `PHP` 8.0+ con PDO MySQL habilitado
- `MySQL`/`MariaDB` 10.4+ (o compatible)
- Servidor web que sirva `index.php` en la raíz del proyecto (Apache, Nginx o servidor embebido de PHP)

## Instalación

1. Clona o copia el proyecto en tu servidor web.
2. Crea la base de datos e importa el esquema recomendado:
   - Recomendado: `app/sql/finanzas_db.sql` (tablas `usuarios` y `finanzas`).
   - Alternativo (solo para pruebas): `database/init.sql` (crea `usuarios` y `movimientos`). El código del modelo `Usuario` detecta la columna de contraseña (`password_hash` vs `password`), pero el modelo de finanzas espera la tabla `finanzas`, por lo que se recomienda el esquema de `finanzas_db.sql`.
3. Configura las variables de entorno de conexión a la base de datos (opcional; tienen valores por defecto):
   - `DB_HOST` (por defecto `localhost`)
   - `DB_NAME` (por defecto `finanzas_db`)
   - `DB_USER` (por defecto `root`)
   - `DB_PASSWORD` (por defecto vacío)
4. Arranca el servidor:
   - Con PHP embebido: `php -S localhost:8000 -t .` desde la carpeta `00_Proyecto`.
   - Abre `http://localhost:8000/` y te redirigirá a `app/Index.html`.

## Estructura

```
00_Proyecto/
├── index.php                 # Punto de entrada: redirige a login/dashboard
├── app/
│   ├── Index.html            # Página de acceso con enlaces a login y registro
│   ├── config/
│   │   └── database.php      # Conexión PDO y utilidades de sesión/CSRF
│   ├── controllers/
│   │   ├── UsuarioController.php   # Acciones: register, login, logout
│   │   └── FinanzasController.php  # Acciones: add, list, summary, monthly
│   ├── models/
│   │   ├── Usuario.php       # Registro/login y utilidades de usuario
│   │   └── Finanzas.php      # CRUD y agregaciones (mensual/anual)
│   ├── views/
│   │   ├── login.php         # Form de inicio de sesión
│   │   ├── registro.php      # Form de registro
│   │   └── dashboard.php     # Form de movimiento + tabla + gráficos
│   ├── sql/
│   │   └── finanzas_db.sql   # Esquema recomendado para producción/pruebas
│   └── doc/                  # Documentación adicional
├── public/
│   ├── css/styles.css        # Estilos
│   └── js/
│       ├── main.js           # Lógica de forms y fetch hacia controladores
│       └── graficos.js       # Render con Chart.js
└── database/init.sql         # Esquema alternativo de ejemplo
```

## Configuración

- Conexión a BD: definida en `app/config/database.php` usando PDO y UTF-8 (`utf8mb4`). Puede leer variables de entorno `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`.
- Sesión/CSRF: `ensure_session()` genera `$_SESSION['csrf_token']` usado en formularios y verificado en controladores.

## Uso

1. Accede a la raíz del proyecto (`/index.php`). Si no hay sesión, se muestra `app/Index.html` con enlaces a:
   - `views/login.php`: inicio de sesión.
   - `views/registro.php`: alta de usuario.
2. Tras registrarte o iniciar sesión, serás redirigido a `views/dashboard.php`.
3. En el dashboard:
   - Agrega movimientos (`ingreso` o `gasto`) con monto, fecha y descripción.
   - Consulta historial en la tabla.
   - Visualiza gráficos: barras mensuales y tarta (ahorro vs gastos). Requiere Chart.js CDN.

## API (controladores)

- `POST /app/controllers/UsuarioController.php`
  - `action=register`  → campos: `nombre`, `email`, `password`, `csrf_token`
  - `action=login`     → campos: `email`, `password`, `csrf_token`
  - `action=logout`    → campos: `csrf_token`

- `POST /app/controllers/FinanzasController.php` (requiere sesión activa)
  - `action=add`       → `tipo` (`ingreso|gasto`), `monto`, `descripcion?`, `fecha`, `csrf_token`
  - `action=list`      → devuelve `items[]` con fecha/tipo/monto/descripcion
  - `action=summary`   → `anio` (por defecto actual); devuelve `ingresos`, `gastos`, `ahorro`
  - `action=monthly`   → `anio`; devuelve mapa mensual 1..12 con `ingresos`/`gastos`

## Notas

- Esquema de BD: usa `app/sql/finanzas_db.sql` para que el modelo `Finanzas` funcione (tabla `finanzas`). El archivo `database/init.sql` es un ejemplo alternativo con tabla `movimientos` y puede no ser compatible con todas las funcionalidades del dashboard.
- Contraseñas: se almacenan con `password_hash`. El modelo de usuario detecta automáticamente si la columna se llama `password_hash` o `password`.
- Frontend: `public/js/main.js` usa módulos ES y `fetch` para hablar con PHP. Chart.js se carga por CDN en el dashboard.

## Desarrollo

- Servidor embebido PHP: `php -S localhost:8000 -t .`
- Logs y errores: los controladores devuelven JSON y fijan códigos HTTP apropiados (`400`, `401`, `405`, `500`).

## Licencia

Uso académico/educativo. Ajusta según necesidades del curso.
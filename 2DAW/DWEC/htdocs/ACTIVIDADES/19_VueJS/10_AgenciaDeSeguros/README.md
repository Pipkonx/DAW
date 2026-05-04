# Agencia de Seguros Aeterna

Sistema de gestión integral para una agencia de seguros, desarrollado con **Vue.js 3**, **PHP** y **MySQL**.

## 🚀 Características
- Gestión completa de Clientes (Alta, Baja, Modificación y Listado).
*   Control de Pólizas por cliente con estados dinámicos.
*   Gestión de Recibos y Pagos (validación de importes pendientes).
*   Geografía Dinámica: Provincias y Municipios cargados desde la Base de Datos.
*   Interfaz moderna con **Bootstrap 5** y componentes **PrimeVue**.
*   Sistema de autenticación con roles (Administrador/Usuario).

## 🔑 Usuarios Predeterminados
Para acceder al sistema, puede utilizar las siguientes credenciales:

| Usuario | Contraseña | Rol |
| :--- | :--- | :--- |
| **admin** | `123` | Administrador (Acceso total) |
| **user** | `user` | Usuario (Solo lectura/operaciones básicas) |

## 🛠️ Instalación
1. Clone o descargue los archivos en su servidor local (XAMPP/WAMP).
2. Importe el archivo `database.sql` en su gestor de bases de datos (phpMyAdmin).
3. Asegúrese de que la base de datos se llame `agencia_seguros_new` (o cambie el nombre en `db_config.php`).
4. Abra `index.html` a través de su servidor local (ej: `http://localhost/AgenciaDeSeguros/`).

## 📁 Estructura del Proyecto
- `index.html`: Punto de entrada y plantillas de componentes.
- `api.php`: Backend REST que gestiona todas las peticiones SQL.
- `app.js`: Lógica principal de la aplicación Vue.
- `utils/`: Abstracciones de API, Estilos y Helpers.
- `views/`: Componentes Vue para cada sección (Login, Clientes, Pólizas, etc.).
- `database.sql`: Esquema completo y datos de ejemplo.

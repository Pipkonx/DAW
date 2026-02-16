# Gestión de Agencia de Seguros

Este proyecto es una aplicación web para la gestión de una agencia de seguros, desarrollada con **Vue.js 3** (Composition API) y **PrimeVue 3** en el frontend, y **PHP** con **MySQL** en el backend.

## 📋 Características

- **Autenticación de Usuarios**: Sistema de login seguro.
- **Panel de Control (Dashboard)**:
  - Listado de clientes con paginación y ordenación.
  - Búsqueda global de clientes.
  - Indicadores visuales de tipo de cliente (Empresa/Particular).
- **Detalle de Cliente**:
  - Información completa del cliente.
  - Historial de pólizas asociadas.
  - Gestión de pagos fraccionados (añadir/eliminar pagos).
  - Estado de pólizas con código de colores (Cobrada, A cuenta, Liquidada, Anulada, Pre-anulada).
- **Reportes Avanzados**:
  - Filtrado por rango de códigos de cliente.
  - Filtrado por rango de fechas.
  - Filtrado por estado de la póliza.
  - Resultados en tabla interactiva.

## 🛠️ Tecnologías Utilizadas

- **Frontend**:
  - **Vue.js 3.x** (Composition API)
  - **PrimeVue 3.x** (Biblioteca de Componentes UI)
  - **PrimeIcons** (Iconos)
  - **PrimeFlex 2.x** (Sistema de Grid y Utilidades CSS)
- **Backend**:
  - **PHP 7.4+** (API REST simple)
  - **PDO** (Conexión a Base de Datos)
- **Base de Datos**:
  - **MySQL / MariaDB**

## 🚀 Instalación y Configuración

### 1. Base de Datos
1. Crea una base de datos en MySQL llamada `agencia_seguros_new`.
2. Importa el archivo `database.sql` incluido en el proyecto.

### 2. Configuración del Backend
1. Abre el archivo `api.php`.
2. Verifica y ajusta las credenciales de tu base de datos al inicio del archivo:
   ```php
   $host = 'localhost';
   $db   = 'agencia_seguros_new';
   $user = 'root'; // Cambia esto por tu usuario de MySQL
   $pass = '';     // Cambia esto por tu contraseña de MySQL
   ```

### 3. Ejecución
Para ejecutar la aplicación, necesitas un servidor web con soporte para PHP (como Apache en XAMPP, Nginx, o el servidor integrado de PHP).

## 👤 Usuarios de Prueba

El sistema incluye usuarios predefinidos para pruebas:

- **Administrador**:
  - Usuario: `admin`
  - Contraseña: `123`
- **Usuario Estándar**:
  - Usuario: `user`
  - Contraseña: `user`

## 📂 Estructura del Proyecto

- `index.html`: Punto de entrada de la aplicación. Carga las librerías CDN y define la estructura base.
- `app.js`: Contiene toda la lógica de la aplicación Vue 3 (Componentes, Router simulado, Estado reactivo).
- `api.php`: API Backend que maneja las peticiones AJAX (Login, CRUD de datos).
- `styles.css`: Estilos personalizados adicionales.
- `database.sql`: Esquema de la base de datos.

## ⚠️ Notas de Migración (Vue 2 -> Vue 3)

Este proyecto ha sido migrado a Vue 3. Principales cambios:
- Uso de `createApp` y `setup()` (Composition API).
- Registro de componentes PrimeVue mediante `app.component`.
- Actualización de sintaxis de slots (`v-slot` / `#`).
- Uso de PrimeFlex 2 para mantener compatibilidad de clases de utilidad.

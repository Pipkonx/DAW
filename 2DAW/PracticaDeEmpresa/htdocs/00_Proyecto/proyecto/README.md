# Sistema de Gestión de Prácticas en Empresa (FCT)

Este es un sistema integral desarrollado con **Laravel 12** y **Filament PHP 3** para la gestión, seguimiento y evaluación de alumnos en el periodo de Formación en Centros de Trabajo (FCT).

## 🚀 Tecnologías Utilizadas

- **Framework:** Laravel 12.x
- **Panel Administrativo:** Filament PHP 3.x
- **Base de Datos:** MySQL / MariaDB
- **Seguridad:** Spatie Laravel Permission (Roles y Permisos)
- **Generación de Informes:** Barryvdh Laravel DomPDF
- **Exportación de Datos:** Filament Excel
- **Iconos:** Heroicons

## 🛠️ Instalación

Sigue estos pasos para poner en marcha el proyecto en tu entorno local:

1. **Clonar el repositorio:**
   ```bash
   git clone <url-del-repositorio>
   cd proyecto
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Configurar el archivo de entorno:**
   ```bash
   cp .env.example .env
   # Configura tus credenciales de base de datos en el archivo .env
   ```

4. **Generar la clave de la aplicación:**
   ```bash
   php artisan key:generate
   ```

5. **Ejecutar migraciones y seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Instalar dependencias de JS (opcional si usas Vite):**
   ```bash
   npm install && npm run dev
   ```

7. **Iniciar el servidor local:**
   ```bash
   php artisan serve
   ```

## 🔐 Credenciales de Acceso (Demo)

El sistema cuenta con 4 roles predefinidos con diferentes niveles de acceso:

| Rol | Email | Contraseña | Descripción |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@admin.com` | `password` | Control total del sistema y configuración. |
| **Tutor de Curso** | `tutor.curso@ejemplo.com` | `password` | Gestiona alumnos y observa incidencias de su curso. |
| **Tutor de Empresa** | `tutor.empresa@ejemplo.com` | `password` | Evalúa alumnos y registra observaciones en su empresa. |
| **Alumno** | `alumno@ejemplo.com` | `password` | Consulta sus notas, registra incidencias y descarga informes. |

## 🌟 Características Principales

- **Dashboard Dinámico:** Estadísticas y gráficos personalizados según el rol.
- **Evaluación por Competencias:** Sistema de rúbricas con cálculo automático de nota final.
- **Gestión de Incidencias:** Flujo de resolución con notificaciones en tiempo real para tutores.
- **Informes PDF:** Generación automática de informes de seguimiento para alumnos.
- **Seguridad Robusta:** Políticas de acceso (Policies) y Scopes globales para filtrar datos por rol.

---
Desarrollado para el módulo de Proyecto de 2º DAW.

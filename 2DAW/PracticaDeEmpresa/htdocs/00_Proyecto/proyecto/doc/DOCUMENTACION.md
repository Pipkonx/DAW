# 🚀 PROYECTO: SISTEMA DE GESTIÓN DE PRÁCTICAS ACADÉMICAS

## 1. DESCRIPCIÓN GENERAL
El presente proyecto consiste en el desarrollo de una aplicación web avanzada para la gestión integral de prácticas académicas (FCT - Formación en Centros de Trabajo). La plataforma ha sido construida utilizando el framework **Laravel 12**, aprovechando la potencia del ecosistema **Filament v3** para la creación de un panel administrativo robusto, intuitivo y altamente funcional.

![Dashboard Preview](https://via.placeholder.com/800x400?text=Captura+del+Dashboard+Principal)
*Espacio para captura de pantalla del Dashboard con estadísticas por rol.*

El sistema implementa el patrón **MVC (Modelo-Vista-Controlador)**, garantizando una separación clara entre la lógica de negocio, los datos y la interfaz de usuario. La persistencia de datos se gestiona mediante **MySQL 8.0** a través del ORM **Eloquent**, lo que permite una manipulación de la base de datos segura y eficiente.

### 👥 Tipos de Usuarios y Niveles de Acceso
El sistema gestiona cinco perfiles diferenciados mediante roles y permisos:
- **Administrador**: Control total sobre el sistema, gestión de usuarios, roles, cursos y configuración global.
- **Alumno**: Seguimiento de sus propias prácticas, registro de observaciones diarias, incidencias y visualización de evaluaciones.
- **Tutor de Empresa (Tutor Prácticas)**: Supervisión de alumnos asignados en su empresa, registro de interacciones y evaluación del desempeño.
- **Tutor de Centro (Tutor Curso)**: Gestión de alumnos de su grupo, asignación de empresas y seguimiento académico.
- **Empresa**: Gestión de datos corporativos y visualización de alumnos asociados.

## 2. OBJETIVOS DE APRENDIZAJE Y DESARROLLO
- **Implementación de MVC con Filament**: Uso de Filament como capa de abstracción sobre Laravel para acelerar el desarrollo de interfaces CRUD sin perder la estructura MVC.
- **Gestión de Base de Datos Relacional**: Diseño complejo de relaciones (1:1, 1:N, N:M) entre Usuarios, Alumnos, Empresas, Cursos, Evaluaciones y Mensajes.
- **Interfaces de Usuario Intuitivas**: Aplicación de Tailwind CSS y componentes de Filament para una experiencia de usuario (UX) moderna y adaptativa.
- **Desarrollo de Funcionalidades CRUD**: Gestión completa de registros con validaciones avanzadas, subida de archivos y filtros dinámicos.
- **Reportes y Estadísticas**: Generación de gráficos (Widgets) y exportación de datos para la toma de decisiones.
- **Buenas Prácticas**: Implementación de Policies para seguridad, Observers para lógica secundaria y Seeders para entornos de prueba.

## 3. TECNOLOGÍAS UTILIZADAS

| Tecnología | Icono / Badge | Descripción |
| :--- | :---: | :--- |
| **PHP 8.2+** | ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) | Lenguaje de programación robusto para el lado del servidor. |
| **Laravel 12** | ![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white) | Framework PHP moderno que facilita el desarrollo rápido y seguro. |
| **Filament v3** | ![Filament](https://img.shields.io/badge/Filament-v3-blue?style=for-the-badge&logo=laravel&logoColor=white) | Framework de administración TALL stack para interfaces CRUD. |
| **MySQL 8.0** | ![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white) | Sistema de gestión de base de datos relacional. |
| **Tailwind CSS** | ![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white) | Framework de CSS orientado a utilidades para diseño rápido. |
| **Livewire** | ![Livewire](https://img.shields.io/badge/livewire-%234e56a6.svg?style=for-the-badge&logo=livewire&logoColor=white) | Framework full-stack para Laravel que permite interfaces reactivas. |
| **Alpine.js** | ![Alpine.js](https://img.shields.io/badge/alpine.js-%238BC0D0.svg?style=for-the-badge&logo=alpine.js&logoColor=black) | Framework de JavaScript ligero para interactividad en el cliente. |
| **Vite** | ![Vite](https://img.shields.io/badge/vite-%23646CFF.svg?style=for-the-badge&logo=vite&logoColor=white) | Herramienta de construcción rápida para el frontend. |
| **Composer** | ![Composer](https://img.shields.io/badge/composer-%23885630.svg?style=for-the-badge&logo=composer&logoColor=white) | Gestor de dependencias para PHP. |

## 4. ESTRUCTURA DE PAQUETES Y DEPENDENCIAS DETALLADA

En esta sección se detallan las librerías clave utilizadas, su propósito técnico y cómo se han integrado en el flujo del proyecto.

### 🛡️ Núcleo y Seguridad

#### **spatie/laravel-permission (v6.24)**
- **¿Por qué?**: Es el estándar de la industria para gestionar Roles y Permisos en Laravel. Evita tener que programar manualmente las comprobaciones de acceso.
- **¿Cómo funciona?**: Almacena los roles (admin, alumno, etc.) y los permisos en tablas de base de datos vinculadas al modelo `User`.
- **Implementación**: Se utiliza en los Resources de Filament para definir quién puede ver, crear o editar registros mediante el método `canViewAny`, `canCreate`, etc.

#### **laravel/breeze (v2.3)**
- **¿Por qué?**: Proporciona un andamiaje (scaffolding) de autenticación minimalista pero seguro.
- **Implementación**: Se usó para la configuración inicial de login, registro y recuperación de contraseña, integrándose perfectamente con el guard de Filament.

### 🖥️ Panel Administrativo y UI

#### **filament/filament (v3.x)**
- **¿Por qué?**: Reduce drásticamente el tiempo de desarrollo de interfaces CRUD complejas.
- **¿Cómo funciona?**: Genera automáticamente formularios y tablas basados en la estructura de los modelos Eloquent.
- **Implementación**: Es el motor principal de toda la interfaz del tutor, alumno y administrador. Se han personalizado los Resources para incluir validaciones complejas y lógica de negocio específica.

![User Resource Code](https://via.placeholder.com/800x300?text=Captura+de+Codigo:+UserResource.php)
*Ejemplo de implementación de un recurso en Filament.*

#### **pxlrbt/filament-excel (v2.5)**
- **¿Por qué?**: Los tutores necesitan exportar listados de alumnos y evaluaciones para sus propios registros.
- **Implementación**: Integrado como una `ExportBulkAction` en las tablas de alumnos y evaluaciones. Permite descargar archivos `.xlsx` filtrados directamente desde la vista del panel.

### 🛠️ Utilidades de Negocio

#### **barryvdh/laravel-dompdf (v3.1)**
- **¿Por qué?**: Los alumnos necesitan un justificante oficial de sus prácticas.
- **¿Cómo funciona?**: Convierte vistas de Blade de Laravel directamente a archivos PDF.
- **Implementación**: Se ha creado una acción personalizada `descargarInforme` en `AlumnoResource` que renderiza la vista `informe.blade.php` con los datos del alumno y sus observaciones.

#### **intervention/image-laravel (v1.5)**
- **¿Por qué?**: El chat permite enviar fotos, y estas pueden ser muy pesadas, saturando el almacenamiento.
- **¿Cómo funciona?**: Intercepta la subida del archivo y lo redimensiona/comprime antes de guardarlo en el disco.
- **Implementación**: En el componente Livewire `InternalChat`, el método `sendMessage` procesa las imágenes con esta librería para asegurar que ninguna foto exceda un tamaño óptimo.

![Image Compression Code](https://via.placeholder.com/800x300?text=Captura+de+Codigo:+Compresion+de+Imagenes)
*Fragmento del código donde se aplica la compresión en el Chat.*

#### **saade/filament-fullcalendar (v3.2)**
- **¿Por qué?**: Proporciona una vista de calendario interactiva dentro del panel de Filament, vital para visualizar plazos de entrega.
- **Implementación**: Se utiliza en el Dashboard mediante `CalendarWidget` para mostrar las fechas de inicio y fin de las prácticas asignadas.

#### **shuvroroy/filament-spatie-laravel-backup (v2.2)**
- **¿Por qué?**: La seguridad de los datos es crítica. Permite realizar copias de seguridad de la base de datos y archivos directamente desde la UI.
- **Implementación**: Configurado como un Plugin en el `AdminPanelProvider`, restringiendo su acceso únicamente a usuarios con el rol de administrador.

#### **laravel/socialite (v5.24)**
- **¿Por qué?**: Facilita la autenticación mediante proveedores externos (OAuth).
- **Implementación**: Integrado en la pantalla de login para permitir el acceso con cuentas de Google, mejorando la comodidad del usuario.

#### **resend/resend-php & railsware/mailtrap-php**
- **¿Por qué?**: Gestión profesional de envío de correos electrónicos (transaccionales y de prueba).
- **Implementación**: Resend se utiliza para notificaciones en producción, mientras que Mailtrap se usa como entorno de pruebas (sandbox) para capturar correos durante el desarrollo.

---

## 5. FUNCIONALIDADES CLAVE DEL SISTEMA

### 📅 Calendario de Actividades Interactivo
- **Vista Centralizada**: Integración de FullCalendar para visualizar todas las prácticas y entregas en una línea de tiempo mensual/semanal.
- **Interacción Dinámica**: Permite a los tutores ver la carga de trabajo de los alumnos y a estos organizar sus tareas de forma visual.

![Calendar Interface](https://via.placeholder.com/800x400?text=Captura+de+Calendario+Interactivo)
*Vista de calendario con las prácticas programadas.*

### 💬 Chat Interno Multi-Usuario
- **Mensajería en Tiempo Real**: Sistema de chat basado en Livewire para comunicación directa entre Alumnos, Tutores y Administradores.
- **Gestión de Adjuntos**: Soporte para envío de imágenes con compresión automática en el lado del servidor para ahorrar espacio.
- **Indicador de Lectura**: Notificaciones visuales de mensajes no leídos en la barra de navegación.

![Chat Interface](https://via.placeholder.com/800x400?text=Captura+de+la+Interfaz+de+Chat)
*Interfaz del chat interno funcionando entre un alumno y un tutor.*

### 📊 Sistema de Evaluación por Competencias
- **Rúbricas Configurables**: Los administradores definen criterios y capacidades evaluables.
- **Cálculo Automático**: El sistema calcula la nota final basándose en las calificaciones de cada capacidad.
- **Exportación Profesional**: Generación de reportes en PDF y listados detallados en Excel.

![Evaluacion Table](https://via.placeholder.com/800x400?text=Captura+de+Tabla+de+Evaluaciones)
*Listado de evaluaciones con filtros dinámicos y badges de calificación.*

### ⚠️ Gestión Inteligente de Incidencias
- **Workflow de Resolución**: Flujo de estados (Abierta, En Proceso, Resuelta) para cada problema detectado.
- **Notificaciones a Tutores**: Registro inmediato de faltas, retrasos o problemas de actitud.

![Incidencias UI](https://via.placeholder.com/800x400?text=Captura+de+Gestion+de+Incidencias)
*Formulario de resolución de incidencias con validación de datos.*

### 📂 Portal de Tareas y Prácticas
- **Asignación Flexible**: Las tareas pueden ser individuales, para un curso completo o para un rol específico.
- **Repositorio de Documentos**: Subida y descarga de archivos adjuntos (memorias, guías, etc.).

![Practicas List](https://via.placeholder.com/800x400?text=Captura+de+Lista+de+Tareas)
*Vista de tareas asignadas con estados de visibilidad diferenciados.*

### 📝 Seguimiento Diario (Bitácora)
- **Registro de Actividades**: Los alumnos anotan diariamente sus tareas y horas realizadas.
- **Validación del Tutor**: Los tutores de empresa pueden revisar y comentar las entradas diarias de sus alumnos.

![Observaciones Code](https://via.placeholder.com/800x300?text=Captura+de+Codigo:+ObservacionDiariaResource.php)
*Lógica de filtrado en la bitácora según el rol del usuario.*

### 🛡️ Matriz de Permisos Avanzada
- **Control Visual**: Se ha implementado una página personalizada para que el Administrador pueda gestionar todos los permisos de Spatie de forma visual y agrupada por módulos.
- **Seguridad Dinámica**: Los cambios en la matriz se aplican en tiempo real, permitiendo ajustar los niveles de acceso de cada rol sin modificar código.

![Permission Matrix](https://via.placeholder.com/800x400?text=Captura+de+Matriz+de+Permisos)
*Interfaz de gestión visual de roles y permisos del sistema.*

### 🔔 Sistema de Notificaciones y Sincronización
- **Notificaciones en Base de Datos**: Los alumnos reciben avisos inmediatos en su panel cuando se publica una evaluación o se asigna una tarea.
- **Alertas por Email (Resend & Mailtrap)**: 
    - **Incidencias**: Envío automático de correos a los tutores de curso cuando se registra una incidencia mediante Mailtrap.
    - **Tareas**: Notificación vía Resend cuando se crea o actualiza una práctica.
- **Sincronización Avanzada con Google Calendar**: 
    - **Multi-usuario**: Las prácticas creadas se sincronizan automáticamente con el calendario de Google de **todos** los usuarios implicados (alumno específico, alumnos de un curso o usuarios con un rol determinado).
    - **Persistencia**: Se utiliza una tabla de pivote (`practice_google_events`) para rastrear los IDs de eventos de Google de forma individual por cada usuario, permitiendo actualizaciones y eliminaciones precisas.

### 💾 Sistema de Backups y Mantenimiento
- **Respaldos Mensuales Automáticos**: El sistema realiza una copia de seguridad completa de la base de datos y archivos el día 1 de cada mes a las 00:00 mediante el programador de tareas de Laravel.
- **Widget de Estado de Backup**: Panel visual en el Dashboard que indica:
    - La fecha del próximo respaldo automático y los días restantes (formateados sin decimales).
    - La fecha y estado del último respaldo realizado con éxito.
- **Limpieza de Chat**: Tarea programada diaria para limpiar mensajes antiguos y optimizar el almacenamiento.

### 👤 Gestión de Usuarios y Perfiles Relacionados
- **Arquitectura de Perfil Único**: Cada usuario (`User`) está vinculado a un perfil específico (`Alumno`, `TutorCurso`, `TutorPracticas` o `Empresa`) mediante un `reference_id`.
- **Creación Centralizada**: El sistema garantiza la integridad de los datos permitiendo la creación de perfiles solo a través del gestor de usuarios, automatizando la asignación de roles y la creación de registros en las tablas relacionadas.
- **Estado de Conexión**: Indicadores de "En línea" y "Última vez visto" basados en la actividad de la sesión del usuario.

![User Management](https://via.placeholder.com/800x400?text=Captura+de+Gestion+de+Usuarios)
*Tabla de usuarios con gestión de roles, avatares y estados de conexión.*

---

## 6. MANUAL DE INSTALACIÓN (Paso a Paso)

### Requisitos Previos
- **PHP 8.2+**
- **Composer**
- **Node.js & NPM**
- **MySQL 8.0+**

### Pasos para el Despliegue Local

1. **Clonar el Repositorio**
   ```bash
   git clone <url-del-repositorio>
   cd proyecto
   ```

2. **Instalar Dependencias de PHP**
   ```bash
   composer install
   ```

3. **Instalar Dependencias de Frontend**
   ```bash
   npm install
   npm run build
   ```

4. **Configuración del Entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Nota: Configura tus credenciales de base de datos en el archivo `.env`.*

5. **Migraciones y Seeders (Datos de Prueba)**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Iniciar el Servidor**
   ```bash
   php artisan serve
   ```
   *La aplicación estará disponible en `http://localhost:8000`.*

---

## 7. MANUAL DE USUARIO POR ROLES

### 👨‍💼 Administrador
- Gestión total de usuarios, roles y permisos.
- Configuración de empresas y cursos.
- Visualización de todas las estadísticas del sistema.
- Realización de copias de seguridad de la base de datos.

### 👩‍🏫 Tutor de Curso (Centro)
- Seguimiento de todos los alumnos de sus cursos asignados.
- Consulta de incidencias y evaluaciones de su grupo.
- Generación de reportes consolidados por curso.

### 👨‍🏭 Tutor de Empresa (Prácticas)
- Registro de observaciones en las bitácoras de sus alumnos.
- Evaluación final del desempeño de los alumnos asignados.
- Registro y resolución de incidencias en el centro de trabajo.

### 👨‍🎓 Alumno
- Registro diario de actividades y horas.
- Consulta de tareas asignadas y descarga de material.
- Visualización de sus propias evaluaciones y feedback.

---

## 8. COMANDOS FRECUENTES Y UTILIDAD

En esta sección se detallan los comandos de Artisan y NPM más utilizados durante el desarrollo y mantenimiento del sistema.

### 🛠️ Comandos de Desarrollo (Artisan)

| Comando | Descripción |
| :--- | :--- |
| `php artisan migrate:fresh --seed` | **Reinicio Total**: Borra todas las tablas, aplica las migraciones y carga los datos de prueba definidos en los Seeders. |
| `php artisan optimize:clear` | **Limpieza Profunda**: Limpia la caché de rutas, configuración, vistas y eventos. Útil ante errores inesperados de interfaz. |
| `php artisan make:filament-resource Nombre` | **Nuevo Módulo**: Genera automáticamente los archivos necesarios para un nuevo CRUD en Filament. |
| `php artisan make:mail NombreMail` | **Nuevo Correo**: Crea una clase Mailable para configurar nuevos tipos de notificaciones por email. |
| `php artisan make:observer NombreObserver --model=Modelo` | **Nuevo Observador**: Crea un Observer para ejecutar lógica automática al crear/editar/borrar registros. |
| `php artisan backup:run` | **Backup Manual**: Ejecuta una copia de seguridad inmediata de la base de datos y archivos. |
| `php artisan schedule:work` | **Simulador de Tareas**: En desarrollo, permite ejecutar las tareas programadas (como los backups mensuales) sin configurar un CRON real. |

### 🌐 Comandos de Frontend (NPM)

| Comando | Descripción |
| :--- | :--- |
| `npm run dev` | Inicia el servidor de desarrollo de Vite para reflejar cambios en CSS/JS al instante. |
| `npm run build` | Compila y optimiza los activos para producción (minificación de archivos). |

### 🔧 Utilidades de Configuración

- **Configuración de Email (Resend)**: El sistema utiliza **Resend** para el envío de correos reales. Se ha preferido Resend sobre Mailtrap para la fase de desarrollo/demo porque permite que los correos lleguen directamente a la bandeja de entrada real del desarrollador (la cuenta con la que se registró en Resend) sin necesidad de configurar un dominio propio desde el primer momento.
  - Para que funcione, el remitente (`MAIL_FROM_ADDRESS`) debe ser `onboarding@resend.dev` si no se tiene un dominio verificado.
  - Los correos de recuperación de contraseña, notificaciones de incidencias y tareas se enviarán de forma real a los destinatarios autorizados.
- **Sincronización de Calendario**: Para que la sincronización con Google Calendar funcione, los usuarios deben iniciar sesión mediante **OAuth (Google Login)**. El sistema detectará automáticamente si el usuario tiene un token válido para sincronizar sus prácticas.
- **Gestión de Backups**: Las copias de seguridad se realizan automáticamente el día 1 de cada mes. El administrador puede ver el estado y los días restantes desde el **Widget de Backup** en el Dashboard principal.
- **Limpieza Automática**: Se ha implementado un comando de limpieza de chat para mantener la base de datos optimizada, eliminando mensajes antiguos según la política de retención configurada.
- **Seguridad de Archivos**: Todas las subidas (avatares, documentos de prácticas, fotos del chat) se gestionan a través del disco `public` de Laravel, asegurando que los archivos sean accesibles solo bajo las rutas autorizadas.

# Proyecto de Gestión de Incidencias - Nosecaen S.L.

Este documento detalla la solución a los problemas planteados en la actividad de Gestión de Incidencias.

**Acceso Administrador:**
*   **Usuario:** `admin@example.com`
*   **Contraseña:** `admin123`

---

### 🟢 Problema 1: Aplicación de Incidencias en Laravel
He desarrollado el sistema completo siguiendo el framework Laravel (Modelos, Controladores y Blade).
*   **Gestión de Tareas y Validaciones**: Se encuentra en el controlador [TaskController.php](app/Http/Controllers/TaskController.php).
*   **Gestión de Clientes**: Se encuentra en [ClientController.php](app/Http/Controllers/ClientController.php).
*   **Gestión de Cuotas y Facturas PDF/Email**: Implementado en [FeeController.php](app/Http/Controllers/FeeController.php).
*   **Modelos de Datos**: Definidos en la carpeta [app/Models/](app/Models/).
*   **Disparador de BD (Trigger)**: Creado mediante esta migración [add_trigger_to_tasks_table.php](database/migrations/2026_04_19_005749_add_trigger_to_tasks_table.php).

---

### 🟢 Problema 2: Pruebas Automatizadas
He creado una batería de pruebas que verifican las rutas y el funcionamiento de los formularios.
*   **Archivo de Tests**: [IncidenciasTest.php](tests/Feature/IncidenciasTest.php).

---

### 🟢 Problema 3: Generación Dinámica de Páginas Web

#### 3.1 Integración de JS y DataTables (CDN)
CRUD de clientes funcionando sin recargar la página usando Fetch API y DataTables.
*   **Vista**: [index_js.blade.php](resources/views/clients/index_js.blade.php).
*   **Ruta**: `/gestor-js`

#### 3.2 Uso de Vue/Quasar usando CDN
Interfaz desarrollada con Vue 3 y componentes de Quasar desde CDN.
*   **Vista**: [index_quasar.blade.php](resources/views/clients/index_quasar.blade.php).
*   **Ruta**: `/gestor-quasar`

#### 3.3 Uso de Vue con VITE y componentes .vue
Listado de tareas usando un componente de Vue 3 real y compilado con Vite a través de Inertia.
*   **Componente .vue**: [Index.vue](resources/js/Pages/Tasks/Index.vue).
*   **Ruta**: `/gestor-vue-vite`

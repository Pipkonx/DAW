# Proyecto de Gestión de Incidencias - Nosecaen S.L.

Este documento detalla la solución a los problemas planteados en la actividad de Gestión de Incidencias.

**Acceso Administrador:**
*   **Usuario:** `admin@example.com`
*   **Contraseña:** `admin123`

---

### 🟢 Problema 1: Aplicación de Incidencias en Laravel (Núcleo Arquitectónico)
Este problema constituye la base de toda la solución, implementando el sistema principal de Nosecaen S.L. siguiendo una arquitectura de **"Thin Controllers"** y centralizando la lógica en los **Modelos**.

*   **Arquitectura y Lógica**: Toda la lógica de negocio y consultas pesadas se han trasladado de los controladores a los modelos (vía `scopes` y métodos personalizados) para cumplir con los estándares académicos.
*   **Gestión de Tareas y Validaciones**: Localizado en [TaskController.php](app/Http/Controllers/TaskController.php) y el modelo [Task.php](app/Models/Task.php).
*   **Gestión de Clientes**: Localizado en [ClientController.php](app/Http/Controllers/ClientController.php) y el modelo [Client.php](app/Models/Client.php).
*   **Facturación (PDF/Email)**: Implementado en [FeeController.php](app/Http/Controllers/FeeController.php) delegando la lógica de pago al modelo [Fee.php](app/Models/Fee.php).
*   **Base de Datos**: Incluye un **Trigger** de BD gestionado por la migración [add_trigger_to_tasks_table.php](database/migrations/2026_04_19_005749_add_trigger_to_tasks_table.php).

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

---

### 🟢 Problema 4: Servicios

#### 4.1 HttpClient (API de Cambio de Moneda)
Al marcar una cuota como pagada, se consulta automáticamente el tipo de cambio actual.
*   **Implementación**: `FeeController@update` y `FeeController@pay`.

#### 4.2 Documentación API con Swagger
Documentación interactiva generada con L5-Swagger y Atributos de PHP 8.
*   **URL**: `/api/documentation`
*   **Controlador API**: [TaskApiController.php](app/Http/Controllers/Api/TaskApiController.php).

#### 4.3 Autenticación con Redes Sociales (Socialite)
Configurada la integración con Google (entorno preparado con variables ficticias).
*   **Rutas**: `/auth/google` y `/auth/google/callback`.

#### 4.4 Simulación de Pagos (PayPal)
Simulación de pasarela de pago que actualiza el estado de la cuota y realiza la conversión a Euros.
*   **Acción**: Botón "Pay" en el listado de cuotas.
*   **Ruta**: `/fees/{id}/pay` (POST).

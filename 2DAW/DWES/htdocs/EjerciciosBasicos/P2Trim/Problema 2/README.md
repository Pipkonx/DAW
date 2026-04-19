# Actividad 2: Pruebas Automatizadas (Software Testing)

Este bloque del proyecto se centra en la **validación y calidad del código** mediante una batería completa de pruebas automatizadas que aseguran la estabilidad del sistema ante cambios.

### 🟢 Problema 2: Pruebas con Pest PHP
Se ha implementado una capa de testing que cubre las funcionalidades críticas de la empresa Nosecaen S.L.

*   **Carpeta de Pruebas**: Localizada en [tests/](tests/).
*   **Pruebas de Funcionalidad**: En el archivo [IncidenciasTest.php](tests/Feature/IncidenciasTest.php), donde se verifica que los formularios de incidencias se procesan correctamente y las rutas son accesibles.
*   **Integridad de Datos**: Uso de `RefreshDatabase` para asegurar que cada prueba se ejecuta en un entorno limpio sin ensuciar la base de datos de producción.
*   **Comando de ejecución**: Para ejecutar las pruebas, utiliza `php artisan test` o `./vendor/bin/pest`.

---

### 🟢 Otros Problemas incluidos (Arquitectura Base)
Este proyecto también incluye la base desarrollada en el [Problema 1](./README.md) pero con el foco puesto en la verificación del código.

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

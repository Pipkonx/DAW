# 📋 Soluciones de la Rúbrica - Proyecto 2ª Evaluación (Nosecaen S.L.)

Este documento detalla el cumplimiento de los requisitos de la rúbrica para el proyecto de gestión de incidencias, indicando los archivos específicos donde se puede verificar cada funcionalidad.

---

## 📍 Rutas de Interés (Estructura Laravel)

Para facilitar la navegación durante la defensa, aquí están las rutas principales del proyecto:

*   **📍 Problema 1 (Core & Blade):**
    *   **Rutas:** [`Problema 1/routes/web.php`](./Problema%201/routes/web.php)
    *   **Controladores:** [`Problema 1/app/Http/Controllers/`](./Problema%201/app/Http/Controllers/)
    *   **Vistas (Blade):** [`Problema 1/resources/views/`](./Problema%201/resources/views/)
    *   **Modelos:** [`Problema 1/app/Models/`](./Problema%201/app/Models/)


---



## 1. Funcionalidades CRUD y Gestión

### 1.1 Gestionar incidencias/tareas por administrativos (CRUD)
* **Estado:** Sí (SIN paginación).
* **Descripción:** Los administradores tienen acceso total para crear, ver, editar y eliminar tareas.
* **Ver en:** [`Problema 1/app/Http/Controllers/TaskController.php`](./Problema%201/app/Http/Controllers/TaskController.php) (métodos `index`, `create`, `store`, `edit`, `update`, `destroy`).

### 1.2 Gestionar empleados por administrativos (CRUD)
* **Estado:** Sí (SIN paginación).
* **Descripción:** Gestión completa de la plantilla, incluyendo alta, edición y baja lógica.
* **Ver en:** [`Problema 1/app/Http/Controllers/EmployeeController.php`](./Problema%201/app/Http/Controllers/EmployeeController.php).

### 1.3 Gestionar clientes por administrativos (CRUD)
* **Estado:** Sí (SIN paginación).
* **Descripción:** Mantenimiento de clientes, datos bancarios y cuotas mensuales con baja lógica.
* **Ver en:** [`Problema 1/app/Http/Controllers/ClientController.php`](./Problema%201/app/Http/Controllers/ClientController.php).

### 1.4 Gestionar incidencias por operarios (CRUD)
* **Estado:** Sí (SIN paginación).
* **Descripción:** Los operarios pueden visualizar sus tareas asignadas y actualizar el estado, añadir notas posteriores y subir archivos adjuntos.
* **Ver en:** [`Problema 1/app/Http/Controllers/TaskController.php`](./Problema%201/app/Http/Controllers/TaskController.php) (lógica filtrada por rol en `index` y `update`).

### 1.A Gestión de roles y acceso
* **Descripción:** Uso de Middlewares personalizados para restringir el acceso según el rol (`admin` u `operator`).
* **Ver en:** 
    * Middlewares: [`app/Http/Middleware/IsAdmin.php`](./Problema%201/app/Http/Middleware/IsAdmin.php), [`app/Http/Middleware/IsOperator.php`](./Problema%201/app/Http/Middleware/IsOperator.php).
    * Modelo: [`app/Models/User.php`](./Problema%201/app/Models/User.php) (métodos `isAdmin()` y `isOperator()`).
    * Rutas: [`Problema 1/routes/web.php`](./Problema%201/routes/web.php) (grupos de rutas protegidos).

### 1.5.1 Gestionar cuotas - CRUD Básico
* **Estado:** Sí.
* **Ver en:** [`Problema 1/app/Http/Controllers/FeeController.php`](./Problema%201/app/Http/Controllers/FeeController.php).

### 1.5.2 Gestionar cuotas - CRUD Filtrada
* **Estado:** No. Actualmente el listado muestra todas las cuotas sin filtro dinámico por cliente.

---

## 2. Validación y Procesos Especiales

### 1.B Validación de datos de entrada
* **Descripción:** Uso del sistema de validación de Laravel (`$request->validate()`) con reglas de integridad y formato.
* **Explicación para no técnicos:** Es un "punto de control" que revisa requisitos (ej. campos obligatorios, formatos de email o teléfono) antes de guardar. Si falla, el sistema avisa al usuario y no guarda datos erróneos.
* **Ver en:** Métodos `store` y `update` de los controladores mencionados arriba.

### 1.B.1 Campos validados en:
* **1.1** CRUD incidencias
* **1.2** CRUD empleados
* **1.3** CRUD clientes
* **1.4** Tareas por operarios

### 1.6 Crear cuota mensual para todos los clientes
* **Estado:** Sí.
* **Descripción:** Función de "Generar Remesa" que automatiza la facturación mensual.
* **Ver en:** [`Problema 1/app/Http/Controllers/FeeController.php`](./Problema%201/app/Http/Controllers/FeeController.php) (método `storeRemittance`).

### 1.6.1 Explicación MVC Remesa
* **Controlador:** Gestiona la lógica de bucle sobre clientes activos.
* **Modelo:** `Fee` persiste los registros y `Client` provee el importe configurado.
* **Vista:** `fees.remittance` solicita la confirmación al usuario.

### 1.9 Crear factura en PDF y envío por correo
* **Estado:** Sí.
* **Descripción:** Generación de PDF mediante `barryvdh/laravel-dompdf` y envío automático.
* **Ver en:** 
    * Lógica: `FeeController@generateInvoice`.
    * Correo: [`app/Mail/InvoiceMail.php`](./Problema%201/app/Mail/InvoiceMail.php).
    * Plantilla: [`resources/views/invoices/template.blade.php`](./Problema%201/resources/views/invoices/template.blade.php).

### 1.10 Registro de incidencias por clientes (Público)
* **Estado:** Sí.
* **Ver en:** `TaskController@createPublic` y `storePublic`.

### 1.10.1 Evitar registros falsos
* **Mecanismo:** Validación cruzada de CIF y Teléfono del cliente antes de permitir el registro.
* **Ver en:** [`app/Models/Client.php`](./Problema%201/app/Models/Client.php) (método `findVerified`).

---

> [!TIP]
> **Rutas de Interés (Problema 2):**
> *   **Pruebas (Feature):** [`Problema 2/tests/Feature/`](./Problema%202/tests/Feature/)
> *   **Pruebas (Unit):** [`Problema 2/tests/Unit/`](./Problema%202/tests/Unit/)
> *   **Factories (Datos):** [`Problema 2/database/factories/`](./Problema%202/database/factories/)

## 3. Pruebas y Seguridad


### 2. Pruebas unitarias/Feature automatizadas
* **Tipos realizados:** 
    * Existencia de rutas.
    * Envío y procesado de formularios.
    * Accesos sin autenticación (Guest access).
    * Validación de contenido HTML (`assertSee`).
* **Ver en:** [`Problema 2/tests/Feature/IncidenciasTest.php`](./Problema%202/tests/Feature/IncidenciasTest.php).

### 2.A Explicación de Pruebas
* **Descripción:** Uso de PHPUnit para simular peticiones y verificar respuestas del servidor y estado de la base de datos. Se ejecutan con `php artisan test`.

### 1.7.A Gestión de usuarios
* **Mecanismo:** Implementación basada en **Laravel Breeze** con extensiones para roles y campos personalizados (`dni`, `role`, `is_active`).

### 1.7 Restablecer contraseña
* **Estado:** Sí. Integrado en el sistema de autenticación.

### 1.8 Enviar correo con PDF adjunto
* **Estado:** Sí. Realizado en el proceso de generación de factura.

---

> [!TIP]
> **Rutas de Interés (Problema 3):**
> *   **Páginas (Vue):** [`Problema 3/Problema 1/resources/js/Pages/`](./Problema%203/Problema%201/resources/js/Pages/)
> *   **Componentes:** [`Problema 3/Problema 1/resources/js/Components/`](./Problema%203/Problema%201/resources/js/Components/)
> *   **Rutas JS:** [`Problema 3/Problema 1/routes/web.php`](./Problema%203/Problema%201/routes/web.php)
> *   **Controladores (Inertia):** [`Problema 3/Problema 1/app/Http/Controllers/`](./Problema%203/Problema%201/app/Http/Controllers/)

## 4. Tecnologías Javascript


### 3. Uso de Javascript
* **Descripción:** Integración progresiva de JS para mejorar la experiencia de usuario (UX).

### 3.1 Integración mediante CDN (DataTables/Fetch)
* **Estado:** Sí. 
* **Ver en:** [`Problema 1/resources/views/clients/index_js.blade.php`](./Problema%201/resources/views/clients/index_js.blade.php).

### 3.1.A Explicación CDN
* **Mecanismo:** Carga de librerías externas y comunicación mediante JSON con una API interna en `ClientController`.

### 3.2 Integración Vue/Quasar (CDN)
* **Estado:** Sí, SIN validación en JS.
* **Ver en:** [`Problema 1/resources/views/clients/index_quasar.blade.php`](./Problema%201/resources/views/clients/index_quasar.blade.php).

### 3.3 Uso de Vue con VITE y componentes .vue
* **Estado:** Sí, SIN validación en JS.
* **Ver en:** [`Problema 3/Problema 1/resources/js/Pages/Tasks/Index.vue`](./Problema%203/Problema%201/resources/js/Pages/Tasks/Index.vue).

### 3.3.1 Explicación Vite
* **Descripción:** Compilador moderno que permite el uso de Single File Components (`.vue`), ofreciendo una velocidad de desarrollo y rendimiento superior a las soluciones basadas en CDN.



---

## 5. Preguntas Frecuentes y Conceptos de Defensa

### 5.1 Diferencias Reales entre Problema 1, 2 y 3
*   **Problema 1 (El Núcleo):** Es el desarrollo backend completo. Se usa **Blade** para las vistas. Aunque Inertia está instalado, el foco aquí es el MVC tradicional y la lógica de negocio.
*   **Problema 2 (Testing):** Es el mismo proyecto pero enfocado en la **calidad**. Se defienden los tests automatizados con PHPUnit.
*   **Problema 3 (Frontend Moderno):** Aquí es donde el proyecto brilla visualmente. Se usa **Inertia.js + Vue 3 + Vite**. La diferencia es que la página ya no se recarga (SPA).

### 5.2 Conceptos Técnicos "Dudosos"

#### 🔹 Props (Propiedades)
*   **Qué son:** Es la forma en que pasamos datos desde el controlador de Laravel hasta el componente de Vue.
*   **Uso:** En el controlador haces `Inertia::render('Pagina', ['tasks' => $tasks])`. Esas `tasks` son las **Props** que recibe el componente Vue para pintarlas.

#### 🔹 Componentes de Correo (`x-mail::message`)
*   Es una etiqueta especial de Laravel (Blade) para los correos electrónicos. Crea una estructura profesional con logo, cuerpo y pie de página automáticamente sin que tengas que escribir el HTML complejo de los emails.

#### 🔹 Carbon (`Carbon\Carbon::parse`)
*   **Qué es:** La librería estándar de PHP para manejar fechas. 
*   **Uso:** `Carbon::parse($fecha)` convierte un texto (string) en un objeto de fecha con el que puedes hacer operaciones (sumar días, cambiar formato, comparar, etc.).

#### 🔹 Componentes Blade (`x-app`, `x-slot`)
*   **`x-app-layout` o `x-app`**: Es el componente "contenedor" que tiene el menú, el footer y el CSS. Evita repetir código en cada página.
*   **`x-slot`**: Se usa para inyectar contenido en un lugar específico del componente padre (por ejemplo, el título de la página o scripts específicos).

#### 🔹 Tipos de Rutas y Métodos
*   **`Route::patch`**: Para actualizar una parte de un registro (ej. cambiar solo el estado de una tarea).
*   **`middleware`**: Un filtro de seguridad que se pone a la ruta (ej. `auth` para obligar a loguearse).
*   **`resource`**: Crea automáticamente las 7 rutas del CRUD (index, store, update...).
*   **`apiResource`**: Igual que el anterior, pero solo las 5 rutas necesarias para una API (sin los formularios de crear/editar).

#### 🔹 Swagger / OpenAPI (`use OA;`, `#[OA\`)
*   **`use OpenApi\Attributes as OA;`**: Importa las etiquetas necesarias para documentar la API.
*   **`#[OA\Get]` o `#[OA\Info]`**: Son **Atributos de PHP 8**. Sirven para que Laravel genere automáticamente la documentación interactiva (Swagger) que permite probar la API desde el navegador.

#### 🔹 Scopes (Query Scopes)
*   **Qué son:** Son métodos que definimos en los Modelos para "empaquetar" filtros o consultas que usamos mucho. Su objetivo es que el Controlador no tenga lógica de base de datos y sea más legible.
*   **Regla de oro:** El método en el Modelo debe empezar por la palabra `scope` (ej: `scopePendientes`), pero al usarlo en el controlador se llama sin el prefijo (ej: `Task::pendientes()->get()`).
*   **Uso en P2Trim:** Se usa en el listado de tareas para filtrar por estado, cliente u operario de forma limpia.
*   **Ver en:** [`app/Models/Task.php`](./Problema%201/app/Models/Task.php#L65).
#### 🔹 CSRF (`@csrf`)
*   **Qué es:** Siglas de *Cross-Site Request Forgery* (Falsificación de Petición en Sitios Cruzados). Es un sistema de seguridad de Laravel.
*   **Qué hace:** Genera un token (un código secreto único) que se envía con cada formulario. Laravel comprueba que ese token sea el correcto antes de procesar cualquier cambio (POST, PUT, DELETE).
*   **Por qué es importante:** Evita que hackers o páginas maliciosas envíen peticiones a tu servidor en nombre de un usuario que ya está logueado, protegiendo así la integridad de los datos.
*   **Ver en:** Cualquier formulario del proyecto, ej: [`create.blade.php`](./Problema%201/resources/views/tasks/create.blade.php#L12).

#### 🔹 Helper de Traducción (`{{ __('...') }}`)
*   **Qué es:** Es una función de Laravel para la internacionalización (i18n).
*   **Qué hace:** Busca la cadena de texto proporcionada en los archivos de traducción (dentro de la carpeta `lang/`). Si encuentra una traducción para el idioma actual del usuario, la muestra; si no, devuelve el texto original.
*   **Uso en el proyecto:** Se utiliza en títulos como `{{ __('Gestión de Incidencias/Tareas') }}` para facilitar que la aplicación sea multidioma en el futuro sin tener que cambiar el código de las vistas.




# 📑 Guía Técnica del Proyecto - Problema 4 (Servicios)

Esta guía detalla la implementación de las funcionalidades avanzadas requeridas para el Problema 4, asegurando el cumplimiento de la rúbrica de la 2ª Evaluación.

---

## 1. Facturación Multi-moneda (4.1) ✅
Hemos implementado un sistema que permite registrar cuotas en la moneda local del cliente (USD, GBP, MXN, etc.) y realizar la conversión contable a euros (EUR) en el momento del pago.

- **Tecnología OBLIGATORIA:** Uso de `Illuminate\Support\Facades\Http` (HttpClient).
- **API Utilizada:** [Exchange API by fawazahmed0](https://github.com/fawazahmed0/exchange-api).
- **Lógica:** Cuando el usuario marca una cuota como pagada, el sistema consulta en tiempo real el tipo de cambio y registra el `eur_amount` y `paid_at`.

---

## 2. Servicio REST y Documentación (4.2) ✅
Se ha creado una API robusta para la gestión de clientes.

- **Rutas:** Implementadas mediante `Route::apiResources` en `routes/api.php`.
- **Formato:** Respuestas en **JSON** con códigos de estado HTTP semánticos (200, 201, 204, 404).
- **Documentación Swagger:** Generada automáticamente usando `L5-Swagger`.
  - Acceso: `http://localhost:8000/api/documentation`
- **Pruebas Automatizadas:** Se han incluido tests de característica en `tests/Feature/ClientApiTest.php` que verifican el CRUD completo de la API.
- **Cliente JS (SPA):** Una interfaz SPA básica implementada con **Fetch API** en `/api-test` que consume y permite interactuar con la API de clientes sin recargar la página.

---

## 3. Autenticación Social con Socialite (4.3) ✅
Permite a los usuarios registrarse e iniciar sesión utilizando proveedores externos.

- **Paquete:** `laravel/socialite`.
- **Proveedores:** Google y GitHub.
- **Configuración:** Credenciales gestionadas en `config/services.php` y variables de entorno en `.env`.
- **Flujo:** Vinculación automática por email. Si el usuario no existe, se crea una cuenta sin necesidad de contraseña manual.

---

## 4. Pasarela de Pago Simulada (4.4) ✅
Simulación del flujo de pago mediante PayPal Sandbox.

- **Proceso:** El usuario selecciona una cuota pendiente y es redirigido a una interfaz que simula la pasarela de PayPal.
- **Resultado:** Tras la confirmación, se procesa el retorno exitoso y se marca la cuota como pagada en la base de datos, realizando la conversión de moneda correspondiente.

---

## 5. Notificaciones por Correo (Rúbrica 1.8) ✅
Cada vez que se genera una nueva cuota para un cliente, el sistema envía automáticamente un correo informativo.

- **Implementación:** Clase Mailable `App\Mail\FeeCreated`.
- **Contenido:** Plantilla Markdown con detalles de la cuota y enlace al panel de pago.
- **Trigger:** Método `store` del `FeeController`.

---

## 🚀 Cómo Replicar / Instalar
1. Clonar el repositorio.
2. Ejecutar `composer install` y `npm install`.
3. Configurar el archivo `.env` con las credenciales de base de datos y Socialite.
4. Ejecutar migraciones: `php artisan migrate`.
5. Iniciar servidor: `php artisan serve`.
6. Para ver la documentación API: `php artisan l5-swagger:generate`.
7. Para ejecutar tests: `php artisan test`.

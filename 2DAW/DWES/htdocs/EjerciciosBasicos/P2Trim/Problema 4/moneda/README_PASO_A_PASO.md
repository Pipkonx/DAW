# 📑 Guía Técnica del Proyecto - Problema 4 (Servicios)

Esta guía detalla la implementación de las funcionalidades avanzadas requeridas para el Problema 4, asegurando el cumplimiento de la rúbrica de la 2ª Evaluación y destacando la limpieza del código mediante abstracción SASS.

---

## 1. Facturación Multi-moneda (Requisito 4.1)
Sistema que permite registrar cuotas en la moneda local del cliente y realizar la conversión contable a euros (EUR) en tiempo real.

- **Servicio de Conversión:** [ServicioDivisas.php](app/Services/ServicioDivisas.php) utiliza `HttpClient` para consultar la API de tipos de cambio.
- **Lógica de Negocio:** En [CuotaController.php (línea 61)](app/Http/Controllers/CuotaController.php#L61) se realiza la conversión y el registro del importe en EUR al marcar como pagada.

---

## 2. Servicio REST y SPA (Requisito 4.2)
API completa documentada con Swagger y cliente JavaScript.

- **Rutas API:** Definidas con `apiResource` en [routes/api.php (línea 12)](routes/api.php#L12).
- **Controlador API:** [ClienteApiController.php](app/Http/Controllers/Api/ClienteApiController.php) con anotaciones OpenAPI/Swagger.
- **Cliente JS (Fetch):** Interfaz SPA en [resources/views/prueba_api.blade.php](resources/views/prueba_api.blade.php) para probar el CRUD asíncronamente.
- **Tests de Calidad:** Verificación automatizada en [tests/Feature/ClientApiTest.php](tests/Feature/ClientApiTest.php).

---

## 3. Autenticación Social (Requisito 4.3)
Integración con Google y GitHub mediante Laravel Socialite.

- **Controlador:** [RedSocialController.php](app/Http/Controllers/Auth/RedSocialController.php) gestiona el flujo de redirección y callback.
- **Registro Automático:** Crea usuarios en la tabla `usuarios` (localizada) vinculándolos por email.

---

## 4. Pasarela de Pago Simulada (Requisito 4.4)
Flujo completo de pago seguro simulando PayPal.

- **Controlador de Pago:** [PagoController.php](app/Http/Controllers/PagoController.php) gestiona la sesión de pago y el retorno de éxito.
- **Vista de Pasarela:** [simulacion_paypal.blade.php](resources/views/pagos/simulacion_paypal.blade.php) con diseño optimizado.

---

## 5. Abstracción de clases de boostrap
- **Abstracción de Clases:** En [resources/sass/app.scss](resources/sass/app.scss) se usan `@extend` de Bootstrap para crear clases semánticas (ej: `.panel-gestion`, `.btn-pagar`).
- **Vista Limpia:** El HTML en [resources/views/cuotas/index.blade.php](resources/views/cuotas/index.blade.php) es ahora mucho más legible al usar una sola clase por elemento.

---

## 6. Notificaciones y Localización
- **Mailing:** [CuotaCreada.php](app/Mail/CuotaCreada.php) envía detalles de la cuota al generarse.
- **Modelos Localizados:** 
    - [Usuario.php](app/Models/Usuario.php)
    - [Cuota.php](app/Models/Cuota.php)
    - [Cliente.php](app/Models/Cliente.php)

---

## 🚀 Instalación y Pruebas
1. `composer install` && `npm install`
2. `php artisan migrate:fresh --seed`
3. `npm run build` (Para compilar los estilos SASS)
4. `php artisan test` (Para verificar la API)

---

## 📮 Pruebas con Postman
Para probar la API de forma profesional con Postman:
   - **URL:** `http://localhost:8000/api/clientes`


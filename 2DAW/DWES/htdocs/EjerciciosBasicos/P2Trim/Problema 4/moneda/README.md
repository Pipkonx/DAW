# PROBLEMA 4

Este soporte multi-moneda, integración de APIs externas y autenticación social.

## Implementación Técnica

### 1. Conversión de Divisas (HttpClient)

El sistema utiliza la fachada `Http` de Laravel para conectar con una API de tipos de cambio externa. Esto permite convertir importes de diversas monedas (USD, GBP, etc.) a Euros en tiempo real durante la creación y el pago de cuotas.

- **Código:** [ServicioDivisas.php (Líneas 23-31)](app/Services/ServicioDivisas.php#L23-L31)
- **Donde se aplica:** [CuotaController.php (Líneas 52 y 80)](app/Http/Controllers/CuotaController.php#L52-L80)

### 2. API REST de Clientes

Dispone de un servicio RESTful completo para la gestión de la entidad Clientes. Está implementado mediante un controlador de recursos que responde en formato JSON.

- **Código:** [ClienteApiController.php (Líneas 42-197)](app/Http/Controllers/Api/ClienteApiController.php#L42-L197)
- **Interfaz:** La gestión asíncrona se realiza mediante `fetch` en la vista [prueba-api](http://127.0.0.1:8000/prueba-api).

### 3. Documentación OpenAPI (Swagger)

La API está documentada siguiendo el estándar OpenAPI 3.0 mediante anotaciones en el código fuente.

- **Anotaciones:** [ClienteApiController.php (Líneas 12-191)](app/Http/Controllers/Api/ClienteApiController.php#L12-L191)
- **Acceso:** [Documentación Interactiva](http://127.0.0.1:8000/api/documentation)

### 4. Autenticación con Google

Integración de Laravel Socialite para permitir el inicio de sesión y registro de usuarios utilizando cuentas de Google.

- **Lógica:** [RedSocialController.php (Líneas 16-61)](app/Http/Controllers/Auth/RedSocialController.php#L16-L61)

## Acceso y Enlaces

- **Administración:** [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login)
    - **Usuario:** `admin@example.com`
    - **Contraseña:** `password`
- **Gestión Clientes (SPA):** [http://127.0.0.1:8000/prueba-api](http://127.0.0.1:8000/prueba-api)

# 💰 Proyecto Facturación Internacional (Problema 4)

Este proyecto implementa servicios avanzados de facturación multi-moneda, API REST documentada y autenticación social.

## 📚 Documentación para la Defensa
He preparado dos documentos clave para ayudarte a explicar el proyecto:

1.  **[Guía Paso a Paso (Comandos y Lógica)](./README_PASO_A_PASO.md)**: Detalla todos los `php artisan` y la estructura técnica.
2.  **[Memoria de Defensa (Explicación sencilla)](./GUIA_PROBLEMA_4.md)**: Ideal para leer mientras enseñas el proyecto al profesor.

## 🚀 Requisitos Implementados

### 4.1 Cliente Servicio (Divisas)
- **Tecnología**: Uso de `HttpClient` de Laravel (`Illuminate\Support\Facades\Http`).
- **Funcionalidad**: Conversión automática de cuotas en moneda local a Euros al momento del pago.
- **API**: Conexión con `currency-api` para obtener tipos de cambio reales.

### 4.2 Servicio REST (API)
- **Rutas**: Implementación de `Route::apiResource` para la gestión de clientes.
- **Formato**: Comunicación íntegra en JSON.
- **Documentación**: Generada con **OpenApi / Swagger**. Accesible en `/api/documentation`.

### 4.3 Social Login
- **Tecnología**: Laravel Socialite.
- **Proveedores**: Configurado para Google y GitHub.

### 4.4 Pagos
- **Funcionalidad**: Botón de pago integrado con simulación de pasarela PayPal.

---

## 🛠️ Instalación rápida
1. `composer install`
2. `php artisan migrate`
3. `php artisan db:seed`
4. `php artisan l5-swagger:generate`
5. `php artisan serve`

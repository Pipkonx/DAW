# Actividad 4: Servicios, APIs e Integraciones Exteriores

Este bloque final se centra en la conectividad del sistema con servicios externos, la seguridad social y la documentación técnica automatizada.

### 🟢 Problema 4: Servicios Avanzados
Se han integrado las siguientes capacidades de nivel empresarial:

*   **4.1 HttpClient e Integración de Cambios**: Consulta automática a APIs de moneda externa para procesos de facturación.
*   **4.2 API RESTful y Swagger**: Exposición de recursos mediante una API documentada bajo el estándar OpenAPI. Ver [TaskApiController.php](app/Http/Controllers/Api/TaskApiController.php) y accesible en `/api/documentation`.
*   **4.3 Autenticación Social (Socialite)**: Login mediante Google configurado para facilitar el acceso de empleados.
*   **4.4 Simulación de Pasarela de Pago**: Proceso de pago ficticio que integra lógica de conversión de moneda y actualización de estados.

---

### 🟢 Arquitectura Base
Este proyecto utiliza el núcleo desarrollado en el [Problema 1](./README.md).

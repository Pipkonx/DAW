# 🚀 Proyecto Final 2ª Evaluación - Nosecaen S.L.

Enlace al repositorio: [GitHub - Pipkonx/DAW](https://github.com/Pipkonx/DAW/tree/main/2DAW/DWES/htdocs/EjerciciosBasicos/P2Trim)

Este repositorio está organizado en **4 Bloques Académicos** independientes. Cada carpeta contiene una versión de la aplicación configurada para destacar una competencia técnica específica del curso.

---

## 📂 Guía Rápida de Diferencias

| Carpeta | Enfoque Principal | Archivos "Protagonistas" |
| :--- | :--- | :--- |
| **[Problema 1](./Problema%201)** | **Arquitectura y Estándares** | [`app/Models`](./Problema%201/app/Models) <br> [`app/Http/Controllers`](./Problema%201/app/Http/Controllers) |
| **[Problema 2](./Problema%202)** | **Software Testing** | [`IncidenciasTest.php`](./Problema%202/tests/Feature/IncidenciasTest.php) <br> [`Pest.php`](./Problema%202/tests/Pest.php) |
| **[Problema 3](./Problema%203)** | **Interactividad (Frontend)** | [`resources/views/clients`](./Problema%203/resources/views/clients) <br> [`Index.vue`](./Problema%203/resources/js/Pages/Tasks/Index.vue) |
| **[Problema 4](./Problema%204)** | **Servicios y APIs** | [`TaskApiController.php`](./Problema%204/app/Http/Controllers/Api/TaskApiController.php) <br> [`Fee.php`](./Problema%204/app/Models/Fee.php) |

---

## 🔍 Detalle del Contenido por Bloque

### 1️⃣ Problema 1: Aplicación Robusta (Core)
Se enfoca en el cumplimiento de la norma de **"Controladores Delgados"**.
*   **Diferencia clave**: Si revisas los controladores de esta carpeta, verás que están limpios. Toda la lógica de consulta y cálculo está en los **Modelos** ([`app/Models`](./Problema%201/app/Models)), cumpliendo con el requisito de no saturar los controladores.
*   **Documentación**: Uso estricto de **PSR-5 PHPDoc** en castellano.

### 2️⃣ Problema 2: Calidad y Verificación
Se enfoca en asegurar que la aplicación no falle tras cambios.
*   **Diferencia clave**: Aquí el foco está en el directorio [`tests/`](./Problema%202/tests/). Incluye pruebas de integración ([`IncidenciasTest.php`](./Problema%202/tests/Feature/IncidenciasTest.php)) que simulan el envío de formularios de incidencias y validan los accesos de Admin vs Operario.

### 3️⃣ Problema 3: Niveles de JS y Vue
Se enfoca en eliminar la recarga de página (UX).
*   **Diferencia clave**: Compara las vistas de clientes en [`resources/views/clients`](./Problema%203/resources/views/clients) y el componente moderno en [`Index.vue`](./Problema%203/resources/js/Pages/Tasks/Index.vue). Muestran la evolución tecnológica desde JS básico hasta una moderna SPA.

### 4️⃣ Problema 4: Conectividad y Servicios
Se enfoca en la comunicación con el exterior.
*   **Diferencia clave**: Implementa el servicio de **Cambio de Moneda** dinámico en el modelo [`Fee.php`](./Problema%204/app/Models/Fee.php) y expone una **API REST** documentada con **Swagger** en el controlador [`TaskApiController.php`](./Problema%204/app/Http/Controllers/Api/TaskApiController.php).

---

**Autor:** Rafael  
**Versión:** 1.0 (Académica)  
**Entorno:** Laravel 12 + MySQL

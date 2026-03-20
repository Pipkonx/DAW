# Memoria Técnica del Proyecto: Wealth Manager

**Autor:** Rafael  
**Tutor:** Analista Senior de Desarrollo  
**Especialidad:** Desarrollo de Aplicaciones Web (DAW)

---

## 1. Resumen Ejecutivo
**Wealth Manager** nació de una frustración personal: tener activos repartidos por tres brokers, dos bancos y un exchange de cripto, y no tener ni idea de cuánto dinero tenía realmente. Este proyecto es mi solución a ese "caos" financiero. A diferencia de las apps que solo anotan gastos, he construido un sistema que "bucea" en internet (Web Scraping) y lee documentos (OCR) para que el usuario no tenga que picar datos a mano. Es una herramienta diseñada por un desarrollador para cualquier persona que quiera tomar el control total de su patrimonio.

---

## 2. Objetivos del Proyecto

### 2.1. Objetivos Técnicos
*   **Automatización Real**: Desarrollar un motor en `app/Http/Controllers/PortfolioController.php` capaz de interpretar extractos bancarios en PDF y capturas de pantalla con una precisión elevada, para que el usuario se olvide del Excel.
*   **Abstracción de Datos**: Crear una capa de servicios en `app/Services/MarketDataService.php` que unifique fuentes como Morningstar o CoinGecko bajo una misma interfaz.
*   **Cómputo Preciso**: Implementar el Costo Promedio Ponderado (WAC) para que el beneficio (P/L) sea real, manejando las compras y ventas de forma matemática en el modelo `app/Models/Asset.php`.

### 2.2. Objetivos de Usuario
*   **Ahorro de Tiempo**: Reducir la carga de datos manual en un 70%.
*   **Visión 360º**: Ver de un vistazo cuánto peso tiene la tecnología o el sector inmobiliario en tu cartera gracias a los gráficos en `resources/js/Components/Charts/AssetAllocationChart.vue`.

---

## 3. Justificación Tecnológica

### 3.1. Arquitectura: Laravel + Inertia
Elegí **Laravel 12** no solo porque es el estándar industrial, sino por cómo maneja los procesos pesados en segundo plano con `app/Jobs/UpdatePricesJob.php`. Para el frontend, usar **Inertia.js** fue un acierto: me permitió trabajar en Vue 3 con la sencillez de no tener que gestionar una API REST separada ni complicarme con tokens JWT para la sesión principal, ya que todo se gestiona de forma segura con las cookies de Laravel.

### 3.2. Las herramientas que me salvaron la vida
*   **`smalot/pdfparser`**: Probé varias, pero esta es la única que no "rompía" las tablas de los extractos bancarios al leer el PDF.
*   **`symfony/dom-crawler`**: Vital para el scraping en `app/Services/MarketData/FundService.php`. Morningstar tiene un HTML muy enrevesado y esta librería me permitió navegarlo con XPath de forma precisa.
*   **`laravel/socialite`**: Implementar el login con Google en `app/Http/Controllers/Auth/GoogleAuthController.php` fue sorprendentemente rápido gracias a este paquete oficial.

---

## 4. Diseño y Modelado de Datos

### 4.1. El corazón de la base de datos
He diseñado el esquema en `database/migrations/` buscando que nada se pierda y que la integridad sea total. Las relaciones principales que sostienen la lógica son:
*   **User → Portfolio**: Un usuario puede tener múltiples carteras (ej. "Inversión Largo Plazo" y "Trading") gestionadas en `app/Models/Portfolio.php`.
*   **Portfolio → Asset**: Cada cartera agrupa diversos activos financieros, definidos en el modelo `app/Models/Asset.php].
*   **Asset → Transaction**: Un activo vive a través de sus movimientos; todas las compras y ventas se registran y vinculan en `app/Models/Transaction.php`.

Esta estructura me permite que, si borras una transacción, el activo sepa automáticamente que debe recalcular su cantidad y precio medio.

---

## 5. Endpoints del Sistema (Routing)

He mapeado todas las rutas en `routes/web.php` y `routes/auth.php`. El uso de **Inertia** hace que casi todas las peticiones `GET` devuelvan una vista enriquecida con datos.

| Endpoint | Método | Controlador | Propósito |
| :--- | :--- | :--- | :--- |
| `/dashboard` | `GET` | `DashboardController@index` | Vista principal con KPIs y gráficos. |
| `/transactions` | `GET` | `TransactionController@index` | Historial completo de movimientos. |
| `/transactions` | `POST` | `TransactionController@store` | Motor de cálculo de WAC y guardado. |
| `/portfolios/preview-import`| `POST` | `PortfolioController@previewImport`| **El núcleo**: Procesa archivos y OCR. |
| `/api/market/search` | `GET` | `MarketDataController@search` | Búsqueda en tiempo real (APIs + Scraping). |
| `/api/market/price` | `GET` | `MarketDataController@getPrice` | Obtención del último precio de mercado. |
| `/expenses` | `GET` | `ExpenseController@index` | Análisis de ahorro y gastos. |
| `/auth/google` | `GET` | `GoogleAuthController@redirect` | Login social con Google. |
| `/profile` | `PATCH` | `ProfileController@update` | Gestión de configuración del usuario. |

---

## 6. Implementación de Funcionalidades Core

### 6.1. Web Scraping: Esquema de Fallbacks
En `app/Services/MarketData/FundService.php`, me encontré con que los fondos de inversión son difíciles de seguir. Mi solución fue un sistema de "salvavidas":
1.  Busco en **Morningstar.es** (para fondos de España).
2.  Si no lo encuentro, salto a **JustETF**.
3.  Si ambos fallan, intento en **Financial Times**.
Esto garantiza que casi nunca te quedes sin ver el valor de tu cartera.

### 6.2. La "magia" del OCR
El mayor reto fue el reconocimiento de imágenes en `app/Http/Controllers/PortfolioController.php:353`. El texto que devuelve la API de OCR.space suele ser un caos de líneas sueltas. Diseñé una **Máquina de Estados** que va leyendo línea a línea:
*   Si ve una fecha, activa el modo `SCANNING`.
*   Si ve un nombre reconocido, busca su vinculación en la tabla `market_assets`.
*   Si ve un monto con el símbolo `€`, cierra la transacción.
Fue frustrante al principio porque los números europeos (uso de comas) daban errores, pero lo solucioné con un helper de limpieza de strings.

---

## 7. Desafíos y Aprendizaje Real

### 7.1. El lío de los formatos numéricos
Uno de los mayores dolores de cabeza fue que cada fuente ( Morningstar, FT, el OCR) escribía los números como quería: `1,200.50`, `1.200,50` o `1200.50`. Tuve que crear una lógica de normalización en el controlador para que el sistema no interpretara que tenía millones de euros cuando solo eran miles (¡ojalá!).

### 7.2. Rendimiento y Caching
Al principio, cargar el dashboard era lento porque el sistema iba a buscar los precios de todos los activos a internet cada vez. Lo solucioné usando `Cache::remember` en los servicios y creando un proceso en segundo plano que refresca los precios a las 6:00 AM (`routes/console.php:13`).

---

## 8. Guía de Instalación en Entorno Local

Si quieres probar el sistema tú mismo, sigue estos pasos:

1.  **Clonado y Backend**:
    ```bash
    git clone https://github.com/usuario/nombre-repo.git
    cd nombre-repo
    composer install
    ```
2.  **Frontend**:
    ```bash
    npm install
    ```
3.  **Configuración**:
    `cp .env.example .env` y genera la clave con `php artisan key:generate`. Configura tu DB y, muy importante, tus claves `EODHD_API_KEY` para los bonos en el `.env`.
4.  **Base de Datos**:
    ```bash
    php artisan migrate --seed
    ```
    He configurado `database/seeders/MarketAssetSeeder.php` para inyectar las categorías base y algunos activos de prueba para que no veas el dashboard vacío.
5.  **Ejecución**:
    En terminales separadas: `php artisan serve` y `npm run dev`.

---

## 9. Conclusiones
Este proyecto me ha enseñado que el mayor valor de una aplicación no es solo que funcione, sino que resuelva un problema real de forma automática. He pasado de anotar mis inversiones en una libreta a tener un sistema que lo hace por mí. Ha sido un reto técnico enorme, especialmente el scraping, pero ver los gráficos de rentabilidad actualizados automáticamente hace que todo el esfuerzo haya valido la pena.

---
**Firmado:**  
Rafael, Desarrollador Principal.
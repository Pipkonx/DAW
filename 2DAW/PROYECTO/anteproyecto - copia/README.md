# 💰 Wealth Manager: Gestor Inteligente de Patrimonio

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![Vue Version](https://img.shields.io/badge/Vue-3.x-green.svg)](https://vuejs.org)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

**Wealth Manager** es una plataforma de ingeniería financiera diseñada para centralizar y automatizar el control de activos heterogéneos (acciones, fondos, cripto, bonos y efectivo). Olvídate de picar datos en un Excel: este sistema utiliza **Web Scraping**, **OCR** y **Parsing de PDFs** para que tu patrimonio se actualice solo.

---

## 🌟 Características Principales

*   **📈 Dashboard Multi-Activo**: Visualización en tiempo real de tu *Net Worth* y rendimiento (P/L).
*   **🤖 Automatización OCR**: Sube una captura de tu banco y el sistema extraerá las transacciones automáticamente usando la API de **OCR.space**.
*   **📄 Importación de PDFs**: Lógica avanzada con `smalot/pdfparser` para leer extractos bancarios sin errores.
*   **🕵️ Motor de Scraping**: Fallback inteligente (Morningstar > JustETF > Financial Times) para obtener precios de fondos donde no hay APIs.
*   **⚖️ Cálculo de WAC**: Implementación matemática del **Costo Medio Ponderado** para una rentabilidad exacta.
*   **📊 Análisis de Distribución**: Desglose visual por sector, industria, región y divisa.

---

## 🛠️ Stack Tecnológico

He diseñado el sistema priorizando la modularidad y la escalabilidad:

### Backend & Core
*   **Laravel 12.x**: Potencia total en el manejo de colas de procesos (`Jobs`) y ORM.
*   **Inertia.js**: La "magia" que une Laravel con Vue 3 sin necesidad de una API REST compleja.
*   **Guzzle**: Cliente HTTP para integraciones con **CoinGecko**, **EODHD** y proveedores de OCR.
*   **Symfony DomCrawler**: Extracción de datos financieros mediante XPath.

### Frontend
*   **Vue 3 (Composition API)**: Reactividad y componentes modulares.
*   **Tailwind CSS**: Diseño moderno, limpio y totalmente *responsive*.
*   **Chart.js**: Gráficas interactivas para el seguimiento de la evolución patrimonial.

---

## 📂 Arquitectura Crítica del Proyecto

| Componente | Ruta del Archivo | Responsabilidad |
| :--- | :--- | :--- |
| **Controlador Maestro** | `app/Http/Controllers/PortfolioController.php` | Orquestación de importaciones y lógica OCR. |
| **Servicio de Datos** | `app/Services/MarketDataService.php` | Gateway centralizado para obtención de precios. |
| **Lógica de Mercado** | `app/Services/MarketData/FundService.php` | Motor de Scraping con sistema de fallbacks. |
| **Modelo Financiero** | `app/Models/Asset.php` | Recalculación de métricas y estados de vinculación. |
| **Procesos Background** | `app/Jobs/UpdatePricesJob.php` | Tareas programadas de actualización de mercado. |
| **Routing** | `routes/web.php` | Definición de los 35+ endpoints del sistema. |

---

## 🧠 Lógica de Ingeniería Avanzada

### 1. Motor de Auto-Healing
El sistema detecta automáticamente inconsistencias en los activos (ej. tras una importación masiva) y dispara la reconstrucción del estado basada en el historial crudo de transacciones:
> `app/Http/Controllers/TransactionController.php:70`

### 2. Simulación de Curva de Rendimiento
Ante la falta de APIs históricas gratuitas para todos los activos, se implementa una **Interpolación Lineal de Plusvalías** para generar gráficas de rendimiento realistas basadas en flujos de caja:
> `TransactionController@getChartData` (Línea 281)

### 3. Máquina de Estados OCR
El parser de imágenes no es lineal; utiliza estados (`SCANNING`, `MATCHING`, `FINALIZING`) para reconstruir transacciones desordenadas o divididas en varias líneas por el motor de OCR.

---

## 🚀 Instalación y Despliegue en Local

Sigue estos pasos para levantar el entorno de desarrollo:

1.  **Clonado y Backend**:
    ```bash
    git clone https://github.com/tu-usuario/wealth-manager.git
    cd wealth-manager
    composer install
    ```
2.  **Dependencias Frontend**:
    ```bash
    npm install
    ```
3.  **Configuración de Entorno**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    > [!WARNING]
    > Es crítico configurar las variables de base de datos y la `EODHD_API_KEY` en el `.env` para que el scraping de bonos y la persistencia funcionen correctamente.

4.  **Migraciones y Seeders**:
    ```bash
    php artisan migrate --seed
    ```
    *Nota: El seeder (`MarketAssetSeeder.php`) cargará activos reales como BTC, AAPL y SPY.*

5.  **Lanzamiento**:
    Inicia los servidores en paralelo: `php artisan serve` y `npm run dev`.

---

## 📈 Roadmap

- [ ] Integración con APIs bancarias directas (PSD2).
- [ ] Notificaciones push de alertas de precios.
- [ ] Soporte para carteras multi-divisa con conversión en tiempo real.
- [ ] Exportación avanzada de informes fiscales (Modelo 720/D6).

---

## 🤝 Créditos y Librerías Destacadas

Este proyecto no sería posible sin:
- [Ziggy](https://github.com/tighten/ziggy) para las rutas en JS.
- [PDFParser](https://github.com/smalot/pdfparser) para la extracción de PDFs.
- [Socialite](https://laravel.com/docs/socialite) para el login con Google.

---
Desarrollado con ❤️ por **Rafael**.
Si este proyecto te ha servido de inspiración, ¡no dudes en darle una ⭐!

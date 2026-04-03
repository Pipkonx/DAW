# 💰 Wealth Manager: Gestión Patrimonial Inteligente

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![Vue Version](https://img.shields.io/badge/Vue-3.x-green.svg)](https://vuejs.org)
[![Tailwind](https://img.shields.io/badge/Tailwind-3.x-blue.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

**Wealth Manager** es una plataforma de ingeniería financiera de alto rendimiento diseñada para centralizar, automatizar y analizar activos heterogéneos (Acciones, ETFs, Criptomonedas, Fondos de Inversión, Bonos y Efectivo). 

Olvídate de las hojas de cálculo manuales: este ecosistema utiliza motores de **Web Scraping resiliente**, **OCR avanzado** y **Parsing logístico de PDFs** para mantener tu patrimonio actualizado en tiempo real con una intervención manual mínima.

---

## 🚀 Características de Clase Enterprise

### 📈 Dashboard de Control Total
Visualización holística del *Net Worth* (Patrimonio Neto) con desgloses de rentabilidad (P/L) diaria y acumulada. Gráficos interactivos de evolución temporal y distribución de activos.

### 🤖 Automatización con Inteligencia Artificial y OCR
Sube una captura de pantalla de tu cuenta bancaria o broker y nuestro motor basado en **OCR.space** extraerá las transacciones, tickers y montos automáticamente, vinculándolos a activos de mercado reales.

### 📄 Motor de Ingesta Documental
Lógica dedicada para el parseo de extractos bancarios en PDF mediante `smalot/pdfparser`. Capaz de interpretar tablas complejas y normalizar datos de múltiples entidades financieras.

### 🕵️ Scraping con Sistema de Fallback
Cuando las APIs tradicionales fallan o no cubren ciertos activos (especialmente fondos de inversión), entra en juego nuestro motor de scraping basado en **XPath** que consulta Morningstar, JustETF y Financial Times de forma secuencial.

### ⚖️ Algoritmos Financieros Precisos
- **Costo Promedio Ponderado (WAC)**: Motor de cálculo preciso para determinar el precio medio de compra tras múltiples operaciones.
- **Normalización de Divisas**: Conversión automática a la divisa base del usuario para una visión unificada.
- **Cálculo de Plusvalías**: Diferenciación entre ganancias latentes y realizadas.

---

## 🛠️ Stack Tecnológico Senior

### Core Backend
- **Laravel 12.x**: El framework PHP más robusto para aplicaciones empresariales.
- **Inertia.js**: Renderizado en el lado del servidor con la reactividad de una SPA.
- **Eloquent ORM**: Modelado de datos financiero altamente optimizado.
- **Laravel Jobs & Queues**: Procesamiento en segundo plano para la actualización masiva de precios mediante comandos programados.

### Frontend Moderno
- **Vue 3 (Composition API)**: Reactividad eficiente y componentes modulares de alta cohesión.
- **Tailwind CSS**: Diseño UI premium, minimalista y totalmente responsivo.
- **Chart.js**: Visualización de datos financiera interactiva.
- **HeroIcons & Lucid**: Set de iconos vectoriales para una interfaz limpia.

---

## 📂 Estructura Crítica del Proyecto

```bash
├── app/
│   ├── Services/               # Capa de lógica de negocio (MarketData, Analysis)
│   ├── Jobs/                   # Tareas programadas (UpdatePrices)
│   ├── Models/                 # Modelos financieros (Asset, Transaction, Portfolio)
│   └── Http/Controllers/       # Orquestadores de peticiones
├── resources/js/
│   ├── Pages/                  # Vistas principales de la aplicación
│   ├── Components/             # Átomos y moléculas de la interfaz
│   ├── Utils/                  # Lógica compartida de formateo y cálculos
│   └── Composables/            # Lógica reactiva reutilizable
└── routes/                     # Definición de la superficie de la aplicación (Web & API)
```

---

## 🏗️ Ingeniería de Software y Buenas Prácticas

Este proyecto no es solo código; es una arquitectura diseñada para durar:
1.  **SOLID**: Aplicación estricta de principios para asegurar que el sistema sea extensible (ej. añadir un nuevo proveedor de datos es tan simple como implementar una interfaz).
2.  **DRY (Don't Repeat Yourself)**: Lógica de formateo y visualización centralizada para evitar inconsistencias.
3.  **Modularización**: El dashboard y las vistas complejas están divididos en micro-componentes para facilitar el mantenimiento y las pruebas.
4.  **Seguridad**: Protección contra XSS, CSRF, inyección SQL y control de acceso basado en políticas (Laravel Policies).

---

## 📈 Roadmap de Desarrollo

- [x] **v1.0**: Lanzamiento Core, OCR y Scraping de Fondos.
- [x] **v1.1**: Integración de IA para análisis de informes.
- [ ] **v1.2**: Sistema de Alertas de Precios y Rebalanceo de Cartera.
- [ ] **v1.5**: Integración directa con APIs bancarias (PSD2).
- [ ] **v2.0**: App Móvil Nativa (iOS/Android) mediante Capacitor.

---

## 🛠️ Guía Rápida de Instalación

1.  **Clonado y Dependencias**:
    ```bash
    git clone https://github.com/tu-usuario/wealth-manager.git
    composer install && npm install
    ```
2.  **Entorno**:
    `cp .env.example .env` y configura tus credenciales de base de datos y APIs.
3.  **Despliegue de Datos**:
    `php artisan migrate --seed`
4.  **Ejecución**:
    `php artisan serve` y `npm run dev` en terminales separadas.

---

## 🤝 Créditos

Desarrollado con pasión por **Rafael**. 
Si te gusta este proyecto, considera darle una ⭐ en GitHub.

---
*Este proyecto es parte del Trabajo de Fin de Grado (TFG) de Desarrollo de Aplicaciones Web.*

# 💰 FintechPro: Gestión Patrimonial Inteligente

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![Vue Version](https://img.shields.io/badge/Vue-3.x-green.svg)](https://vuejs.org)
[![Tailwind](https://img.shields.io/badge/Tailwind-3.x-blue.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

**FintechPro** es una plataforma de ingeniería financiera de alto rendimiento diseñada para centralizar, automatizar y analizar activos heterogéneos (Acciones, ETFs, Criptomonedas, Fondos de Inversión, Bonos y Efectivo). 

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

### 🤖 Asistente de FAQ Inteligente
Hemos integrado un asistente conversacional directamente en el Dashboard que resuelve dudas comunes sobre el funcionamiento de la plataforma, cálculos financieros y seguridad de los datos, mejorando la curva de aprendizaje del usuario.

### 🔐 Seguridad de Grado Bancario
- **Autenticación de Doble Factor (TOTP)**: Implementación real mediante aplicaciones de autenticación (Google Authenticator, Authy).
- **Trazabilidad de Sesiones**: Historial detallado de conexiones con detección de la sesión actual, IP y UserAgent para prevenir accesos no autorizados.
- **Protección de Datos**: Cifrado de secretos 2FA y políticas de acceso granulares.

### 🎫 Centro de Soporte Integrado
Sistema de ticketing interno que permite la comunicación directa con el equipo técnico. Incluye gestión de estados (Abierto, Respondido, Cerrado) y prioridades para una resolución eficiente.

### 🚀 Onboarding Guiado y Landing Premium
- **Nueva Landing Page**: Rediseño minimalista de alta conversión con composición de **Mockups 3D** basados en capturas reales del producto.
- **Flujo de Bienvenida**: Guía interactiva de 4 pasos para nuevos usuarios, facilitando la transición desde carteras manuales a la automatización de FintechPro.

### ⚖️ Ecosistema Legal y Transparencia
Sistema integral de cumplimiento legal con páginas dinámicas y coherentes:
- **Política de Privacidad**: Detalles sobre el tratamiento de datos y privacidad.
- **Términos de Servicio**: Marco regulatorio de suscripciones y uso de la plataforma.
- **Aviso Legal**: Identificación corporativa completa.

### ⚖️ Algoritmos Financieros Preciso
- **Costo Promedio Ponderado (WAC)**: Motor de cálculo preciso para determinar el precio medio de compra tras múltiples operaciones.
- **Normalización de Divisas**: Conversión automática a la divisa base del usuario para una visión unificada.
- **Cálculo de Plusvalías**: Diferenciación entre ganancias latentes y realizadas.

### 👥 Ecosistema Social y Gurús
- **Follow & Insights**: Sigue a inversores experimentados (Gurús) para ver la composición sectorial de sus carteras sin exponer cantidades exactas.
- **Feed Interactivo**: Publica análisis, comenta estrategias de otros usuarios y guarda (bookmark) posts técnicos.
- **Transparencia Blindada**: Sistema de reportes de comunidad para mantener un entorno profesional y libre de spam.

### 🛡️ Administración Senior y Mantenimiento
- **Telemetría de Salud**: Monitorización en tiempo real del estado de las APIs de mercado y consumo de cuotas.
- **Gestión de Backups**: Generación, descarga e importación de snapshots de la base de datos (SQLite) directamente desde la UI.
- **Control de Usuarios**: Gestión de privilegios administrativos y estados de suscripción manual.

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
- **Inertia.js**: Comunicación fluida entre Laravel y Vue sin API REST externa.
- **Chart.js & Vue-Chartjs**: Motor gráfico para telemetría financiera y distribución de carteras.
- **Axios**: Gestión de peticiones asíncronas para actualizaciones en tiempo real.
- **PragmaRX Google2FA**: Motor estándar para la implementación de TOTP (RFC 6238).
- **Bacon QR Code**: Generación de vectores QR para vinculación de dispositivos.

---

## 🎨 Identidad Visual y Diseño Premium

FintechPro ha sido diseñado bajo una estética **High-Finance Dashboard**, priorizando la legibilidad técnica y una experiencia de usuario inmersiva.

### 🔴 Paleta de Colores (Brand System)
- **Principal**: `Indigo-600` (`#4f46e5`) - Acciones primarias y marca corporativa.
- **Superficies (Dark Mode)**: `Slate-950` (`#020617`) - Fondo inmersivo para reducir la fatiga visual.
- **Estados Financieros**: 
    - `Emerald-500` (`#10b981`) - Ganancias (Bullish).
    - `Rose-500` (`#ef4444`) - Pérdidas (Bearish).
    - `Amber-500` (`#f59e0b`) - Alertas y Tiers Legend.
- **Textos**: Gradientes de `Slate-50` a `Slate-400` para jerarquía visual técnica.

### 📐 Tipografía y Activos
- **Fuentes**: `Inter` y `Outfit` (sans-serif) para una legibilidad óptima de datos numéricos.
- **Iconografía**: 
    - **Lucid Icons**: Para una estética técnica y minimalista.
    - **HeroIcons**: Para acciones core de la interfaz.
- **Avatares**: Integración con **UI-Avatars** para la generación dinámica de perfiles maestros de gurús.

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
- [x] **v1.2**: Rediseño Premium de Landing Page y Mockups reales.
- [x] **v1.3**: Implementación de Ecosistema Legal y Seguridad 2FA avanzada.
- [ ] **v1.5**: Sistema de Alertas de Precios y Rebalanceo de Cartera.
- [ ] **v1.8**: Integración directa con APIs bancarias (PSD2).
- [ ] **v2.0**: App Móvil Nativa (iOS/Android) mediante Capacitor.

---

## 🛠️ Guía Rápida de Instalación

1.  **Clonado y Dependencias**:
    ```bash
    git clone https://github.com/tu-usuario/fintechpro.git
    composer install && npm install
    ```
2.  **Entorno**:
    `cp .env.example .env` y configura tus credenciales de base de datos y APIs.
3.  **Despliegue de Datos**:
    `php artisan migrate --seed`
4.  **Ejecución**:
    `php artisan serve` y `npm run dev` en terminales separadas.

---

## 🔐 Acceso de Prueba (Evaluación)

Para facilitar la revisión de las funcionalidades de administración, social y análisis IA:

**Cuenta de Administrador (Estado Inicial):**
- **Email:** `admin@fintechpro.com`
- **Password:** `admin1234`

**Cuenta de Prueba (5 años de historial):**
- **Email:** `test@example.com`
- **Password:** `password1234`

---

## 🤝 Créditos

Desarrollado con pasión por **Rafael**. 
Si te gusta este proyecto, considera darle una ⭐ en GitHub.

---
*Este proyecto es parte del Trabajo de Fin de Grado (TFG) de Desarrollo de Aplicaciones Web.*

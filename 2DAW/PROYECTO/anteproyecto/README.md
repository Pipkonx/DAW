# FintechPro - Gestión de Finanzas Personales

![FintechPro Dashboard](https://via.placeholder.com/800x400?text=FintechPro+Preview)

**FintechPro** es una aplicación de gestión financiera personal (SPA) construida con **Laravel 11**, **Vue 3** e **Inertia.js**. Diseñada para ser intuitiva incluso para usuarios sin experiencia financiera, permite llevar un control total de ingresos, gastos, ahorros e inversiones en una interfaz moderna y profesional.

---

## 🚀 Características Principales

### 1. 📊 Dashboard Financiero Integral
- **KPIs en Tiempo Real:** Visualiza tu Patrimonio Neto, Ingresos Mensuales, Gastos, Tasa de Ahorro y Rendimiento de Inversiones al instante.
- **Gráficos Interactivos:** Evolución histórica de tu patrimonio y desglose de activos (Doughnut Chart) con filtros por tipo de activo.
- **Resumen Mensual:** Desglose automático de transacciones por tipo (Ingresos, Gastos, Compras, Ventas, Dividendos, etc.).

### 2. 💰 Gestión de Inversiones Avanzada
- **Soporte Multi-Activo:** Registra Acciones, Criptomonedas, Fondos de Inversión, ETFs, Bonos, Bienes Raíces y más.
- **Cálculo de Rendimiento:** Seguimiento automático del coste base vs. valor actual para calcular ganancias/pérdidas (%) y rendimientos totales.
- **Proyección a Futuro:** Estimación de tu patrimonio a 1 año basada en tu ritmo de ahorro actual y un crecimiento conservador del 5%.

### 3. 📝 Transacciones Dinámicas
- **Formulario Inteligente:** El botón "Nueva Transacción" adapta los campos según la operación:
  - *Compras/Ventas:* Pide activo, cantidad y precio.
  - *Dividendos:* Pide activo y monto recibido.
  - *Gastos/Ingresos:* Pide categoría y monto.
- **Categorización Automática:** Organiza tus movimientos para un análisis detallado.

### 4. 🔐 Seguridad y Autenticación
- **Login Social:** Integración completa con **Google OAuth** (vía Laravel Socialite).
- **Registro Seguro:** Verificación de correo electrónico y validaciones robustas.
- **UI Consistente:** Diseño de autenticación unificado con la estética de la aplicación.

### 5. 🎨 Diseño Fintech Moderno
- **Interfaz Limpia:** Estilo minimalista con Tailwind CSS, sombras suaves y tipografía clara.
- **Modo Educativo:** Tooltips explicativos (`InfoTooltip`) en cada métrica para ayudar a entender conceptos financieros.
- **Responsive:** Funciona perfectamente en escritorio, tablet y móvil.

---

## 🛠️ Stack Tecnológico

- **Backend:** [Laravel 11](https://laravel.com)
- **Frontend:** [Vue 3](https://vuejs.org) (Composition API)
- **Full-Stack Glue:** [Inertia.js](https://inertiajs.com)
- **Estilos:** [Tailwind CSS](https://tailwindcss.com)
- **Base de Datos:** MySQL / PostgreSQL
- **Gráficos:** [Chart.js](https://www.chartjs.org) (vía vue-chartjs)
- **Autenticación:** Laravel Breeze + Socialite

---

## ⚙️ Instalación y Configuración

Sigue estos pasos para desplegar el proyecto en tu entorno local:

### 1. Prerrequisitos
- PHP 8.2+
- Composer
- Node.js & NPM
- Servidor de Base de Datos (MySQL/MariaDB/PostgreSQL)

### 2. Clonar y Configurar
```bash
git clone https://github.com/tu-usuario/fintech-pro.git
cd fintech-pro

# Instalar dependencias de PHP
composer install

# Instalar dependencias de JS
npm install
```

### 3. Configuración de Entorno (.env)
Copia el archivo de ejemplo y configura tu base de datos:
```bash
cp .env.example .env
php artisan key:generate
```

Edita el `.env` con tus credenciales:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fintech_pro
DB_USERNAME=root
DB_PASSWORD=

# Configuración Google OAuth (Opcional)
GOOGLE_CLIENT_ID=tu-client-id
GOOGLE_CLIENT_SECRET=tu-client-secret
GOOGLE_REDIRECT_URL="${APP_URL}/auth/google/callback"
```

### 4. Base de Datos y Seeders
Ejecuta las migraciones y carga datos de prueba (muy recomendado para ver todas las funciones):
```bash
php artisan migrate --seed
```

### 5. Ejecutar
Inicia los servidores de desarrollo:
```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Compilador Vite (Frontend)
npm run dev
```
Accede a `http://localhost:8000`.

---

## 🎨 Personalización de Estilos

Los estilos están centralizados en `resources/css/app.css` usando capas de Tailwind.

- **Colores:** Se usa la paleta `slate` (grises), `blue` (primario), `emerald` (positivo) y `rose` (negativo). Puedes cambiarlos en `tailwind.config.js`.
- **Componentes:** Clases como `.btn-primary`, `.card`, `.input-field` están definidas en `app.css` para fácil reutilización.

---

## 📂 Estructura del Proyecto

- `app/Models`: Modelos `Transaction` (ingresos/gastos) y `Asset` (inversiones).
- `app/Http/Controllers/DashboardController`: Lógica principal de cálculo de KPIs y gráficos.
- `resources/js/Pages`: Vistas Vue (Dashboard, Auth, Legal).
- `resources/js/Components`: Componentes reutilizables (`TransactionModal`, `InfoTooltip`, `Charts/*`).

---

## 📄 Licencia

Este proyecto es de código abierto bajo la licencia [MIT](https://opensource.org/licenses/MIT).

---

*Desarrollado con ❤️ para ayudarte a dominar tus finanzas.*

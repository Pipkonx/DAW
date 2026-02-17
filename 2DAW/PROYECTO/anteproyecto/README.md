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
- **Modo Oscuro:** Soporte completo para tema oscuro en toda la aplicación.

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

## 🏗️ Arquitectura y Refactorización

El proyecto ha sido refactorizado siguiendo principios de **Clean Code** y **Arquitectura de Componentes** para asegurar escalabilidad y mantenibilidad.

### Estructura de Componentes
Se ha adoptado una estrategia de "Componentes Atómicos" y "Separación de Responsabilidades":

- **Orquestadores (Pages):** Las vistas principales (ej: `Transactions/Index.vue`) actúan solo como orquestadores, gestionando el estado global y la comunicación entre componentes hijos, sin contener lógica de presentación compleja.
- **Componentes de Dominio:** Se han creado componentes específicos por funcionalidad en directorios organizados (ej: `Components/Transactions/`).
  - `PortfolioHeader`: Gestión de carteras.
  - `EvolutionChart` y `AllocationChart`: Lógica de visualización de datos.
  - `AssetsTable`: Listado y filtrado de activos.
  - `TransactionHistory`: Historial de operaciones con paginación.
  - `ExportModal`: Lógica reutilizable de exportación.

### Utilidades Compartidas
- **Formatting Utils:** Se ha centralizado la lógica de formato (moneda, porcentajes, fechas) en `Utils/formatting.js` para garantizar consistencia en toda la app y facilitar la localización.

### Buenas Prácticas Aplicadas
- **Single Responsibility Principle:** Cada componente tiene una única responsabilidad.
- **DRY (Don't Repeat Yourself):** Eliminación de código duplicado mediante extracción de componentes y utilidades.
- **Composition API:** Uso moderno de Vue 3 con `<script setup>` para una lógica más limpia.
- **Prop Validation:** Definición estricta de `props` para asegurar la integridad de los datos.

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

### 5. Compilar Assets y Servir
En dos terminales separadas:
```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Compilación de Assets (Vite)
npm run dev
```

---

## 🤝 Contribución

1. Haz un Fork del proyecto
2. Crea una rama para tu Feature (`git checkout -b feature/AmazingFeature`)
3. Haz Commit de tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Haz Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

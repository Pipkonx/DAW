<div align="center">

# 🎨 Guía de Estilos

### FintechPro — Sistema de Diseño Visual

<br/>

<img src="https://img.shields.io/badge/Framework-Tailwind_CSS_v3-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind"/>
<img src="https://img.shields.io/badge/Fuente-Poppins-000?style=for-the-badge&logo=google-fonts&logoColor=white" alt="Font"/>
<img src="https://img.shields.io/badge/Iconos-Heroicons-6366F1?style=for-the-badge" alt="Icons"/>

</div>

---

## 📑 Índice

1. [Tipografía](#1--tipografía)
2. [Paleta de Colores](#2--paleta-de-colores)
3. [Componentes UI](#3--componentes-ui-design-system)
4. [Iconografía](#4--iconografía)
5. [Espaciado y Rejilla](#5--espaciado-y-rejilla)
6. [Modo Oscuro](#6--modo-oscuro)
7. [Archivos de Referencia](#7--archivos-de-referencia)

---

## 1. ✏️ Tipografía

El proyecto utiliza una única familia tipográfica para mantener coherencia visual en toda la interfaz.

### Fuente Principal

| Propiedad | Valor |
|:---|:---|
| **Familia** | `Poppins` (Sans-serif) |
| **Fallback** | `sans-serif` → stack por defecto de Tailwind |
| **Carga** | Google Fonts CDN |
| **Pesos utilizados** | Regular (400), Semibold (600), Bold (700), Extrabold (800), Black (900) |

### Justificación

**Poppins** es una tipografía geométrica de formas limpias y redondeadas que transmite modernidad y confianza. Su alta legibilidad en pantalla y su amplio rango de pesos la convierten en una elección ideal para aplicaciones financieras donde la claridad de la información es crítica.

### Escala Tipográfica

| Clase Tailwind | Uso | Ejemplo |
|:---|:---|:---|
| `text-7xl font-black` | Título Hero (H1) | *"Todas sus inversiones."* |
| `text-5xl font-black` | Títulos de sección (H2) | *"Máxima protección"* |
| `text-2xl font-bold` | Subtítulos de tarjeta (H3) | *"Importar"* |
| `text-xl` | Descripciones destacadas | Textos de lead |
| `text-lg` | Testimonios, FAQ | Citas y respuestas |
| `text-base` | Texto de cuerpo | Contenido general |
| `text-sm` | Badges, etiquetas | Metadatos y labels |

> 📎 **Configuración:** [`tailwind.config.js`](../tailwind.config.js) — línea 17: `fontFamily: { sans: ['Poppins', ...] }`

---

## 2. 🎨 Paleta de Colores

La paleta ha sido diseñada para transmitir profesionalidad financiera, con un contraste alto para garantizar accesibilidad WCAG 2.1.

### Colores Primarios — Marca

| Token | Hex | Muestra | Uso |
|:---|:---:|:---:|:---|
| `blue-600` | `#2563EB` | 🟦 | Color principal de marca, CTA, enlaces activos |
| `blue-700` | `#1D4ED8` | 🟦 | Estado hover de botones primarios |
| `blue-500` | `#3B82F6` | 🟦 | Acentos, checkmarks, indicadores |
| `blue-100` | `#DBEAFE` | 🔵 | Fondos de iconos y badges (modo claro) |

### Colores Neutros — Superficie y Texto

| Token | Hex | Muestra | Uso |
|:---|:---:|:---:|:---|
| `white` | `#FFFFFF` | ⬜ | Fondo principal (modo claro) |
| `slate-50` | `#F8FAFC` | ⬜ | Fondos de sección alternos, tarjetas |
| `slate-100` | `#F1F5F9` | ⬜ | Bordes de tarjetas (modo claro) |
| `slate-400` | `#94A3B8` | 🔲 | Texto secundario, placeholders |
| `slate-600` | `#475569` | 🔲 | Texto de cuerpo principal |
| `slate-900` | `#0F172A` | ⬛ | Encabezados, fondo modo oscuro |
| `slate-950` | `#020617` | ⬛ | Fondo profundo modo oscuro |

### Colores de Estado — Feedback

| Estado | Token | Hex | Uso |
|:---|:---|:---:|:---|
| ✅ Éxito / Ganancia | `green-500` | `#22C55E` | Rentabilidades positivas, confirmaciones |
| ❌ Error / Pérdida | `red-500` | `#EF4444` | Pérdidas, acciones destructivas, errores |
| ⚠️ Advertencia | `amber-500` | `#F59E0B` | Alertas, datos pendientes de revisión |
| ℹ️ Información | `blue-400` | `#60A5FA` | Tooltips, badges informativos |

### Contraste y Accesibilidad

| Combinación | Ratio | Cumple WCAG AA |
|:---|:---:|:---:|
| `slate-900` sobre `white` | **15.4:1** | ✅ |
| `slate-600` sobre `white` | **5.9:1** | ✅ |
| `blue-600` sobre `white` | **4.6:1** | ✅ |
| `white` sobre `slate-900` | **15.4:1** | ✅ |
| `white` sobre `blue-600` | **4.6:1** | ✅ |

---

## 3. 🧩 Componentes UI (Design System)

Todos los componentes están construidos con el sistema **utility-first** de Tailwind CSS. Los estilos reutilizables están centralizados en [`resources/css/app.css`](../resources/css/app.css) mediante `@layer components`.

### Botones

| Variante | Clase CSS | Aspecto |
|:---|:---|:---|
| **Primario** | `.btn-primary` | Fondo `blue-600`, texto blanco, sombra `shadow-blue-500/30`, hover con elevación `-translate-y-0.5` |
| **Secundario** | `.btn-secondary` | Fondo blanco, borde `slate-200`, hover fondo `slate-50` |
| **Peligro** | `DangerButton.vue` | Fondo rojo para acciones destructivas (eliminar, revocar) |

### Tarjetas (Cards)

```
┌─────────────────────────────────────┐
│  .card                              │
│                                     │
│  bg-white                           │
│  rounded-2xl                        │
│  border border-slate-100            │
│  shadow-sm → hover:shadow-md        │
│  p-6                                │
│                                     │
└─────────────────────────────────────┘
```

### Formularios

| Elemento | Clase | Comportamiento |
|:---|:---|:---|
| **Input** | `.input-field` | Borde `gray-300`, foco `blue-500` con ring, esquinas `rounded-lg` |
| **Label** | `InputLabel.vue` | Texto `gray-700`, `font-medium`, asociado vía `for` |
| **Error** | `InputError.vue` | Texto `red-600`, aparece bajo el campo con mensaje claro |
| **Checkbox** | `Checkbox.vue` | Borde redondeado, check `blue-600` |

### Componentes Interactivos

| Componente | Archivo Vue | Descripción |
|:---|:---|:---|
| **Modal** | `Modal.vue` | Overlay con backdrop oscuro, contenido centrado, transición `fade` |
| **Modal Confirmación** | `ModalConfirm.vue` | Modal con título, mensaje y acciones (confirmar/cancelar) |
| **Tooltip** | `InfoTooltip.vue` | Icono `?` con tooltip flotante al hover |
| **Dropdown** | `Dropdown.vue` | Menú desplegable con transición, cierre al hacer clic fuera |
| **Toast** | `Toast.vue` | Notificación emergente con auto-dismiss y tipos (success/error/info) |

> 📎 **Directorio de componentes:** [`resources/js/Components/BaseUI/`](../resources/js/Components/BaseUI/)

---

## 4. 🔷 Iconografía

| Propiedad | Valor |
|:---|:---|
| **Librería** | [Heroicons](https://heroicons.com/) v2 (integración Vue) |
| **Paquete** | `@heroicons/vue` |
| **Estilo** | Iconos de contorno (`outline`) — 24×24px |
| **Formato** | SVG inline — peso mínimo, escalable sin pérdida |
| **Uso** | Navegación, acciones de formulario, indicadores de estado |

### Criterios de Uso

- Usar iconos `outline` para la interfaz general.
- Usar iconos `solid` para estados activos o seleccionados.
- Acompañar siempre los iconos de texto descriptivo o atributos `aria-label` para accesibilidad.

---

## 5. 📐 Espaciado y Rejilla

### Sistema de Espaciado

Tailwind utiliza una escala de espaciado basada en **incrementos de 4px**:

| Clase | Valor | Uso Común |
|:---|:---:|:---|
| `p-2` / `m-2` | 8px | Padding interno de badges |
| `p-4` / `m-4` | 16px | Espaciado estándar entre elementos |
| `p-6` / `m-6` | 24px | Padding de tarjetas |
| `p-8` / `m-8` | 32px | Padding de secciones de features |
| `gap-4` | 16px | Separación en grids y flex |
| `gap-8` | 32px | Separación entre tarjetas |
| `gap-16` | 64px | Separación entre columnas del hero |

### Sistema de Rejilla Responsive

```
┌──────────────────────────────────────────────────────┐
│                   MOBILE (default)                    │
│  ┌──────────────────────────────────────────────┐    │
│  │              1 columna (100%)                 │    │
│  └──────────────────────────────────────────────┘    │
├──────────────────────────────────────────────────────┤
│                   TABLET (md: 768px)                  │
│  ┌─────────────────┐  ┌─────────────────┐            │
│  │    Columna 1     │  │    Columna 2     │           │
│  └─────────────────┘  └─────────────────┘            │
├──────────────────────────────────────────────────────┤
│                  DESKTOP (lg: 1024px)                 │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐           │
│  │  Col 1    │  │  Col 2    │  │  Col 3    │          │
│  └──────────┘  └──────────┘  └──────────┘           │
└──────────────────────────────────────────────────────┘
```

| Breakpoint | Prefijo | Ancho Mínimo | Columnas |
|:---|:---:|:---:|:---:|
| Móvil | *(default)* | 0px | 1 |
| Tablet | `md:` | 768px | 2 |
| Desktop | `lg:` | 1024px | 3 |
| Wide | `xl:` | 1280px | 3-4 |
| Ultra-wide | `2xl:` | 1536px | Contenido centrado `max-w-7xl` |

---

## 6. 🌙 Modo Oscuro

El proyecto soporta **modo oscuro** mediante la estrategia `class` de Tailwind:

```javascript
// tailwind.config.js
darkMode: 'class'
```

| Elemento | Modo Claro | Modo Oscuro |
|:---|:---|:---|
| Fondo principal | `bg-white` | `dark:bg-slate-900` |
| Fondo de tarjeta | `bg-slate-50` | `dark:bg-slate-800/50` |
| Texto principal | `text-slate-900` | `dark:text-white` |
| Texto secundario | `text-slate-600` | `dark:text-slate-400` |
| Bordes | `border-slate-100` | `dark:border-slate-700` |

---

## 7. 📂 Archivos de Referencia

| Archivo | Ruta | Descripción |
|:---|:---|:---|
| **Configuración Tailwind** | [`tailwind.config.js`](../tailwind.config.js) | Fuente, dark mode, plugins |
| **Estilos globales** | [`resources/css/app.css`](../resources/css/app.css) | Clases reutilizables `@layer components` |
| **Componentes Base** | [`resources/js/Components/BaseUI/`](../resources/js/Components/BaseUI/) | Modal, Tooltip, Dropdown, Toast... |
| **Landing Page** | [`resources/js/Pages/Welcome.vue`](../resources/js/Pages/Welcome.vue) | Ejemplo completo del sistema de diseño |
| **PostCSS Config** | [`postcss.config.js`](../postcss.config.js) | Pipeline de procesamiento CSS |

---

<div align="center">

*Guía de Estilos v1.0 — Proyecto FintechPro · DAW 2025/2026*

</div>

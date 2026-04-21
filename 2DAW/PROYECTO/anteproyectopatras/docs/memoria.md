<div align="center">

# 📝 Memoria Técnica del Proyecto

### FintechPro — Gestión Patrimonial Inteligente

<br/>

| | |
|:---|:---|
| **Autor** | Rafael |
| **Fecha** | Abril 2026 |
| **Titulación** | Ciclo Superior en Desarrollo de Aplicaciones Web (DAW) |
| **Módulo** | Diseño de Interfaces Web |

</div>

---

## 📑 Índice

1. [Introducción](#1--introducción)
2. [Planificación del Proyecto](#2--planificación-del-proyecto)
3. [Creación — Justificación Tecnológica](#3--creación--justificación-tecnológica)
4. [Multimedia y Elementos Interactivos](#4--multimedia-y-elementos-interactivos)
5. [Accesibilidad y Usabilidad](#5--accesibilidad-y-usabilidad)
6. [Webgrafía y Recursos](#6--webgrafía-y-recursos)
7. [Incidencias y Conclusiones](#7--incidencias-y-conclusiones)

---

## 1. 📌 Introducción

**FintechPro** es una aplicación web de gestión patrimonial inteligente desarrollada como proyecto integrador del módulo de Diseño de Interfaces Web. La plataforma permite a los usuarios consolidar activos financieros de múltiples fuentes (brókers, bancos, exchanges), automatizar la ingesta de datos mediante tecnología OCR y visualizar su rendimiento en un dashboard interactivo con telemetría en tiempo real.

> El objetivo principal del proyecto es demostrar la capacidad de planificar, diseñar, desarrollar y validar una interfaz web completa que cumpla estándares profesionales de accesibilidad, usabilidad y rendimiento.

---

## 2. 📋 Planificación del Proyecto

### 2.1 Tablero de Gestión — Roadmap

La planificación del proyecto se ha centralizado en un **Roadmap iterativo** documentado en el [README principal](../README.md#️-roadmap). Este Roadmap divide el desarrollo en **5 fases cronológicas** con seguimiento de estado:

| Fase | Nombre | Estado |
|:---:|:---|:---:|
| 1 | Cimentación de la arquitectura VILT | ✅ Completado |
| 2 | Automatización e Ingesta (OCR / PDF) | ✅ Completado |
| 3 | Seguridad y Social Hub | ✅ Completado |
| 4 | Optimización, Rediseño y Arquitectura | 🔄 En progreso |
| 5 | Evolución futura (Mobile, Open Banking) | ⬜ Backlog |

> **Nota:** La estructura del Roadmap actúa como evidencia de planificación equivalente a un tablero Trello, documentando cada tarea, su estado y descripción detallada directamente en el repositorio.

### 2.2 Control de Versiones — GitHub

Se utiliza **GitHub** como plataforma de control de versiones, lo que permite:

- Historial granular de todos los cambios mediante **commits semánticos** (`feat:`, `fix:`, `docs:`).
- Gestión de incidencias e ideas a través de **Issues** y **Pull Requests**.
- Flujo de trabajo colaborativo basado en ramas (`feature/`, `hotfix/`).
- Documentación del proyecto integrada en el repositorio.

### 2.3 Estructura del Sitio Web

El proyecto sigue la estructura estándar de **Laravel 12 + Vite**, con una separación clara entre:

```
anteproyecto/
├── app/                    ← Backend: Controladores, Modelos, Servicios
│   ├── Http/Controllers/   ← Lógica de controladores
│   ├── Models/             ← Modelos Eloquent ORM
│   └── Services/           ← Capa de servicios (Service Layer)
├── resources/
│   ├── js/                 ← Frontend: Vue 3 + componentes reactivos
│   │   ├── Pages/          ← Vistas de ruta
│   │   ├── Components/     ← Componentes reutilizables
│   │   └── composables/    ← Lógica reactiva compartida
│   ├── css/                ← Hoja de estilos (app.css)
│   └── multimedia/         ← Archivos multimedia (original/ + optimized/)
├── public/                 ← Archivos servidos (favicon, imágenes)
├── docs/                   ← Documentación del proyecto
│   ├── memoria.md          ← Este documento
│   ├── guia_de_estilos.md  ← Identidad visual
│   └── manual_buenas_practicas_multimedia.md
└── README.md               ← Documentación principal
```

---

## 3. 🎨 Creación — Justificación Tecnológica

### 3.1 Elección de Tailwind CSS frente a Bootstrap

El enunciado del proyecto sugiere el uso de **Bootstrap** como framework CSS. Sin embargo, para este proyecto se ha optado por **Tailwind CSS v3** de forma fundamentada. A continuación se detallan las razones técnicas de esta decisión:

#### A. Rendimiento y Tamaño del Bundle

| Aspecto | Bootstrap 5 | Tailwind CSS 3 |
|:---|:---:|:---:|
| Tamaño CSS sin purgar | ~230 KB | ~3.5 MB (pre-compilación) |
| Tamaño CSS final (producción) | ~30-50 KB | **~8-15 KB** |
| Método de optimización | Requiere configuración manual de PurgeCSS | Motor **JIT (Just-In-Time)** integrado |
| CSS no utilizado en producción | Alto (muchos componentes no usados) | **Cero** |

> **Conclusión:** Tailwind genera **únicamente las clases CSS que se utilizan** en el código fuente, lo que reduce el peso final del bundle hasta un **70% frente a Bootstrap**, mejorando directamente el **LCP (Largest Contentful Paint)** y las métricas de Core Web Vitals.

#### B. Diseño Utility-First vs. Componentes Predefinidos

Bootstrap impone una estética predefinida reconocible que, para un proyecto que busca una identidad visual única, obliga a **sobrescribir cientos de reglas CSS**. Tailwind adopta el enfoque opuesto (**utility-first**):

- Cada clase CSS realiza **una sola acción** visual (`bg-blue-600`, `rounded-xl`, `shadow-lg`).
- Los componentes se construyen **directamente en el template**, sin dependencia de estilos globales.
- No existe el problema del *"aspecto Bootstrap"*: cada interfaz es única por diseño.

#### C. Integración con Vue 3 y Ecosistema Moderno

El stack VILT (Vue + Inertia + Laravel + Tailwind) es el **estándar de facto** del ecosistema Laravel desde la versión 10:

- **Laravel Breeze** y **Jetstream** utilizan Tailwind como framework por defecto.
- La documentación oficial de Laravel recomienda Tailwind para nuevos proyectos.
- Los **Composables de Vue 3** encapsulan lógica, y Tailwind encapsula estilos: principios de diseño coherentes.

#### D. Mantenibilidad a Largo Plazo

- **Sin colisiones CSS**: Al no usar selectores globales, escalar el proyecto no genera conflictos entre estilos de distintas páginas.
- **Consistencia**: El archivo `tailwind.config.js` centraliza los valores de diseño (colores, fuentes, breakpoints), actuando como **fuente única de verdad** del sistema visual.
- **Developer Experience**: Autocompletado de clases en VSCode vía la extensión oficial *Tailwind CSS IntelliSense*.

> 📎 **Archivo de referencia:** [`tailwind.config.js`](../tailwind.config.js) · [`resources/css/app.css`](../resources/css/app.css)

### 3.2 Rejilla Responsive

Se ha implementado un sistema de rejilla responsiva utilizando las utilidades de **Flexbox** y **CSS Grid** de Tailwind, con los siguientes breakpoints:

| Breakpoint | Prefijo | Ancho mínimo | Uso |
|:---|:---:|:---:|:---|
| Móvil | *(default)* | 0px | Layout de una columna |
| Tablet | `md:` | 768px | Layout de 2 columnas |
| Desktop | `lg:` | 1024px | Layout de 3-4 columnas |
| Ultra-wide | `xl:` / `2xl:` | 1280px+ | Contenido centrado con márgenes amplios |

> 📎 **Ejemplo real:** [`Welcome.vue`](../resources/js/Pages/Welcome.vue) — La landing page utiliza `grid lg:grid-cols-2` para la sección Hero y `grid md:grid-cols-2 lg:grid-cols-3` para la cuadrícula de features.

### 3.3 Componentes UI y Archivo CSS

Los estilos personalizados del proyecto se encuentran centralizados en [`resources/css/app.css`](../resources/css/app.css), donde se definen clases reutilizables mediante la directiva `@layer components`:

- `.btn-primary` — Botón principal con sombra y transición de elevación.
- `.btn-secondary` — Botón secundario con borde fino.
- `.card` — Tarjeta con bordes redondeados y sombra hover.
- `.heading-hero` / `.heading-section` — Estilos tipográficos estandarizados.
- `.input-field` — Campos de formulario con foco azul.

> 📎 **Componentes Vue reutilizables:** [`resources/js/Components/BaseUI/`](../resources/js/Components/BaseUI/) — Incluye `Modal.vue`, `InfoTooltip.vue`, `Dropdown.vue`, `Toast.vue` y más.

---

## 4. 🖼️ Multimedia y Elementos Interactivos

### 4.1 Optimización Multimedia

Se han aplicado procesos de optimización a todos los recursos gráficos del sitio web:

| Tipo | Formato | Técnica de Optimización |
|:---|:---:|:---|
| Mockups de interfaz | PNG / WebP | Compresión sin pérdida, redimensionado al tamaño real de visualización |
| Iconos e indicadores | SVG (Heroicons) | Vectoriales: peso mínimo, nitidez infinita en cualquier resolución |
| Favicon | ICO | Formato estándar para compatibilidad universal en pestañas |
| Imágenes decorativas | PNG | Lazy Loading (`loading="lazy"`) para priorizar el contenido crítico |

> 📎 **Archivos multimedia organizados:** [`resources/multimedia/original/`](../resources/multimedia/original/) y [`resources/multimedia/optimized/`](../resources/multimedia/optimized/)

> 📎 **Manual de referencia completo:** [Manual de Buenas Prácticas Multimedia](./manual_buenas_practicas_multimedia.md)

### 4.2 Componentes Interactivos

La aplicación incorpora componentes interactivos de alto nivel, todos ellos construidos con Vue 3:

| Componente | Archivo | Descripción |
|:---|:---|:---|
| **Modales** | `Modal.vue`, `ModalConfirm.vue` | Diálogos de confirmación para transacciones y acciones destructivas |
| **Tooltips** | `InfoTooltip.vue` | Ayudas contextuales flotantes para la interfaz financiera |
| **Dropdown** | `Dropdown.vue` | Menús desplegables para navegación y acciones |
| **Acordeón (FAQ)** | `Welcome.vue` | Preguntas frecuentes con transición suave `max-height` |
| **Notificaciones** | `Toast.vue` | Alertas emergentes con temporización automática |
| **Gráficos** | Chart.js + vue-chartjs | Pie charts y visualización de datos en tiempo real |
| **Animaciones** | CSS `@keyframes` | Bounce en iconos de seguridad, fade-in en FAQs |

> 📎 **Ruta de componentes:** [`resources/js/Components/BaseUI/`](../resources/js/Components/BaseUI/)

---

## 5. ♿ Accesibilidad y Usabilidad

Se han integrado principios de accesibilidad web siguiendo las directrices **WCAG 2.1** y las recomendaciones de Bootstrap sobre accesibilidad:

### Técnicas Implementadas

| Criterio | Técnica | Estado |
|:---|:---|:---:|
| **Texto alternativo** | Atributo `alt` descriptivo en todas las imágenes informativas | ✅ |
| **Semántica HTML5** | Uso de `<header>`, `<main>`, `<section>`, `<footer>`, `<nav>` | ✅ |
| **Jerarquía de encabezados** | Un único `<h1>` por página, estructura H1→H6 coherente | ✅ |
| **Contraste de color** | Paleta Slate/Blue verificada contra estándar WCAG (4.5:1 mínimo) | ✅ |
| **Navegación por teclado** | Focus visible en todos los elementos interactivos (`focus:ring`) | ✅ |
| **Formularios accesibles** | Etiquetas `<label>` asociadas y mensajes de error claros | ✅ |
| **Contenido sin parpadeo** | Ningún elemento parpadea más de 3 veces/segundo | ✅ |
| **Diseño responsivo** | Funcional al 200-300% de zoom sin pérdida de contenido | ✅ |

> 📎 **Referencia:** [Guía de Accesibilidad de Bootstrap](https://getbootstrap.esdocu.com/docs/5.3/getting-started/accessibility/) · [WCAG 2.1](https://www.w3.org/TR/WCAG21/)

---

## 6. 🌐 Webgrafía y Recursos

### Documentación Oficial
| Recurso | Enlace |
|:---|:---|
| Laravel 12 Documentation | https://laravel.com/docs |
| Vue 3 Composition API | https://vuejs.org/guide/introduction.html |
| Tailwind CSS v3 | https://tailwindcss.com/docs |
| Inertia.js | https://inertiajs.com/ |
| Chart.js | https://www.chartjs.org/docs/latest/ |

### APIs y Servicios Externos
| Recurso | Enlace |
|:---|:---|
| OCR.space API | https://ocr.space/ocrapi |
| EODHD Financial Data API | https://eodhd.com/ |
| CoinGecko Crypto API | https://www.coingecko.com/en/api |

### Herramientas de Desarrollo
| Recurso | Enlace |
|:---|:---|
| TinyPNG (Compresión de imágenes) | https://tinypng.com/ |
| Squoosh (Optimización de imágenes) | https://squoosh.app/ |
| Heroicons (Iconos SVG) | https://heroicons.com/ |

### Recursos de Diseño y Accesibilidad
| Recurso | Enlace |
|:---|:---|
| Bootstrap Accesibilidad | https://getbootstrap.esdocu.com/docs/5.3/getting-started/accessibility/ |
| Diseño Web Accesible (Eniun) | https://www.eniun.com/diseno-desarrollo-webs-accesibles-accesibilidad-web/ |
| Usabilidad Web (Eniun) | https://www.eniun.com/tutorial-usabilidad-web/ |
| Formatos de Vídeo Web (Eniun) | https://www.eniun.com/formatos-archivos-video-conversiones-web/ |
| Animaciones CSS (Eniun) | https://www.eniun.com/animaciones-keyframes-animation/ |
| Freepik (Recursos gráficos) | https://www.freepik.com/ |

---

## 7. 📝 Incidencias y Conclusiones

### Incidencias Técnicas Relevantes

| Incidencia | Solución Aplicada |
|:---|:---|
| Errores OCR en caracteres ambiguos (`8`↔`B`, `0`↔`O`) | Algoritmo de **distancia de Levenshtein** para corrección probabilística |
| Diversidad de formatos en extractos bancarios PDF | Motor RegEx con **múltiples patrones** de extracción adaptados a distintos brókers |
| Inconsistencia en APIs de precios de mercado | Sistema de **fallbacks en cascada** con 4 niveles de resiliencia |
| Rendimiento de carga con múltiples gráficos Chart.js | Implementación de **lazy loading** y renderizado condicional en Vue |

### Conclusiones

El uso de la arquitectura **VILT** (Vue + Inertia + Laravel + Tailwind) ha permitido alcanzar un nivel de interactividad, rendimiento y calidad visual comparable al de las aplicaciones financieras nativas del mercado. La elección de **Tailwind CSS** sobre Bootstrap ha demostrado ser acertada, proporcionando un control total sobre la identidad visual sin sacrificar velocidad de desarrollo ni rendimiento en producción.

El proyecto demuestra la integración exitosa de múltiples tecnologías en un entorno coherente y mantenible, cumpliendo los objetivos de planificación, diseño responsive, optimización multimedia e interactividad avanzada establecidos en el enunciado.

---

<div align="center">

*Documento generado como parte de la entrega del proyecto de Diseño de Interfaces Web — DAW 2025/2026*

</div>

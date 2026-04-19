<div align="center">

# 📸 Manual de Buenas Prácticas Multimedia

### FintechPro — Optimización de Imágenes, Vídeo y Audio para la Web

<br/>

<img src="https://img.shields.io/badge/Formatos-WebP_·_SVG_·_MP4-0ea5e9?style=for-the-badge" alt="Formatos"/>
<img src="https://img.shields.io/badge/Rendimiento-Core_Web_Vitals-22c55e?style=for-the-badge" alt="Performance"/>

</div>

---

## 📑 Índice

1. [Imágenes — Formatos y Optimización](#1--imágenes--formatos-y-optimización)
2. [Vídeo — Manual de Buenas Prácticas](#2--vídeo--manual-de-buenas-prácticas)
3. [Audio — Formatos Recomendados](#3--audio--formatos-recomendados)
4. [Justificación de Formatos en FintechPro](#4--justificación-de-formatos-en-fintechpro)
5. [Licencias y Atribuciones](#5--licencias-y-atribuciones)
6. [Herramientas Recomendadas](#6--herramientas-recomendadas)
7. [Organización de Archivos](#7--organización-de-archivos)

---

## 1. 🖼️ Imágenes — Formatos y Optimización

### Formatos Recomendados

| Formato | Uso Recomendado | Ventajas | Inconvenientes |
|:---:|:---|:---|:---|
| **WebP** | Fotografías, mockups, fondos | Compresión superior (~30% menos que PNG/JPG), soporte en todos los navegadores modernos | Sin soporte en IE11 (irrelevante en 2026) |
| **SVG** | Logotipos, iconos, ilustraciones | Vectorial: peso mínimo, escala infinita sin pérdida de calidad, manipulable con CSS | No apto para fotografías |
| **PNG** | Screenshots, imágenes con transparencia (fallback) | Compresión sin pérdida, canal alpha | Peso elevado frente a WebP |
| **JPG** | Fotografías (fallback navegadores antiguos) | Buena compresión con pérdida controlada | Sin transparencia, artefactos visibles en altas compresiones |

### Técnicas de Optimización Aplicadas

#### A. Compresión

| Técnica | Descripción | Herramienta |
|:---|:---|:---|
| **Compresión sin pérdida** | Elimina metadatos EXIF y optimiza la tabla de colores sin afectar la calidad visual | TinyPNG, OptiPNG |
| **Compresión con pérdida controlada** | Reduce el peso hasta un 70% manteniendo una calidad visual aceptable (quality 80-85%) | Squoosh, Sharp |
| **Redimensionado** | Ajustar las imágenes al tamaño máximo de visualización real en pantalla | ImageMagick, Squoosh |

> ⚠️ **Regla de oro:** Nunca subir una imagen de 4000×3000px si se va a mostrar a 800×600px. El redimensionado previo a la subida puede reducir el peso hasta un **90%**.

#### B. Lazy Loading

Todas las imágenes que no son visibles en el primer scroll deben cargarse de forma diferida:

```html
<!-- Imagen con carga diferida -->
<img src="/images/mockups/dashboard.png"
     alt="Dashboard FintechPro"
     loading="lazy"
     width="800"
     height="450" />
```

| Atributo | Propósito |
|:---|:---|
| `loading="lazy"` | Retrasa la descarga hasta que la imagen entra en el viewport |
| `width` y `height` | Reserva el espacio en el layout, evitando el **CLS** (Cumulative Layout Shift) |
| `alt` | Texto alternativo descriptivo para accesibilidad y SEO |

#### C. Elemento `<picture>` — Formatos Modernos con Fallback

```html
<picture>
  <source srcset="/images/hero.webp" type="image/webp" />
  <source srcset="/images/hero.png" type="image/png" />
  <img src="/images/hero.png" alt="Hero FintechPro" loading="lazy" />
</picture>
```

> El navegador elige automáticamente el formato más eficiente que soporte.

#### D. CDN (Content Delivery Network)

Para proyectos en producción, se recomienda servir los archivos multimedia desde un **CDN** para reducir la latencia:

| CDN | Especialidad |
|:---|:---|
| Cloudflare Images | Optimización automática y caching global |
| Cloudinary | Transformación de imágenes en tiempo real (resize, crop, format) |
| Imgix | Procesamiento de imágenes bajo demanda |

---

## 2. 🎬 Vídeo — Manual de Buenas Prácticas

Este apartado recoge las mejores prácticas para la integración de contenido de vídeo en la web, enfocadas en **rendimiento**, **accesibilidad**, **SEO** y **experiencia de usuario**.

### 2.1 Alojamiento

| Estrategia | Cuándo usar | Ventaja |
|:---|:---|:---|
| **Servicios externos** (YouTube, Vimeo) | Vídeos largos (>30s), tutoriales, demos | Sin carga en el servidor, player optimizado, CDN incluido |
| **Archivos locales optimizados** | Vídeos de fondo, loops breves (<10s) | Control total, sin dependencia externa |

> ⚠️ **Evitar:** Subir vídeos pesados (>10 MB) directamente al servidor sin optimización previa.

### 2.2 Formatos de Vídeo

| Formato | Códec | Compatibilidad | Uso Recomendado |
|:---:|:---:|:---|:---|
| **MP4** | H.264 | 🟢 Universal (todos los navegadores) | **Formato principal** para cualquier vídeo |
| **WebM** | VP9 | 🟡 Navegadores modernos | Alternativa de mayor eficiencia (~30% menos peso) |
| **OGV** | Theora | 🔴 En desuso | Solo como fallback legacy (no recomendado) |

### 2.3 Rendimiento

```html
<!-- Vídeo optimizado para rendimiento -->
<video
  preload="none"
  poster="/images/video-poster.webp"
  controls
  width="1280"
  height="720"
>
  <source src="/videos/demo.mp4" type="video/mp4" />
  <source src="/videos/demo.webm" type="video/webm" />
  Tu navegador no soporta vídeo HTML5.
</video>
```

| Atributo | Impacto en Rendimiento |
|:---|:---|
| `preload="none"` | **No descarga nada** hasta que el usuario interactúe → ahorro total de ancho de banda |
| `preload="metadata"` | Descarga solo metadatos (duración, dimensiones) → compromiso razonable |
| `poster` | Muestra una imagen estática antes de reproducir → mejora el LCP percibido |
| `width` / `height` | Reserva espacio en el layout → evita CLS |

### 2.4 Accesibilidad

| Requisito | Implementación |
|:---|:---|
| **Subtítulos** | Incluir pistas `<track>` con subtítulos en formato WebVTT |
| **Transcripción** | Proporcionar una transcripción textual completa debajo del vídeo |
| **Autoplay silencioso** | Solo permitir `autoplay` con `muted` → nunca con sonido automático |
| **Controles visibles** | Usar el atributo `controls` para garantizar que el usuario pueda pausar/avanzar |

```html
<video controls muted>
  <source src="/videos/demo.mp4" type="video/mp4" />
  <track src="/videos/subtitulos-es.vtt" kind="subtitles" srclang="es" label="Español" default />
</video>
```

### 2.5 SEO

| Técnica | Beneficio |
|:---|:---|
| `title` en el contenedor | Los motores de búsqueda indexan el contexto del vídeo |
| Texto descriptivo cercano | Proporciona contexto semántico para el rastreo |
| Schema.org `VideoObject` | Permite aparición como resultado enriquecido en Google |
| Thumbnail optimizado | Mejora el CTR en resultados de búsqueda |

---

## 3. 🔊 Audio — Formatos Recomendados

| Formato | Códec | Bitrate Recomendado | Uso |
|:---:|:---:|:---:|:---|
| **MP3** | MPEG-1 Layer 3 | 128-192 kbps | **Formato principal** — compatibilidad universal |
| **OGG** | Vorbis | 128-160 kbps | Alternativa con mejor calidad/peso para navegadores modernos |
| **AAC** | AAC-LC | 128 kbps | Alternativa para dispositivos Apple |

```html
<audio controls preload="none">
  <source src="/audio/notification.mp3" type="audio/mpeg" />
  <source src="/audio/notification.ogg" type="audio/ogg" />
  Tu navegador no soporta audio HTML5.
</audio>
```

---

## 4. 📋 Justificación de Formatos en FintechPro

A continuación se detalla la justificación del uso de cada formato multimedia en el proyecto:

| Recurso | Formato Utilizado | Justificación |
|:---|:---:|:---|
| **Mockups de interfaz** | PNG | Capturas de pantalla que requieren alta fidelidad y soporte de transparencia. En producción se convertirían a WebP con fallback PNG |
| **Iconos de interfaz** | SVG (Heroicons) | Formato vectorial que garantiza nitidez absoluta en cualquier resolución (Retina, 4K), con un peso inferior a 1 KB por icono |
| **Favicon** | ICO | Formato estándar para la pestaña del navegador, compatible con todos los navegadores incluyendo versiones legacy |
| **Imágenes decorativas** | PNG + Lazy Loading | Cargadas bajo demanda para no penalizar el tiempo de primer renderizado (FCP) |

### Importancia de la Optimización Multimedia

La optimización del contenido multimedia tiene un impacto directo en tres métricas fundamentales:

```
┌─────────────────────────────────────────────────────────────┐
│                    CORE WEB VITALS                           │
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │     LCP      │  │     FID      │  │     CLS      │       │
│  │  Largest     │  │  First Input │  │  Cumulative  │       │
│  │  Contentful  │  │  Delay       │  │  Layout      │       │
│  │  Paint       │  │              │  │  Shift       │       │
│  │             │  │             │  │             │       │
│  │ ← Imágenes  │  │ ← Scripts   │  │ ← Dimensiones│       │
│  │   pesadas    │  │   de carga  │  │   sin definir│       │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│                                                             │
│  ✅ Optimizar imágenes mejora LCP                           │
│  ✅ Lazy Loading reduce la carga inicial                    │
│  ✅ Definir width/height previene CLS                       │
└─────────────────────────────────────────────────────────────┘
```

---

## 5. 📜 Licencias y Atribuciones

### Buenas Prácticas de Licencias

| Regla | Descripción |
|:---|:---|
| ✅ Verificar siempre la fuente y licencia | Antes de usar cualquier recurso, confirmar que la licencia permite su uso |
| ✅ Mantener un registro | Documentar la licencia de cada recurso multimedia utilizado |
| ✅ Usar bancos de recursos confiables | Freepik, Unsplash, Pexels, Heroicons |
| ✅ Crear contenido propio siempre que sea posible | Mockups y capturas generadas desde la propia aplicación |
| ✅ Incluir atribuciones visibles cuando se requiera | Pie de foto o sección de créditos |
| ❌ Evitar descargar de Google Imágenes | Sin permiso explícito del autor |
| ❌ Evitar recursos de redes sociales | Los derechos pertenecen al creador original |

### Recursos Utilizados en FintechPro

| Recurso | Fuente | Licencia |
|:---|:---|:---|
| Iconos de interfaz | [Heroicons](https://heroicons.com/) | MIT License |
| Tipografía Poppins | [Google Fonts](https://fonts.google.com/specimen/Poppins) | Open Font License (OFL) |
| Mockups de interfaz | Capturas propias de la aplicación | Contenido propio |
| Badges e indicadores | [Shields.io](https://shields.io/) | CC0 (Dominio público) |

---

## 6. 🛠️ Herramientas Recomendadas

| Herramienta | Tipo | Uso | Enlace |
|:---|:---|:---|:---|
| **Squoosh** | Web App | Compresión y conversión de imágenes (WebP, AVIF, PNG) | https://squoosh.app/ |
| **TinyPNG** | Web App | Compresión sin pérdida de PNG y JPG | https://tinypng.com/ |
| **HandBrake** | Desktop | Conversión y optimización de vídeo (MP4, WebM) | https://handbrake.fr/ |
| **FFmpeg** | CLI | Procesamiento de audio y vídeo por línea de comandos | https://ffmpeg.org/ |
| **Sharp** | Node.js | Procesamiento programático de imágenes en el pipeline de build | https://sharp.pixelplumbing.com/ |
| **ImageMagick** | CLI | Redimensionado y conversión batch de imágenes | https://imagemagick.org/ |

---

## 7. 📂 Organización de Archivos

Los archivos multimedia del proyecto están organizados para evidenciar el proceso de optimización:

```
resources/multimedia/
├── original/                     ← Archivos originales sin procesar
│   ├── hero-mockup.png           (574 KB)
│   ├── dashboard.png             (231 KB)
│   ├── portfolio.png             (85 KB)
│   └── social.png                (192 KB)
│
└── optimized/                    ← Archivos optimizados para producción
    ├── hero-mockup-opt.png       (optimizado)
    ├── dashboard-opt.png         (optimizado)
    ├── portfolio-opt.png         (optimizado)
    └── social-opt.png            (optimizado)
```

| Directorio | Propósito |
|:---|:---|
| [`resources/multimedia/original/`](../resources/multimedia/original/) | Archivos en su estado original para documentar el proceso de optimización |
| [`resources/multimedia/optimized/`](../resources/multimedia/optimized/) | Versiones procesadas y listas para producción |
| [`public/images/`](../public/images/) | Archivos servidos directamente por el servidor web |

---

<div align="center">

*Manual de Buenas Prácticas Multimedia v1.0 — Proyecto FintechPro · DAW 2025/2026*

</div>

# Sistema de Diseño - Farmer Auto

> **Filosofía**: "Cozy Futuristic". Una mezcla entre la calidez de los juegos de granja (Stardew Valley) y la limpieza de la ciencia ficción optimista (Solarpunk).

## 1. Colores

### Paleta Principal (Modo Oscuro - Default)
| Nombre | Color | Hex | Uso |
|--------|-------|-----|-----|
| **Deep Space** | Slate-950 | `#020617` | Fondo principal, inmersión. |
| **Glass Base** | Slate-900 | `#0f172a` | Paneles, tarjetas (con opacidad). |
| **Bio Life** | Emerald-500 | `#10b981` | Éxito, vida, cultivos, "Run". |
| **Tech Data** | Cyan-400 | `#22d3ee` | Datos, cursores, selección. |

### Paleta Secundaria (Acentos)
| Nombre | Color | Hex | Uso |
|--------|-------|-----|-----|
| **Warning Amber** | Amber-400 | `#fbbf24` | Alertas, Drones exploradores. |
| **Magic Pink** | Fuchsia-400 | `#e879f9` | Elementos especiales, Cosechadoras. |
| **Error Red** | Red-400 | `#f87171` | Errores de sintaxis, fallos. |

## 2. Tipografía

### UI / Textos
**Familia**: [Quicksand](https://fonts.google.com/specimen/Quicksand)
*   **Pesos**: Light (300), Regular (400), Bold (700).
*   **Estilo**: Redondeada, geométrica pero humana.

### Código / Datos
**Familia**: [Fira Code](https://fonts.google.com/specimen/Fira+Code)
*   **Pesos**: Regular (400), Medium (500).
*   **Características**: Ligaduras de programación (`=>`, `===`, `!=`).

## 3. UI Kit (Componentes)

### Botones
*   **Primario**: Gradiente `bg-gradient-to-r from-emerald-500 to-teal-500`. Texto Blanco. `rounded-xl`. Sombra `shadow-lg shadow-emerald-500/20`.
*   **Secundario**: Fondo transparente `bg-white/5`. Borde `border-white/10`. Hover `bg-white/10`.
*   **Icono**: `p-2 rounded-lg`. Hover con `scale-110`.

### Tarjetas (Glassmorphism)
```css
.glass-panel {
  @apply bg-slate-900/60 backdrop-blur-md border border-white/5 shadow-xl;
}
```

### Inputs / Editor
*   Fondo transparente para integrarse con el glassmorphism.
*   Bordes sutiles que brillan al foco (`focus:ring-2 ring-emerald-500/50`).

## 4. Iconografía
*   **Emojis**: Usados para añadir color y carácter (🌱, ⚡, 💎).
*   **SVG (Heroicons)**: Para controles de interfaz (Cerrar, Menú, Play). Estilo "Outline" con trazo de 2px.

## 5. Feedback Visual (Juice)
*   **Hover**: Elevación suave (`-translate-y-0.5`).
*   **Click**: Efecto de pulsación (`scale-95`).
*   **Notificaciones (Toasts)**: Deslizar desde la derecha, con iconos de color y fondo blur.
*   **Carga**: Spinners suaves, barras de progreso pulsantes.

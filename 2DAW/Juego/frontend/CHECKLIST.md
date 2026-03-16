# Diseño Visual - Farmer Auto

## Sistema de Diseño (Design System)

### 1. Paleta de Colores (Cozy Futuristic)

**Primarios (Naturaleza Tech)**
- `Emerald-500` (#10b981): Vida, éxito, crecimiento.
- `Cyan-400` (#22d3ee): Tecnología, energía, datos.

**Secundarios (Acentos)**
- `Amber-400` (#fbbf24): Advertencias, drones exploradores, luz cálida.
- `Fuchsia-400` (#e879f9): Cosechadoras, elementos especiales, magia tecnológica.

**Neutros (Glassmorphism)**
- `Slate-950` (#020617): Fondo principal (Deep Space).
- `Slate-900` (#0f172a): Paneles y tarjetas.
- `White/10` y `White/5`: Bordes y fondos translúcidos.

### 2. Tipografía

**Interfaz (UI)**
- **Familia**: 'Quicksand', sans-serif.
- **Características**: Redondeada, amigable, moderna, legible.
- **Uso**: Títulos, botones, textos descriptivos.

**Código**
- **Familia**: 'Fira Code', monospace.
- **Características**: Ligaduras (=>, !=), clara, técnica.
- **Uso**: Editor de código, consola, valores numéricos.

### 3. Componentes Visuales

**Glassmorphism**
- Paneles con `backdrop-blur-md` o `backdrop-blur-xl`.
- Bordes sutiles `border-white/10`.
- Sombras suaves `shadow-2xl`.

**Botones**
- Gradientes sutiles (`from-emerald-500 to-teal-500`).
- Efectos de brillo (shine) al pasar el mouse.
- Transformaciones suaves (`scale`, `translate`).

**Iconografía**
- Emojis minimalistas o iconos SVG de línea (Heroicons).
- Uso consistente de iconos para acciones (Play, Reset, Close).

### 4. Animaciones

- **Micro-interacciones**: Hover scale en tarjetas, botones.
- **Transiciones**: Fade in/out, Slide panels.
- **Feedback**: Vibración en error, pulso en éxito.

---

## Lista de Verificación de Calidad (Checklist)

### Estética y UI
- [x] La paleta de colores es coherente en toda la aplicación.
- [x] El texto es legible con suficiente contraste (cumple WCAG AA).
- [x] Los elementos interactivos tienen estados hover/active/focus claros.
- [x] El espaciado (padding/margin) es consistente (sistema de grilla de 4px/8px).
- [x] El estilo Glassmorphism no compromete la legibilidad.

### Experiencia de Usuario (UX)
- [x] El usuario recibe feedback inmediato al ejecutar acciones (Toast, Sonidos).
- [x] Las animaciones no son intrusivas ni causan mareo (respetan prefers-reduced-motion).
- [x] La navegación es intuitiva (Menú, Pestañas, Botones claros).
- [x] El editor de código tiene resaltado de sintaxis y autocompletado útil.

### Responsividad
- [x] El diseño se adapta a pantallas móviles (layout vertical).
- [x] El diseño funciona en pantallas grandes (layout horizontal de 3 columnas).
- [x] Los elementos táctiles tienen tamaño suficiente (>44px) en móvil.

### Performance
- [x] Los assets (SVG) son ligeros y vectoriales (no pixelados).
- [x] Las animaciones usan propiedades transform/opacity para 60fps.
- [x] El juego carga rápido y mantiene el framerate.

### Pulido Final
- [x] Scrollbars personalizados para encajar con el tema.
- [x] Tooltips o títulos en botones de iconos.
- [x] Sonidos agradables y no repetitivos.
- [x] Modo oscuro/claro implementado correctamente.

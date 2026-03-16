# Wireframes - Farmer Auto

## 1. Pantalla Principal (Game View)

### Desktop (Landscape)
```
+---------------------------------------------------------------+
|  [Logo] Farmer Auto          [Theme] [Sound] [Ranking] [Tech] |  <- Header (Glassmorphism)
+---------------------------------------------------------------+
| +----------------+  +---------------------------------------+ |
| | Sidebar (30%)  |  | Game Canvas (70%)                     | |
| |                |  |                                       | |
| | [Tabs: Code/   |  | +-----------------------------------+ | |
| |        Files]  |  | |                                   | | |
| |                |  | |        [ MAPA DE GRANJA ]         | | |
| | +------------+ |  | |                                   | | |
| | | Editor     | |  | |        (PixiJS Renderer)          | | |
| | | Monaco     | |  | |                                   | | |
| | |            | |  | +-----------------------------------+ | |
| | +------------+ |  |                                       | |
| |                |  | +-----------------------------------+ | |
| | [Ejecutar >]   |  | | HUD (Overlay)                     | | |
| | [Reiniciar]    |  | | [Recursos] [Objetivos]            | | |
| |                |  | +-----------------------------------+ | |
| +----------------+  +---------------------------------------+ |
+---------------------------------------------------------------+
| [Console Log Panel]                                           |
+---------------------------------------------------------------+
```

### Mobile (Portrait)
```
+---------------------------------------+
| [Logo] [Menu]                         | <- Header Compacto
+---------------------------------------+
| +-----------------------------------+ |
| | Game Canvas (Square)              | |
| |                                   | |
| |                                   | |
| +-----------------------------------+ |
+---------------------------------------+
| [Tabs: Editor | Consola | Archivos]   |
+---------------------------------------+
| +-----------------------------------+ |
| | Editor de Código (Monaco)         | |
| |                                   | |
| |                                   | |
| +-----------------------------------+ |
+---------------------------------------+
| [Botón Ejecutar Grande (Floating)]    |
+---------------------------------------+
```

## 2. Árbol de Tecnologías (Modal)
```
+-------------------------------------------------------+
| Título: Tecnologías                  [X] Cerrar       |
+-------------------------------------------------------+
|                                                       |
|        [Bucles] ----> [Funciones]                     |
|           |                                           |
|           v                                           |
|      [Variables] ---> [Sensores] ---> [Optimización]  |
|                                                       |
| (Nodos interactivos con líneas curvas animadas)       |
|                                                       |
+-------------------------------------------------------+
| Detalle de Selección (Slide-in Panel)                 |
| [Icono Grande] Nombre                                 |
| Descripción...                                        |
| Costo: $500                                           |
| [Botón: Desbloquear]                                  |
+-------------------------------------------------------+
```

## 3. Componentes UI Clave

### Tarjeta de Recurso (HUD)
```
+------------------+
| Icono | Cantidad |
| [⚡] | 150/200  |
+------------------+
(Glassmorphism, borde sutil, glow al cambiar valor)
```

### Botón Principal (Primary Action)
```
+-------------------------------------+
|  [Icono] Texto (Ej. Ejecutar)       |
+-------------------------------------+
(Gradiente Emerald->Teal, Sombra suave, Efecto Shine)
```

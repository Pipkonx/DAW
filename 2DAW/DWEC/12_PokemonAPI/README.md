# 🔴 Pokédex - Explorador de Pokémon

Una aplicación web interactiva que consume la PokéAPI para mostrar información detallada de Pokémon con funcionalidades de búsqueda, filtrado por tipos y navegación paginada.

## 🎯 Características principales

- **Catálogo completo**: Visualización de Pokémon obtenidos de la PokéAPI
- **Búsqueda inteligente**: Por nombre o ID del Pokémon
- **Filtrado por tipos**: Clasificación por tipos de Pokémon (fuego, agua, planta, etc.)
- **Carga paginada**: Sistema de "Cargar más" para optimizar rendimiento
- **Diseño temático**: Colores dinámicos basados en el tipo principal de cada Pokémon
- **Interfaz responsive**: Adaptable a diferentes tamaños de pantalla

## 🚀 Funcionalidades

### Navegación y búsqueda
- Búsqueda en tiempo real por nombre o número de ID
- Filtrado por 18 tipos diferentes de Pokémon
- Botón "Reestablecer" para limpiar filtros y búsquedas
- Carga progresiva de 20 Pokémon por página

### Visualización de datos
- Tarjetas con información esencial: ID, imagen, nombre y tipos
- Colores de fondo dinámicos según el tipo principal
- Numeración formateada con ceros a la izquierda (ej: #001, #025)
- Sprites oficiales de cada Pokémon

### Gestión de tipos
- 18 tipos de Pokémon con colores característicos
- Filtrado específico por tipo con carga optimizada
- Visualización de múltiples tipos por Pokémon

## 🛠️ Tecnologías utilizadas

- **HTML5**: Estructura semántica y accesible
- **CSS3**: Estilos modernos con Grid Layout y gradientes
- **JavaScript ES6+**: Lógica de aplicación con async/await
- **PokéAPI**: Fuente oficial de datos de Pokémon
- **Fetch API**: Peticiones HTTP asíncronas

## 📁 Estructura del proyecto

```
12_Pokemon/
├── index.html          # Página principal
├── main.js            # Lógica de la aplicación
├── style.css          # Estilos de la interfaz
└── README.md          # Este archivo
```

## 🎨 Características de diseño

- **Grid responsive**: Diseño adaptable con auto-fit
- **Colores temáticos**: 18 colores únicos para cada tipo de Pokémon
- **Gradientes**: Fondos con degradados suaves
- **Tipografía**: Fuente Poppins para mejor legibilidad
- **Sombras**: Efectos de profundidad en las tarjetas

## 🎨 Paleta de colores por tipo

| Tipo | Color | Tipo | Color |
|------|-------|------|-------|
| Normal | #A8A77A | Fuego | #EE8130 |
| Agua | #6390F0 | Eléctrico | #F7D02C |
| Planta | #7AC74C | Hielo | #96D9D6 |
| Lucha | #C22E28 | Veneno | #A33EA1 |
| Tierra | #E2BF65 | Volador | #A98FF3 |
| Psíquico | #F95587 | Bicho | #A6B91A |
| Roca | #B6A136 | Fantasma | #735797 |
| Dragón | #6F35FC | Siniestro | #705746 |
| Acero | #B7B7CE | Hada | #D685AD |

## 🔧 Instalación y uso

### Opción 1: Live Server (Recomendado)
1. Abre el proyecto en VS Code
2. Instala la extensión "Live Server"
3. Clic derecho en `index.html` → "Open with Live Server"

### Opción 2: Servidor local
```bash
# Navega al directorio del proyecto
cd 12_Pokemon

# Inicia un servidor HTTP simple
python -m http.server 8000
# o con Node.js
npx http-server -p 8000

# Visita: http://localhost:8000
```

### Opción 3: Abrir directamente
- Abre `index.html` directamente en el navegador
- ✅ Funciona completamente sin servidor local

## 📋 APIs utilizadas

La aplicación consume la **PokéAPI**:
- **Pokémon**: `https://pokeapi.co/api/v2/pokemon/`
- **Tipos**: `https://pokeapi.co/api/v2/type/`
- **Datos**: Información completa de cada Pokémon (sprites, tipos, estadísticas)

## 🎮 Cómo usar la aplicación

1. **Explorar Pokémon**: Los primeros 20 Pokémon se cargan automáticamente
2. **Buscar**: Escribe el nombre o ID en el campo de búsqueda
3. **Filtrar por tipo**: Selecciona un tipo del menú desplegable
4. **Cargar más**: Haz clic en "Cargar más Pokémon" para ver más resultados
5. **Reestablecer**: Usa el botón "Reestablecer" para limpiar filtros

## 🔍 Funciones JavaScript principales

- `cargarPokemons()`: Carga Pokémon de forma paginada
- `cargarTipos()`: Obtiene todos los tipos disponibles
- `cargarPorTipo()`: Filtra Pokémon por tipo específico
- `crearCard()`: Genera tarjetas HTML para cada Pokémon
- `mostrarPokemons()`: Renderiza la lista filtrada
- Event listeners para búsqueda, filtros y navegación

## 🎯 Características técnicas

### Gestión de estado
- `todosLosPokemons[]`: Array con todos los Pokémon cargados
- `pokemonsPorTipo[]`: Array filtrado por tipo actual
- `offset` y `offsetTipo`: Control de paginación
- `cantidadPorPagina`: Límite de resultados por carga (20)

### Optimizaciones
- Carga paginada para mejorar rendimiento
- Búsqueda en tiempo real sin peticiones adicionales
- Filtrado local después de la carga inicial
- Gestión eficiente de eventos

### Responsive Design
- Grid adaptable con `auto-fit` y `minmax()`
- Breakpoints implícitos para diferentes pantallas
- Imágenes optimizadas de la PokéAPI

## 🚨 Requisitos

- Navegador moderno con soporte para ES6+
- Conexión a internet para cargar datos de la PokéAPI
- JavaScript habilitado

## 📝 Notas de desarrollo

- Los datos se cargan de forma asíncrona con `async/await`
- La búsqueda funciona tanto con nombres como con IDs
- Los colores se asignan dinámicamente según el tipo principal
- La aplicación es completamente del lado del cliente
- No requiere instalación de dependencias

## 🔄 Flujo de la aplicación

1. **Inicialización**: Carga tipos y primeros 20 Pokémon
2. **Búsqueda**: Filtra resultados localmente en tiempo real
3. **Filtro por tipo**: Carga Pokémon específicos del tipo seleccionado
4. **Paginación**: Carga más resultados según el contexto actual
5. **Reset**: Vuelve al estado inicial con todos los filtros limpiados

---

💡 **Tip**: Usa las herramientas de desarrollo del navegador (F12) para inspeccionar las peticiones a la PokéAPI y explorar la estructura de datos de cada Pokémon.
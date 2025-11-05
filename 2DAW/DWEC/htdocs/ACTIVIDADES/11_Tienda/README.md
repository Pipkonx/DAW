# 🛒 Pipkon Shop - Tienda Online

Una aplicación web completa de tienda online que consume la API de FakeStore para mostrar productos de ropa con funcionalidades de carrito de compras, filtrado y gestión de stock.

## 🎯 Características principales

- **Catálogo de productos**: Visualización de productos obtenidos de FakeStore API
- **Sistema de filtrado**: Por categorías y ordenamiento por precio
- **Carrito de compras**: Agregar, eliminar y gestionar cantidades
- **Gestión de stock**: Control de inventario en tiempo real
- **Interfaz responsive**: Diseño adaptable a diferentes dispositivos
- **Video hero**: Sección principal con video de fondo

## 🚀 Funcionalidades

### Navegación y filtros
- Filtrado por categorías de productos
- Ordenamiento ascendente y descendente por precio
- Botón para limpiar todos los filtros aplicados
- Búsqueda y visualización dinámica de productos

### Gestión de productos
- Visualización de información completa: imagen, título, precio, descripción
- Sistema de valoraciones con estrellas
- Control de stock disponible
- Eliminación de productos del catálogo

### Carrito de compras
- Agregar productos con cantidad seleccionable
- Modificar cantidades desde el carrito
- Eliminar productos del carrito
- Cálculo automático del total
- Validación de stock disponible

## 🛠️ Tecnologías utilizadas

- **HTML5**: Estructura semántica
- **CSS3**: Estilos modernos con variables CSS y flexbox
- **JavaScript ES6+**: Lógica de aplicación con fetch API
- **FakeStore API**: Fuente de datos de productos
- **Video HTML5**: Elemento multimedia para hero section

## 📁 Estructura del proyecto

```
11_Tienda/
├── index.html          # Página principal
├── main.js            # Lógica de la aplicación
├── style.css          # Estilos de la interfaz
├── favicon.png        # Icono de la aplicación
├── subir.png          # Icono del botón "subir"
├── videoplayback.mp4  # Video de fondo del hero
└── README.md          # Este archivo
```

## 🎨 Características de diseño

- **Navbar sticky**: Navegación fija en la parte superior
- **Hero section**: Video de fondo con overlay de texto
- **Grid responsive**: Diseño de tarjetas adaptable
- **Colores temáticos**: Esquema de colores consistente
- **Botón "subir"**: Navegación rápida al inicio

## 🔧 Instalación y uso

### Opción 1: Live Server (Recomendado)
1. Abre el proyecto en VS Code
2. Instala la extensión "Live Server"
3. Clic derecho en `index.html` → "Open with Live Server"

### Opción 2: Servidor local
```bash
# Navega al directorio del proyecto
cd 11_Tienda

# Inicia un servidor HTTP simple
python -m http.server 8000
# o con Node.js
npx http-server -p 8000

# Visita: http://localhost:8000
```

### Opción 3: Abrir directamente
- Abre `index.html` directamente en el navegador
- ⚠️ Algunas funciones pueden no trabajar por restricciones CORS

## 📋 API utilizada

La aplicación consume la **FakeStore API**:
- **Endpoint**: `https://fakestoreapi.com/products`
- **Datos**: Productos con información completa (título, precio, descripción, imagen, categoría, valoración)

## 🎮 Cómo usar la aplicación

1. **Explorar productos**: Los productos se cargan automáticamente al abrir la página
2. **Filtrar por categoría**: Usa el selector de categorías en la navbar
3. **Ordenar**: Selecciona ordenamiento ascendente o descendente por precio
4. **Agregar al carrito**: Selecciona cantidad y haz clic en "Agregar"
5. **Ver carrito**: Haz clic en el botón "Carrito" para ver los productos agregados
6. **Gestionar carrito**: Modifica cantidades o elimina productos desde el carrito

## 🔍 Funciones JavaScript principales

- `cargarProductos()`: Obtiene productos de la API
- `cargarCat()`: Carga las categorías disponibles
- `filtrarCat()`: Aplica filtro por categoría
- `ordenar()`: Ordena productos por precio
- `agregarAlCarrito()`: Añade productos al carrito
- `mostrarCarrito()`: Muestra el modal del carrito
- `calcularTotalCarrito()`: Calcula el total de la compra

## 🎯 Características técnicas

- **Gestión de estado**: Variables globales para productos, stock y carrito
- **Manipulación DOM**: Creación dinámica de elementos
- **Eventos**: Listeners para interacciones del usuario
- **Validaciones**: Control de stock y cantidades
- **Responsive**: Diseño adaptable con CSS Grid y Flexbox

## 🚨 Requisitos

- Navegador moderno con soporte para ES6+
- Conexión a internet para cargar productos de la API
- JavaScript habilitado

## 📝 Notas de desarrollo

- El stock se inicializa con el valor `rating.count` de cada producto
- El carrito se gestiona en memoria (se pierde al recargar)
- Los productos eliminados se quitan del DOM pero no afectan la API
- La aplicación es completamente del lado del cliente

---

💡 **Tip**: Usa las herramientas de desarrollo del navegador (F12) para inspeccionar las peticiones a la API y el estado de la aplicación.
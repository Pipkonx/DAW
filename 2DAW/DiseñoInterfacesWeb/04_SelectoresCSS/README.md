# 🎨 Selectores CSS

Este directorio contiene ejercicios prácticos sobre selectores CSS, fundamentales para aplicar estilos a elementos HTML de manera eficiente y estructurada.

## 📋 Contenido

El objetivo de estos ejercicios es aprender a:

- Utilizar diferentes tipos de selectores CSS
- Aplicar estilos a elementos HTML específicos
- Crear reglas CSS efectivas para mejorar la presentación de páginas web

## 🗂️ Archivos del Directorio

### Ejercicios

- [**01-copiarAspecto.html**](./01-copiarAspecto.html): Ejercicio para replicar un aspecto visual utilizando selectores CSS
- [**02-añadirReglasCss.html**](./02-añadirReglasCss.html): Ejercicio para añadir reglas CSS a elementos HTML específicos
- [**03-tablaCSS.html**](./03-tablaCSS.html): Ejercicio para aplicar estilos CSS a tablas HTML

## 📚 Tipos de Selectores CSS

- **Selectores básicos**:

  - Selector de tipo: `p { color: blue; }`
  - Selector de clase: `.miClase { font-size: 16px; }`
  - Selector de ID: `#miID { background-color: yellow; }`
  - Selector universal: `* { margin: 0; padding: 0; }`

- **Selectores combinadores**:

  - Descendiente: `div p { color: red; }`
  - Hijo directo: `div > p { color: green; }`
  - Hermano adyacente: `h1 + p { font-weight: bold; }`
  - Hermanos generales: `h1 ~ p { text-decoration: underline; }`

- **Selectores de atributos**:
  - `[attr]`: Elementos con el atributo attr
  - `[attr=valor]`: Elementos con attr="valor"
  - `[attr^=valor]`: Elementos con attr que comienza con "valor"
  - `[attr$=valor]`: Elementos con attr que termina con "valor"
  - `[attr*=valor]`: Elementos con attr que contiene "valor"

## 🔗 Enlaces de Interés

- [W3Schools - CSS Selectors](https://www.w3schools.com/css/css_selectors.asp)
- [MDN Web Docs - Selectores CSS](https://developer.mozilla.org/es/docs/Web/CSS/CSS_Selectors)
- [CSS-Tricks - Guía de Selectores CSS](https://css-tricks.com/almanac/selectors/)

## 🔙 Navegación

- [Volver al índice principal](../README.MD)

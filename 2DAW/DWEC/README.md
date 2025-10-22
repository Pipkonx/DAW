# 🌐 Desarrollo Web en Entorno Cliente (DWEC)

Colección de actividades y prácticas del módulo DWEC de 2º DAW. Ejercicios progresivos para dominar HTML, CSS y JavaScript en el navegador.

## 🎯 Objetivos del módulo

- Manejar el DOM y los eventos del navegador
- Validar formularios y gestionar datos con JSON
- Practicar lógica de programación y buenas prácticas con JavaScript moderno
- Aplicar estilos y accesibilidad en interfaces web

## 📁 Actividades disponibles

### Fundamentos y sintaxis
- **[01_EjerciciosBasicos](./01_EjerciciosBasicos/)** - Sintaxis de JS, variables, funciones, condicionales y bucles
- **[02_Ejercicio_DOM](./02_Ejercicio_DOM/)** - Selección y manipulación de elementos del DOM, eventos

### Interacción y formularios
- **[03_Mostrar-Ocultar](./03_Mostrar-Ocultar/)** - Interacción con clases y estilos para mostrar/ocultar elementos
- **[04_Formularios](./04_Formularios/)** - Envío, control y tratamiento de datos de formularios
- **[05_Seleccion_Multiple](./05_Seleccion_Multiple/)** - Listas, selección múltiple y gestión de opciones

### Aplicaciones prácticas
- **[06_factura](./06_factura/)** - Cálculo de totales, IVA y descuentos
- **[07_BuscarArticulo](./07_BuscarArticulo/)** - Filtrado y búsqueda en colecciones de datos
- **[08_ValiacionFormulario](./08_ValiacionFormulario/)** - Validación de campos en cliente
- **[09_FValidaciondeRegistro](./09_FValidaciondeRegistro/)** - Validación de registros completos

### Datos y APIs
- **[10_JsonApiRest](./10_JsonApiRest/)** - Lectura y procesamiento de datos en formato JSON
- **[11_Tienda](./11_Tienda/)** - Aplicación completa de tienda online

### Repaso
- **[99_Repaso](./99_Repaso/)** - Ejercicios de refuerzo y resumen

## 🚀 Cómo probar las actividades

### Opción 1: VS Code + Live Server (Recomendado)
1. Abre la carpeta de la actividad en VS Code
2. Instala la extensión "Live Server" si no la tienes
3. Clic derecho en `index.html` → "Open with Live Server"
4. Se abrirá automáticamente en el navegador con recarga automática

### Opción 2: Servidor Python
```bash
# Navega a la carpeta de la actividad
cd ruta/a/la/actividad
# Inicia servidor (Python 3)
python -m http.server 8000
# Visita: http://localhost:8000
```

### Opción 3: Servidor Node.js
```bash
# Instala http-server globalmente (una sola vez)
npm install -g http-server
# En la carpeta de la actividad
npx http-server -p 8000
# Visita: http://localhost:8000
```

### Opción 4: Abrir directamente
- Simplemente abre el archivo `index.html` en tu navegador
- ⚠️ Algunas funcionalidades pueden no funcionar por restricciones CORS

## 🛠️ Requisitos técnicos

- **Navegador moderno**: Chrome, Firefox, Edge o Safari
- **Editor de código**: VS Code (recomendado), Sublime Text, Atom
- **Node.js** (opcional): Para herramientas de desarrollo adicionales

## 📋 Estructura típica de cada actividad

```
actividad/
├── index.html          # Archivo principal
├── style.css          # Estilos (si aplica)
├── script.js          # Lógica JavaScript
├── README.md          # Instrucciones específicas (si existe)
└── assets/            # Recursos adicionales (imágenes, etc.)
```

## ✅ Convenciones y buenas prácticas

- **JavaScript moderno**: `let/const`, funciones flecha, destructuring
- **Nombres descriptivos**: Variables y funciones con nombres claros
- **Validaciones**: Manejo de errores en formularios y entrada de datos
- **Comentarios**: Solo donde aporten claridad real
- **Accesibilidad**: Etiquetas semánticas y atributos ARIA cuando sea necesario

## 🔧 Depuración y desarrollo

1. **Consola del navegador**: `F12` → Console para ver errores y logs
2. **Elementos**: `F12` → Elements para inspeccionar DOM y CSS
3. **Network**: Para revisar peticiones HTTP y respuestas
4. **Breakpoints**: Usa `debugger;` en el código o puntos de interrupción en DevTools

## 📚 Recursos recomendados

- **[MDN Web Docs](https://developer.mozilla.org/es/)** - Documentación oficial de JavaScript
- **[JavaScript.info](https://es.javascript.info/)** - Tutorial completo y actualizado
- **[Web.dev](https://web.dev/learn/)** - Guías de Google para desarrollo web
- **[Can I Use](https://caniuse.com/)** - Compatibilidad de características web

## 📝 Notas importantes

- Cada actividad incluye su propio `index.html` como punto de entrada
- Revisa la consola del navegador si algo no funciona como esperado
- Algunas actividades pueden requerir archivos JSON o APIs externas
- Los enlaces son relativos y funcionan tanto en GitHub como en VS Code

## 🤝 Contribuciones

Si encuentras mejoras o errores:
1. Abre un issue describiendo el problema
2. Propón cambios mediante pull request
3. Asegúrate de que el código sigue las convenciones establecidas

---

💡 **Tip**: Usa las herramientas de desarrollo del navegador (`F12`) para experimentar y aprender más sobre cada ejercicio.

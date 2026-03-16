# Proyecto de Automatización Agrícola - Fase 2

Este proyecto sigue una arquitectura modular basada en **Vue 3 (Frontend)** y **Laravel (Backend)**, aplicando patrones de diseño profesionales como **MVC** y principios **SOLID**.

## 🏗️ Arquitectura y Estructura de Carpetas

La estructura del proyecto está diseñada para ser escalable y mantenible.

### Frontend (Vue 3)
```
frontend/src/
├── assets/          # Recursos estáticos (imágenes, iconos)
├── components/      # Componentes UI reutilizables (Botones, Modales, HUD)
│   ├── CodeEditor.vue
│   ├── Sidebar.vue
│   └── ...
├── composables/     # Lógica de negocio reactiva (Hooks/Controllers)
│   └── useGameController.js  # Lógica principal del juego (Controller)
├── game/            # Modelos y Lógica pura del juego (Models)
│   ├── GameEngine.js         # Motor de simulación
│   ├── Robot.js              # Entidad Robot
│   └── Grid.js               # Entidad Mapa
├── services/        # Servicios externos (API, WebSockets)
│   └── ApiService.js         # (Pendiente) Comunicación con Laravel
├── stores/          # Gestión de Estado Global (Pinia)
│   ├── tutorial.js
│   └── filesystem.js
├── utils/           # Utilidades puras (Helpers)
│   └── pyodide.js
└── views/           # Vistas principales (Páginas)
    └── GameView.vue          # Vista principal del juego
```

### Backend (Laravel - Recomendado)
```
backend/app/
├── Http/Controllers/ # Controladores delgados (Manejan peticiones HTTP)
├── Models/           # Modelos Eloquent (Datos)
├── Services/         # Lógica de negocio compleja
├── Repositories/     # Abstracción de acceso a datos
└── ...
```

## 🧩 Patrón MVC Aplicado

Hemos separado las responsabilidades en el Frontend para evitar componentes gigantes ("God Components"):

1.  **Model (Modelo)**:
    *   Ubicación: `src/game/`
    *   Ejemplo: `GameEngine.js`, `Robot.js`.
    *   Responsabilidad: Mantener el estado de la simulación y ejecutar reglas de negocio puras (moverse, recolectar). No sabe nada de Vue ni del DOM.

2.  **View (Vista)**:
    *   Ubicación: `src/views/` y `src/components/`
    *   Ejemplo: `GameView.vue`, `Sidebar.vue`.
    *   Responsabilidad: Mostrar datos al usuario y capturar eventos. **No contiene lógica compleja**.
    *   Refactorización: `GameView.vue` ahora solo renderiza componentes y delega la lógica al Controller.

3.  **Controller (Controlador)**:
    *   Ubicación: `src/composables/`
    *   Ejemplo: `useGameController.js`.
    *   Responsabilidad: Conectar la Vista con el Modelo. Maneja el estado reactivo (`isRunning`, `logs`), inicia el motor (`new GameEngine`), y responde a eventos del usuario (`runCode`, `reset`).

## 📚 Documentación Adicional

*   [**Especificaciones Backend (Laravel)**](docs/BACKEND_SPECS.md): Guía detallada para implementar la API.
*   [**Consejos de Escalabilidad**](docs/SCALABILITY.md): Recomendaciones para crecer el proyecto sin deuda técnica.

## ✅ Checklist de Buenas Prácticas

- [ ] **Idioma**: Código en inglés técnico, Comentarios y UI en **Castellano**.
- [ ] **Tamaño**: Archivos menores a 300 líneas. Si crece, extraer lógica a `composables` o `utils`.
- [ ] **Responsabilidad Única (SRP)**: Cada archivo/función hace una sola cosa bien.
- [ ] **No "Magic Numbers"**: Usar constantes (`const MAX_ENERGY = 100`).
- [ ] **Documentación**: Comentar el "por qué", no el "qué", en lógica compleja.
- [ ] **Gestión de Errores**: Usar `try/catch` en operaciones asíncronas y mostrar feedback al usuario.

## 🛠️ Herramientas de Calidad (Instalación)

Para mantener el código limpio automáticamente, recomendamos instalar ESLint y Prettier.

Ejecuta este comando en la carpeta `frontend`:
```bash
npm install -D eslint eslint-plugin-vue prettier @vue/eslint-config-prettier
```

Y añade estos scripts a tu `package.json`:
```json
"scripts": {
  "lint": "eslint . --ext .vue,.js,.jsx,.cjs,.mjs --fix --ignore-path .gitignore",
  "format": "prettier --write src/"
}
```

## 🚀 Consejos para Escalar

1.  **Tipado Fuerte**: Migrar a **TypeScript** en la Fase 3 para evitar errores de tipos en tiempo de ejecución.
2.  **Tests Automatizados**: Implementar Vitest para probar `GameEngine.js` sin necesidad de levantar el navegador.
3.  **Feature Flags**: Usar un sistema de configuración para habilitar/deshabilitar funcionalidades (como los nuevos drones) sin romper el código base.

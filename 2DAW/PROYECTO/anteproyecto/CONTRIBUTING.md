# Guía de Contribución — FintechPro

> Gracias por dedicar tu tiempo a mejorar FintechPro.
> Cada contribución, por pequeña que sea, hace el proyecto más sólido.

---

## 📋 Tabla de Contenidos

- [Antes de Empezar](#-antes-de-empezar)
- [Flujo de Trabajo](#-flujo-de-trabajo)
- [Convenciones de Commits](#-convenciones-de-commits)
- [Estándares de Código](#-estándares-de-código)
- [Pull Requests](#-pull-requests)
- [Reportar Errores](#-reportar-errores)
- [Vulnerabilidades de Seguridad](#-vulnerabilidades-de-seguridad)

---

## 🧭 Antes de Empezar

Antes de abrir un PR o un Issue, revisa lo siguiente:

- [ ] ¿Existe ya un [Issue abierto](https://github.com/Pipkonx/DAW/issues) que cubra lo que quieres aportar?
- [ ] ¿Has leído la [documentación del proyecto](README.md) y la arquitectura existente?
- [ ] ¿Tu cambio respeta los principios **SOLID** y **DRY** que rigen este proyecto?
- [ ] ¿La dependencia que quieres añadir es **estrictamente necesaria** y no puede resolverse con lo ya instalado?

Si tienes dudas antes de implementar algo, **abre un Issue de discusión primero**. Es mejor alinear criterios antes que rehacer trabajo.

---

## 🔀 Flujo de Trabajo

FintechPro sigue un flujo basado en **ramas por feature** sobre `main`:

```
main
 └── feature/nombre-descriptivo     ← Nueva funcionalidad
 └── fix/nombre-del-bug             ← Corrección de errores
 └── refactor/nombre-del-modulo     ← Refactorizaciones
 └── docs/seccion-actualizada       ← Solo documentación
```

### Pasos

```bash
# 1. Fork del repositorio y clonado local
git clone https://github.com/Pipkonx/DAW.git
cd fintechpro

# 2. Crear una rama con nombre descriptivo
git checkout -b feature/buscador-de-activos

# 3. Desarrollar con commits atómicos y descriptivos
git commit -m "feat(assets): agregar buscador con filtro por ticker y sector"

# 4. Mantener la rama actualizada con main
git fetch origin
git rebase origin/main

# 5. Push y abrir Pull Request
git push origin feature/buscador-de-activos
```

---

## 📝 Convenciones de Commits

Seguimos la especificación **[Conventional Commits](https://www.conventionalcommits.org/es/)**.  
El formato es: `tipo(ámbito): descripción corta en imperativo`

| Tipo | Cuándo usarlo |
|:---|:---|
| `feat` | Nueva funcionalidad visible para el usuario |
| `fix` | Corrección de un bug |
| `refactor` | Cambio de código que no añade ni corrige nada |
| `perf` | Mejora de rendimiento |
| `test` | Añadir o corregir tests |
| `docs` | Cambios en documentación |
| `chore` | Tareas de mantenimiento (deps, config, scripts) |
| `style` | Cambios de formato que no afectan la lógica |

**Ejemplos correctos:**

```
feat(ocr): añadir soporte para extractos de Degiro en PDF
fix(2fa): corregir validación de código TOTP tras cambio de zona horaria
refactor(MarketDataService): extraer lógica de fallback a clase dedicada
docs(README): actualizar sección de instalación con paso de Docker
```

**Reglas:**
- Descripción en **castellano**, en modo imperativo (`añadir`, no `añadido` ni `añade`)
- Sin punto final en la descripción corta
- Longitud máxima: **72 caracteres**
- Si el cambio es significativo, añade un cuerpo de commit explicando el **por qué**

---

## 🎨 Estándares de Código

### PHP / Laravel

- Estándar **[PSR-12](https://www.php-fig.org/psr/psr-12/)** obligatorio
- Toda la lógica de negocio en `app/Services` — **nunca** en controladores ni modelos
- Los controladores deben limitarse a orquestar: validar entrada, llamar al servicio, devolver respuesta
- Usar **Eloquent** con relaciones explícitas; evitar queries en crudo salvo casos justificados
- Los comentarios y docblocks en **castellano**

```php
// ✅ Correcto
public function update(UpdateAssetRequest $request, Asset $asset): JsonResponse
{
    $result = $this->assetService->update($asset, $request->validated());
    return response()->json($result);
}

// ❌ Incorrecto — lógica de negocio en el controlador
public function update(Request $request, int $id): JsonResponse
{
    $asset = Asset::find($id);
    $asset->price = $request->price * 1.02;
    $asset->save();
    // ...
}
```

### Vue 3 / JavaScript

- **ESLint** con la configuración del proyecto (`npm run lint` debe pasar sin errores)
- **Composition API** exclusivamente — no usar Options API en componentes nuevos
- Lógica reutilizable extraída a **Composables** en `resources/js/composables/`
- Props tipadas con `defineProps<{}>()` y emits declarados con `defineEmits`
- Nombres de componentes en **PascalCase**, archivos en **PascalCase.vue**

```vue
<!-- ✅ Correcto -->
<script setup lang="ts">
const props = defineProps<{
  ticker: string
  currentPrice: number
}>()
</script>

<!-- ❌ Incorrecto -->
<script>
export default {
  props: ['ticker', 'currentPrice'],
  // ...
}
</script>
```

### General

- **Sin código comentado** en los PRs — si algo está desactivado, explica por qué en el commit o en un comentario explícito
- **Sin `console.log`** ni `dd()` / `dump()` sin eliminar antes del PR
- Mantén los archivos **bajo las 300 líneas**; si superan ese umbral, es señal de que deben dividirse

---

## 🔍 Pull Requests

### Checklist antes de abrir un PR

- [ ] `php artisan test` pasa sin errores
- [ ] `npm run lint` pasa sin errores
- [ ] `npm run build` compila sin warnings críticos
- [ ] He actualizado el `README.md` si he añadido variables de entorno, rutas o funcionalidades nuevas
- [ ] El PR tiene un **título descriptivo** con formato Conventional Commits
- [ ] He rellenado la **plantilla de PR** (se carga automáticamente al abrirlo)
- [ ] He asignado el PR a mí mismo y añadido las etiquetas correspondientes

### Criterios de revisión

Un PR será revisado atendiendo a:

| Criterio | Descripción |
|:---|:---|
| **Corrección** | ¿El código hace lo que dice que hace? |
| **Arquitectura** | ¿Respeta la separación de responsabilidades del proyecto? |
| **Seguridad** | ¿Introduce algún vector de ataque o exposición de datos? |
| **Rendimiento** | ¿Genera queries N+1 o procesos innecesariamente costosos? |
| **Tests** | ¿Los cambios en servicios críticos están cubiertos? |
| **Legibilidad** | ¿Otro desarrollador puede entender el código sin preguntar? |

---

## 🐛 Reportar Errores

Si encuentras un bug, abre un **[Issue](https://github.com/Pipkonx/DAW/issues/new?template=bug_report.md)** incluyendo:

```
**Descripción del error**
Descripción clara y concisa de qué está fallando.

**Pasos para reproducirlo**
1. Ir a '...'
2. Hacer clic en '...'
3. Ver el error

**Comportamiento esperado**
Qué debería haber ocurrido.

**Capturas de pantalla / Logs**
Si aplica, añade capturas o el stack trace completo.

**Entorno**
- OS: [ej. Ubuntu 22.04]
- PHP: [ej. 8.3.2]
- Node: [ej. 20.11.0]
- Navegador: [ej. Chrome 123]
```

> ⚠️ **No incluyas credenciales, tokens ni datos personales en los Issues.**

---

## 🔐 Vulnerabilidades de Seguridad

Si has descubierto una vulnerabilidad de seguridad, **no abras un Issue público**.  
Hacerlo podría exponer a otros usuarios antes de que el problema esté resuelto.

Utiliza el canal privado de reporte responsable:

**📧 [Pipkon.proyectos@gmail.com](mailto:Pipkon.proyectos@gmail.com)**

Incluye en tu reporte:
- Descripción detallada de la vulnerabilidad
- Pasos para reproducirla
- Impacto potencial estimado
- Tu propuesta de solución (si la tienes)

Nos comprometemos a acusar recibo en **72 horas** y a comunicar el plan de acción en **7 días**.  
Tu nombre aparecerá en los créditos del release si así lo deseas.

---

<div align="center">

Desarrollado con 🧠 por **Rafael** — *Desarrollo de Aplicaciones Web (DAW)*

*¡Feliz codificación!*

</div>
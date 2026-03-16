# Especificaciones del Backend (Laravel)

Este documento detalla la arquitectura y requisitos para el desarrollo del backend en Laravel, alineado con el frontend Vue 3.

## 🏗️ Arquitectura Limpia

Se debe seguir una arquitectura por capas para mantener la escalabilidad y testabilidad.

### Estructura de Carpetas Recomendada

```
app/
├── Http/
│   ├── Controllers/    # Controladores (Manejo de Request/Response)
│   ├── Requests/       # Validaciones (FormRequests)
│   └── Resources/      # Transformación de datos (API Resources)
├── Models/             # Modelos Eloquent (Datos)
├── Services/           # Lógica de Negocio (Business Logic)
├── Repositories/       # Acceso a Datos (Data Access)
└── ...
```

## 🧩 Principios de Diseño

### 1. Controladores Delgados (Thin Controllers)
Los controladores **NO** deben contener lógica de negocio. Su única responsabilidad es:
1.  Recibir la petición (Request).
2.  Validar los datos (usando FormRequests).
3.  Delegar la lógica a un Servicio.
4.  Retornar la respuesta (usando API Resources).

**Ejemplo Incorrecto:**
```php
public function store(Request $request) {
    $validated = $request->validate([...]);
    $user = User::create($validated); // Lógica en controlador
    // ... más lógica ...
    return response()->json($user);
}
```

**Ejemplo Correcto:**
```php
public function store(StoreUserRequest $request, UserService $userService) {
    $user = $userService->registerUser($request->validated());
    return new UserResource($user);
}
```

### 2. Servicios (Services)
Aquí reside la lógica de negocio compleja.
*   Deben ser independientes del framework HTTP (no recibir `Request` ni devolver `Response`).
*   Deben lanzar excepciones en caso de error, que el controlador capturará.

### 3. Repositorios (Repositories) - Opcional pero Recomendado
Abstraen la capa de persistencia. Si mañana cambiamos MySQL por MongoDB, solo cambiamos el repositorio.
*   Métodos típicos: `find`, `create`, `update`, `delete`, `findByCriteria`.

### 4. Validaciones Separadas (Form Requests)
Nunca validar en el controlador. Usar clases `FormRequest`.
```bash
php artisan make:request StoreScoreRequest
```

## 🔌 API Endpoints Requeridos

### Autenticación (Sanctum)
*   `POST /login`: { email, password } -> Token/Cookie
*   `POST /register`: { name, email, password } -> Token/Cookie
*   `POST /logout`: Revocar token.
*   `GET /user`: Obtener usuario actual.

### Ranking
*   `GET /rankings?period=weekly`: Listado de puntuaciones.
*   `POST /scores`: { score, level_id } -> Guardar puntuación.
*   `GET /user/rank`: Posición del usuario actual.

### Progreso
*   `GET /progress`: Niveles desbloqueados, tecnologías.
*   `POST /progress/save`: Guardar estado del juego.

## ✅ Tests Unitarios
Cada servicio debe tener sus tests unitarios usando PHPUnit o Pest.
*   Probar casos de éxito.
*   Probar casos de error (excepciones).
*   No probar el framework (Laravel ya está probado), probar TU lógica.

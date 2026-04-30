# 🚀 Guía Rápida: Añadir una nueva tabla relacionada (Paso a Paso)

Si necesitas añadir una nueva entidad (ej. una tabla de `Comentarios` para las `Tareas`), sigue este esquema mental.

### 1. El Comando "Todo en Uno"
Para no crear los archivos uno a uno, usa este comando que crea el **Modelo**, la **Migración** y el **Controlador** con todos los métodos (CRUD):
```bash
php artisan make:model Comment -mcr
```

### 2. Definir las Columnas (Migración)
Abre el archivo creado en `database/migrations/`. Aquí defines qué datos guardará la tabla:
*   **Tipos comunes:** `$table->string('titulo');`, `$table->text('contenido');`, `$table->integer('prioridad');`, `$table->boolean('leido')->default(false);`.
*   **La Relación:** Siempre debes poner el ID de la tabla a la que pertenece.
```php
public function up(): void {
    Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->text('body'); // Una columna para el texto
        $table->string('author')->nullable(); // Una columna opcional para el autor
        
        // LA CLAVE: Relación con la tabla Tasks
        $table->foreignId('task_id')->constrained()->onDelete('cascade');
        
        $table->timestamps();
    });
}
```
*Ejecuta:* `php artisan migrate` para que se cree en MySQL.

### 3. Entender "Padre" e "Hijo"
Es la forma de saber quién manda y quién pertenece a quién:
*   **EL PADRE (La tabla principal):** Es la que ya existía. Por ejemplo, una **Tarea**. Ella no sabe cuántos comentarios va a tener, solo sabe que "tiene muchos" (`hasMany`).
*   **EL HIJO (La tabla nueva):** Es el **Comentario**. Cada comentario "pertenece a" (`belongsTo`) una única tarea. El hijo es siempre el que lleva el "apellido" (el ID) del padre.

### 4. Conectar los Modelos (La lógica)
Para que Laravel sepa que están unidos, escribe esto en los archivos de la carpeta `app/Models/`:

*   **En el HIJO (`Comment.php`):**
    ```php
    public function task() { 
        return $this->belongsTo(Task::class); 
    }
    ```
*   **En el PADRE (`Task.php`):**
    ```php
    public function comments() { 
        return $this->hasMany(Comment::class); 
    }
    ```

### 5. Uso en el Controlador y Rutas
1.  **Rutas:** Registra el controlador en `routes/web.php`: `Route::resource('comments', CommentController::class);`.
2.  **Guardar datos:** En el método `store` del controlador, valida y guarda:
    ```php
    $validated = $request->validate([
        'body' => 'required',
        'task_id' => 'required|exists:tasks,id'
    ]);
    Comment::create($validated);
    ```
3.  **Mostrar en la vista:** Ahora puedes hacer `$task->comments` en cualquier Blade para sacar todos los comentarios de esa tarea específica.

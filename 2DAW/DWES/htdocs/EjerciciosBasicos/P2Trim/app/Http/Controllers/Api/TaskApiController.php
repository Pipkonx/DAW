<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task; // Importamos el modelo de Tarea para poder usar la tabla de la base de datos
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXPLICACIÓN: Atributos #[OA\...]
|--------------------------------------------------------------------------
| Estas líneas que empiezan con #[OA... no afectan a cómo funciona tu código 
| de PHP. Son como "post-its" o "etiquetas" que lee una herramienta llamada 
| Swagger. Sirven para que se genere automáticamente una página web donde 
| otros programadores pueden ver qué hace tu API y probarla sin escribir código.
*/
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Tareas", description: "Operaciones para gestionar las tareas de incidencias")]
class TaskApiController extends Controller
{
    #[OA\Get(
        path: "/api/tasks",
        operationId: "getTasksList",
        summary: "Listar todas las tareas",
        description: "Devuelve un JSON con todas las tareas que hay en la base de datos",
        tags: ["Tareas"],
        responses: [
            new OA\Response(response: 200, description: "Lista enviada correctamente"),
            new OA\Response(response: 401, description: "No tienes permiso (debes estar logueado)")
        ]
    )]
    public function index()
    {
        // Task::all() le dice a la base de datos: "tráeme todo lo que tengas en la tabla de tareas"
        return response()->json(Task::all(), 200);
    }

    #[OA\Post(
        path: "/api/tasks",
        operationId: "storeTask",
        summary: "Crear una nueva tarea",
        description: "Recibe datos y los guarda como una nueva tarea",
        tags: ["Tareas"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["description", "contact_person"],
                properties: [
                    new OA\Property(property: "description", type: "string", example: "Arreglar el grifo"),
                    new OA\Property(property: "contact_person", type: "string", example: "Raquel"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Tarea guardada con éxito")
        ]
    )]
    public function store(Request $request)
    {
        // Guardamos los datos recibidos en una nueva fila de la tabla
        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    #[OA\Get(
        path: "/api/tasks/{id}",
        operationId: "getTaskById",
        summary: "Ver una tarea concreta",
        tags: ["Tareas"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "El número (ID) de la tarea que quieres ver",
                required: true,
                in: "path",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Tarea encontrada"),
            new OA\Response(response: 404, description: "Esa tarea no existe")
        ]
    )]
    public function show(string $id)
    {
        // Buscamos la tarea por su número de ID
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['error' => 'No encontrada'], 404);
        }
        return response()->json($task, 200);
    }

    #[OA\Put(
        path: "/api/tasks/{id}",
        operationId: "updateTask",
        summary: "Modificar datos de una tarea",
        tags: ["Tareas"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID de la tarea a cambiar",
                required: true,
                in: "path",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "description", type: "string", example: "Arreglar grifo (Urgente)"),
                    new OA\Property(property: "contact_person", type: "string", example: "Raquel"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Tarea actualizada correctamente"),
            new OA\Response(response: 404, description: "No encontrada")
        ]
    )]
    public function update(Request $request, string $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['error' => 'No encontrada'], 404);
        }
        // Actualizamos los campos con los nuevos datos recibidos
        $task->update($request->all());
        return response()->json($task, 200);
    }

    #[OA\Delete(
        path: "/api/tasks/{id}",
        operationId: "deleteTask",
        summary: "Borrar una tarea",
        tags: ["Tareas"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID de la tarea que quieres borrar",
                required: true,
                in: "path",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Borrada sin problemas"),
            new OA\Response(response: 404, description: "No la he encontrado, así que no he podido borrarla")
        ]
    )]
    public function destroy(string $id)
    {
        if (!Task::find($id)) {
            return response()->json(['error' => 'No encontrada'], 404);
        }
        // Borramos la tarea definitivamente de la base de datos
        Task::destroy($id);
        return response()->json(null, 204);
    }
}

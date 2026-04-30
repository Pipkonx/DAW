<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * Controlador de API para Tareas
 * 
 * Este controlador expone los recursos de Tareas en formato JSON para su 
 * consumo por servicios externos y aplicaciones móviles.
 */
#[OA\Tag(name: "Tareas", description: "Operaciones para gestionar las tareas de incidencias")]
class TaskApiController extends Controller
{
    /**
     * Lista todas las tareas en formato JSON.
     * 
     * @return JsonResponse
     */
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
    public function index(): JsonResponse
    {
        return response()->json(Task::all(), 200);
    }

    /**
     * Crea una nueva tarea desde la API.
     * 
     * @param Request $request
     * @return JsonResponse
     */
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
    public function store(Request $request): JsonResponse
    {
        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    /**
     * Muestra una tarea específica por su ID.
     * 
     * @param string $id
     * @return JsonResponse
     */
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
    public function show(string $id): JsonResponse
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['error' => 'No encontrada'], 404);
        }
        return response()->json($task, 200);
    }

    /**
     * Actualiza los datos de una tarea vía API.
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
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
    public function update(Request $request, string $id): JsonResponse
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['error' => 'No encontrada'], 404);
        }
        $task->update($request->all());
        return response()->json($task, 200);
    }

    /**
     * Borra una tarea del sistema.
     * 
     * @param string $id
     * @return JsonResponse
     */
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
    public function destroy(string $id): JsonResponse
    {
        if (!Task::find($id)) {
            return response()->json(['error' => 'No encontrada'], 404);
        }
        Task::destroy($id);
        return response()->json(null, 204);
    }
}



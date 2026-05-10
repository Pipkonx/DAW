<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "API de Clientes",
    version: "1.0.0",
    description: "Servicio REST para la gestión de clientes internacionales"
)]
#[OA\Server(url: "http://localhost:8000")]
class ClienteApiController extends Controller
{
    #[OA\Get(
        path: "/api/clientes",
        summary: "Listado de todos los clientes",
        tags: ["Clientes"],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Lista de clientes obtenida con éxito",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example: "Juan Perez"),
                            new OA\Property(property: "cif", type: "string", example: "B12345678"),
                            new OA\Property(property: "currency", type: "string", example: "EUR")
                        ]
                    )
                )
            )
        ]
    )]
    public function index()
    {
        return response()->json(Cliente::all(), Response::HTTP_OK);
    }

    #[OA\Post(
        path: "/api/clientes",
        summary: "Crear un nuevo cliente",
        tags: ["Clientes"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "cif", "currency"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Empresa S.A."),
                    new OA\Property(property: "cif", type: "string", example: "A87654321"),
                    new OA\Property(property: "currency", type: "string", example: "USD")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201, 
                description: "Cliente creado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 5),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "cif", type: "string"),
                        new OA\Property(property: "currency", type: "string")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'cif' => 'required|string|unique:clientes',
            'currency' => 'required|string|size:3',
        ]);

        $cliente = Cliente::create($validated);
        return response()->json($cliente, Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: "/api/clientes/{id}",
        summary: "Ver detalle de un cliente",
        tags: ["Clientes"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Detalle del cliente encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "cif", type: "string"),
                        new OA\Property(property: "currency", type: "string")
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Cliente no encontrado")
        ]
    )]
    public function show(Cliente $cliente)
    {
        return response()->json($cliente, Response::HTTP_OK);
    }

    #[OA\Put(
        path: "/api/clientes/{id}",
        summary: "Actualizar un cliente",
        tags: ["Clientes"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Nombre Actualizado"),
                    new OA\Property(property: "cif", type: "string"),
                    new OA\Property(property: "currency", type: "string", example: "GBP")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: "Cliente actualizado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "cif", type: "string"),
                        new OA\Property(property: "currency", type: "string")
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Cliente no encontrado")
        ]
    )]
    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($request->all());
        return response()->json($cliente, Response::HTTP_OK);
    }

    #[OA\Delete(
        path: "/api/clientes/{id}",
        summary: "Eliminar un cliente",
        tags: ["Clientes"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 204, description: "Cliente eliminado correctamente"),
            new OA\Response(response: 404, description: "Cliente no encontrado")
        ]
    )]
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

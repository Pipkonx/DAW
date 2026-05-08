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
        summary: "Listado de clientes",
        responses: [
            new OA\Response(response: 200, description: "Lista de clientes")
        ]
    )]
    public function index()
    {
        return response()->json(Cliente::all(), Response::HTTP_OK);
    }

    #[OA\Post(
        path: "/api/clientes",
        summary: "Crear un nuevo cliente",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "cif", type: "string"),
                    new OA\Property(property: "currency", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Cliente creado")
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
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Detalle del cliente"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show(Cliente $cliente)
    {
        return response()->json($cliente, Response::HTTP_OK);
    }

    #[OA\Put(
        path: "/api/clientes/{id}",
        summary: "Actualizar un cliente",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "cif", type: "string"),
                    new OA\Property(property: "currency", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Cliente actualizado"),
            new OA\Response(response: 404, description: "No encontrado")
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
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 204, description: "Cliente eliminado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Practice;
use Illuminate\Http\Request;

/**
 * @group Gestión de Prácticas
 *
 * APIs para que los alumnos y tutores gestionen las actividades de prácticas.
 */
class PracticeController extends Controller
{
    /**
     * Listar todas las prácticas.
     *
     * Retorna una lista paginada de todas las actividades de prácticas registradas.
     *
     * @queryParam page int Página a mostrar. Example: 1
     */
    public function index()
    {
        return response()->json(Practice::with('alumno.user')->paginate(10));
    }

    /**
     * Crear una nueva práctica.
     *
     * @bodyParam alumno_id int required ID del alumno. Example: 1
     * @bodyParam title string required Título de la actividad. Example: Desarrollo de API
     * @bodyParam starts_at string required Fecha de inicio (ISO). Example: 2026-01-28 10:00:00
     * @bodyParam ends_at string required Fecha de fin (ISO). Example: 2026-01-28 14:00:00
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
        ]);

        $practice = Practice::create($validated);

        return response()->json($practice, 201);
    }

    /**
     * Mostrar detalle de una práctica.
     *
     * @urlParam id int required ID de la práctica. Example: 1
     */
    public function show($id)
    {
        $practice = Practice::with('alumno.user')->findOrFail($id);
        return response()->json($practice);
    }
}

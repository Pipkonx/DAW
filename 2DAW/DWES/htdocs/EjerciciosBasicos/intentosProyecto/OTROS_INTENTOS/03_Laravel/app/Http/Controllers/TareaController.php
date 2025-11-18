<?php

namespace App\Http\Controllers;

use App\Legacy\Tarea;
use App\Legacy\Usuario;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index(Request $request)
    {
        $usuarioId = $request->query('usuario_id');
        $completada = $request->query('completada');

        $tareaModel = new Tarea();
        if ($usuarioId !== null && $usuarioId !== '') {
            $tareas = $tareaModel->getByUsuario((int) $usuarioId);
        } elseif ($completada !== null && $completada !== '') {
            $tareas = $tareaModel->getByEstado((int) $completada);
        } else {
            $tareas = $tareaModel->getAll();
        }

        $usuarios = (new Usuario())->getAll();
        $filtroUsuario = $usuarioId ?? '';
        $filtroCompletada = $completada ?? '';

        return view('tareas.index', compact('tareas', 'usuarios', 'filtroUsuario', 'filtroCompletada'));
    }
}
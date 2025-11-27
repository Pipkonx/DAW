<?php

namespace App\Http\Controllers;

use App\Models\Funciones; // Para la validación
use App\Models\Tareas;    // Asumiendo que tienes un modelo Tarea
use Illuminate\Support\Facades\Auth; // Para obtener el usuario autenticado (si lo usas)

class OperarioController extends Controller
{
    const TAREASXPAGINA = 10; // Constante para paginación, si aplica

    /**
     * Muestra una lista de tareas asignadas al operario autenticado.
     *
     * @return \Illuminate\Http\Response
     */
    public function listar()
    {
        // Asumiendo que el operario se identifica por su nombre o ID en la sesión
        // Reemplaza 'Auth::user()->nombre' con la forma en que identificas al operario
        $operarioEncargado = session('nombre_operario') ?? 'Operario Desconocido'; // Ejemplo: obtener de sesión

        $modelo = new Tareas();
        $error = '';
        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        if ($paginaActual < 1) $paginaActual = 1;
        $totalElementos = 0;
        $totalPaginas = 1;
        try {
            // Filtrar tareas por el operario encargado
            $tareas = $modelo->listarPorOperario(self::TAREASXPAGINA, $paginaActual, $operarioEncargado);
            $totalElementos = $modelo->contarPorOperario($operarioEncargado);
            $totalPaginas = (int) max(1, ceil($totalElementos / self::TAREASXPAGINA));
        } catch (\Throwable $e) {
            $tareas = [];
            $error = 'No se pudo obtener el listado de tareas asignadas.';
        }
        $datos = ['tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
        if ($error) $datos['errorGeneral'] = $error;
        return view('operario.tareas.lista', $datos); // Asumiendo una vista 'operario.tareas.lista'
    }

    /**
     * Muestra los detalles de una tarea específica asignada al operario.
     *
     * @return \Illuminate\Http\Response
     */
    public function mostrar()
    {
        $id = $_GET['id'] ?? null;
        // Asumiendo que el operario se identifica por su nombre o ID en la sesión
        $operarioEncargado = session('nombre_operario') ?? 'Operario Desconocido'; // Ejemplo: obtener de sesión

        $modelo = new Tareas();
        $tarea = null;
        try {
            $tarea = $modelo->buscar((int)$id);
            // Asegurarse de que la tarea esté asignada a este operario
            if ($tarea && $tarea['operarioEncargado'] !== $operarioEncargado) {
                $tarea = null; // No permitir ver tareas no asignadas
            }
        } catch (\Throwable $e) {
        }

        if (!$tarea) {
            return redirect(url('operario/tareas'))->with('errorGeneral', 'La tarea no existe o no está asignada a usted.');
        }
        return view('operario.tareas.detalle', $tarea); // Asumiendo una vista 'operario.tareas.detalle'
    }

    /**
     * Muestra el formulario para editar una tarea existente (solo para operarios).
     *
     * @return \Illuminate\Http\Response
     */
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        $operarioEncargado = session('nombre_operario') ?? 'Operario Desconocido';

        $modelo = new Tareas();
        $tarea = null;
        try {
            $tarea = $modelo->buscar((int)$id);
            if ($tarea && $tarea['operarioEncargado'] !== $operarioEncargado) {
                $tarea = null;
            }
        } catch (\Throwable $e) {
        }

        if (!$tarea) {
            return redirect(url('operario/tareas'))->with('errorGeneral', 'No se pudo cargar la tarea para edición o no está asignada a usted.');
        }

        $datos = $tarea;
        $datos['id'] = (int)$id;
        $datos['formActionUrl'] = url('operario/tareas/actualizar?id=' . $id); // Ruta para el POST de actualización

        return view('operario.tareas.edicion_operario', $datos); // Vista específica para edición de operario
    }

    /**
     * Actualiza una tarea existente en la base de datos (solo para operarios).
     *
     * @return \Illuminate\Http\Response
     */
    public function actualizar()
    {
        $id = $_GET['id'] ?? null;
        $operarioEncargado = session('nombre_operario') ?? 'Operario Desconocido';

        if (!isset($_POST['id'])) {
            $_POST['id'] = $id;
        }

        $this->filtrarOperario(); // Validación específica para operarios
        if (!empty(Funciones::$errores)) {
            $datos = $_POST;
            $datos['id'] = (int)$id;
            $datos['formActionUrl'] = url('operario/tareas/actualizar?id=' . $id);
            return view('operario.tareas.edicion_operario', $datos);
        }

        $modelo = new Tareas();
        try {
            $tareaExistente = $modelo->buscar((int)$id);
            if ($tareaExistente && $tareaExistente['operarioEncargado'] !== $operarioEncargado) {
                return redirect(url('operario/tareas'))->with('errorGeneral', 'No tiene permisos para actualizar esta tarea.');
            }

            $modelo->actualizarOperario((int)$id, $_POST); // Método específico para operarios
            // Guardar evidencias (ficheros)
            $base = __DIR__ . '/../../../public/evidencias/' . (int)$id;
            if (!is_dir($base)) @mkdir($base, 0777, true);
            if (isset($_FILES['fichero_resumen']) && is_uploaded_file($_FILES['fichero_resumen']['tmp_name'])) {
                @move_uploaded_file($_FILES['fichero_resumen']['tmp_name'], $base . '/resumen_' . time() . '_' . basename($_FILES['fichero_resumen']['name']));
            }
            if (isset($_FILES['fotos'])) {
                foreach ((array)$_FILES['fotos']['tmp_name'] as $i => $tmp) {
                    if (is_uploaded_file($tmp)) {
                        @move_uploaded_file($tmp, $base . '/foto_' . time() . '_' . basename($_FILES['fotos']['name'][$i]));
                    }
                }
            }

            return redirect(url('operario/tareas'))->with('mensaje', 'Tarea actualizada correctamente.');
        } catch (\Throwable $e) {
            $datos = $_POST;
            $datos['id'] = (int)$id;
            $datos['errorGeneral'] = 'No se pudo actualizar la tarea. Revise la conexión y la tabla.';
            $datos['formActionUrl'] = url('operario/tareas/actualizar?id=' . $id);
            return view('operario.tareas.edicion_operario', $datos);
        }
    }

    /**
     * Valida y normaliza los datos recibidos del formulario de tarea para operarios.
     * Población de mensajes de error en `Funciones::$errores` (estático).
     *
     * @return void
     */
    private function filtrarOperario()
    {
        Funciones::$errores = [];

        $estado = trim((string)($_POST['estadoTarea'] ?? ''));
        $anotacionesPosteriores = $_POST['anotacionesPosteriores'] ?? '';

        if (!in_array($estado, ['B', 'P', 'R', 'C'])) {
            Funciones::$errores['estadoTarea'] = 'Estado de tarea no válido.';
        }
        // Puedes añadir más validaciones específicas para el operario aquí
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Funciones; // Para la validación
use App\Models\Tareas;    // Asumiendo que tienes un modelo Tarea

class AdministradorController extends Controller
{
    const TAREASXPAGINA = 5; // Constante para paginación, si aplica

    /**
     * Muestra una lista de todas las tareas (para el administrador).
     *
     * @return \Illuminate\Http\Response
     */
    public function listar()
    {
        $modelo = new Tareas();
        $error = '';
        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        if ($paginaActual < 1) $paginaActual = 1;
        $totalElementos = 0;
        $totalPaginas = 1;
        try {
            $tareas = $modelo->listar(self::TAREASXPAGINA, $paginaActual);
            $totalElementos = $modelo->contar();
            $totalPaginas = (int) max(1, ceil($totalElementos / self::TAREASXPAGINA));
        } catch (\Throwable $e) {
            $tareas = [];
            $error = 'No se pudo obtener el listado de tareas.';
        }
        $datos = ['tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
        if ($error) $datos['errorGeneral'] = $error;
        return view('tareas.lista', $datos); // Asumiendo una vista 'admin.tareas.lista'
    }

    /**
     * Muestra el formulario para crear una nueva tarea.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        // Datos iniciales para el formulario vacío
        $datos = [
            'nifCif' => '',
            'personaNombre' => '',
            'telefono' => '',
            'descripcionTarea' => '',
            'correo' => '',
            'direccionTarea' => '',
            'poblacion' => '',
            'codigoPostal' => '',
            'provincia' => '',
            'estadoTarea' => '',
            'operarioEncargado' => '',
            'fechaRealizacion' => '',
            'anotacionesAnteriores' => '',
            'anotacionesPosteriores' => '',
            'formActionUrl' => url('admin/tareas/crear'), // Ruta para el POST de creación
        ];
        return view('tareas.alta_edicion', $datos); // Asumiendo una vista 'admin.tareas.alta_edicion'
    }

    /**
     * Almacena una nueva tarea en la base de datos.
     *
     * @return \Illuminate\Http\Response
     */
    public function guardar()
    {
        // Validar datos
        $this->filtrar();
        if (!empty(Funciones::$errores)) {
            // Si hay errores, volver al formulario con los datos y errores
            $datos = $_POST;
            $datos['formActionUrl'] = url('admin/tareas/crear');
            return view('tareas.alta_edicion', $datos);
        }

        $modelo = new Tareas();
        try {
            $modelo->crear($_POST); // Usar el método crear del modelo Tareas
            $mensaje = 'Tarea creada correctamente';

            // Recargar la lista de tareas para la vista
            $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($paginaActual < 1) $paginaActual = 1;
            $totalElementos = $modelo->contar();
            $totalPaginas = (int) max(1, ceil($totalElementos / self::TAREASXPAGINA));
            $tareas = $modelo->listar(self::TAREASXPAGINA, $paginaActual);

            $datos = ['mensaje' => $mensaje, 'tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
            return view('tareas.lista', $datos);
        } catch (\Throwable $e) {
            $datos = $_POST;
            $datos['errorGeneral'] = 'No se pudo guardar la tarea. Revise la conexión y la tabla.';
            $datos['formActionUrl'] = url('admin/tareas/crear');
            return view('tareas.alta_edicion', $datos);
        }
    }

    /**
     * Muestra los detalles de una tarea específica.
     *
     * @return \Illuminate\Http\Response
     */
    public function mostrar()
    {
        $id = $_GET['id'] ?? null;
        $modelo = new Tareas();
        $tarea = null;
        try {
            $tarea = $modelo->buscar((int)$id);
        } catch (\Throwable $e) {
        }

        if (!$tarea) {
            // Si no existe la tarea, redirigir o mostrar error
            $datos = ['errorGeneral' => 'La tarea no existe.'];
            return view('tareas.lista', $datos);
        }
        return view('tareas.detalle', $tarea); // Asumiendo una vista 'admin.tareas.detalle'
    }

    /**
     * Muestra el formulario para editar una tarea existente.
     *
     * @return \Illuminate\Http\Response
     */
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        $modelo = new Tareas();
        $tarea = null;
        try {
            $tarea = $modelo->buscar((int)$id);
        } catch (\Throwable $e) {
        }

        if (!$tarea) {
            $datos = ['errorGeneral' => 'No se pudo cargar la tarea para edición.'];
            return view('tareas.lista', $datos);
        }

        // Preparar datos para la vista de edición
        $datos = $tarea; // Los datos de la tarea encontrada
        $datos['id'] = (int)$id;
        $datos['formActionUrl'] = url('admin/tareas/editar?id=' . $id); // Ruta para el POST de edición

        return view('tareas.alta_edicion', $datos);
    }

    /**
     * Actualiza una tarea existente en la base de datos.
     *
     * @return \Illuminate\Http\Response
     */
    public function actualizar()
    {
        $id = $_GET['id'] ?? null; // El ID viene por GET en la URL para la edición
        if (!isset($_POST['id'])) { // Asegurarse de que el ID también esté en POST para el modelo
            $_POST['id'] = $id;
        }

        $this->filtrar();
        if (!empty(Funciones::$errores)) {
            $datos = $_POST;
            $datos['id'] = (int)$id;
            $datos['formActionUrl'] = url('admin/tareas/editar?id=' . $id);
            return view('tareas.alta_edicion', $datos);
        }

        $modelo = new Tareas();
        try {
            $modelo->actualizar((int)$id, $_POST); // Usar el método actualizar del modelo Tareas
            $mensaje = 'Tarea actualizada correctamente';

            // Recargar la lista de tareas para la vista
            $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($paginaActual < 1) $paginaActual = 1;
            $totalElementos = $modelo->contar();
            $totalPaginas = (int) max(1, ceil($totalElementos / self::TAREASXPAGINA));
            $tareas = $modelo->listar(self::TAREASXPAGINA, $paginaActual);

            $datos = ['mensaje' => $mensaje, 'tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
            return view('tareas.lista', $datos);
        } catch (\Throwable $e) {
            $datos = $_POST;
            $datos['id'] = (int)$id;
            $datos['errorGeneral'] = 'No se pudo actualizar la tarea. Revise la conexión y la tabla.';
            $datos['formActionUrl'] = url('admin/tareas/editar?id=' . $id);
            return view('tareas.alta_edicion', $datos);
        }
    }

    /**
     * Elimina una tarea de la base de datos.
     *
     * @return \Illuminate\Http\Response
     */
    public function eliminar()
    {
        $id = $_POST['id'] ?? null; // El ID para eliminar viene por POST
        if (session('rol') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $modelo = new Tareas();
        try {
            $modelo->eliminar((int)$id);
            $mensaje = 'Tarea eliminada correctamente';

            // Recargar la lista de tareas para la vista
            $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($paginaActual < 1) $paginaActual = 1;
            $totalElementos = $modelo->contar();
            $totalPaginas = (int) max(1, ceil($totalElementos / self::TAREASXPAGINA));
            $tareas = $modelo->listar(self::TAREASXPAGINA, $paginaActual);

            $datos = ['mensaje' => $mensaje, 'tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
            return view('tareas.lista', $datos);
        } catch (\Throwable $e) {
            $datos = ['errorGeneral' => 'No se pudo eliminar la tarea.'];
            return view('admin.tareas.lista', $datos);
        }
    }

    /**
     * Muestra una vista de confirmación antes de eliminar la tarea.
     *
     * @return \Illuminate\Http\Response
     */
    public function confirmarEliminacion()
    {
        $id = $_GET['id'] ?? null;
        if (session('rol') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $modelo = new Tareas();
        $tarea = null;
        try {
            $tarea = $modelo->buscar((int)$id);
        } catch (\Throwable $e) {
        }
        if (!$tarea) {
            $datos = ['errorGeneral' => 'No se pudo cargar la tarea para eliminación.'];
            return view('tareas.lista', $datos);
        }
        $tarea['id'] = (int)$id;
        return view('tareas.confirmar_eliminacion', $tarea); // Asumiendo una vista 'admin.tareas.confirmar_eliminacion'
    }

    /**
     * Valida y normaliza los datos recibidos del formulario de tarea.
     * Población de mensajes de error en `Funciones::$errores` (estático).
     *
     * @return void
     */
    private function filtrar()
    {
        Funciones::$errores = Tareas::validarDatos($_POST);
    }
}

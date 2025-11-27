<?php

namespace App\Http\Controllers;

use App\Models\M_Funciones; // Para la validación
use App\Models\M_Tareas;    // Asumiendo que tienes un modelo Tarea

class C_Administrador extends C_Controller
{

    /**
     * Muestra una lista de todas las tareas (para el administrador).
     *
     * @return \Illuminate\Http\Response
     */
    public function listar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $modelo = new M_Tareas();
        $error = '';
        $tareas = [];

        // Obtener datos de paginación
        $paginationData = $this->getPaginationData($modelo);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];

        $tareas = $modelo->listar(self::TAREAS_POR_PAGINA, $paginaActual);

        $datos = ['tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas, 'baseUrl' => 'admin/tareas'];
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
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
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
            'formActionUrl' => dirname($_SERVER['SCRIPT_NAME']) . '/admin/tareas/crear', // Ruta para el POST de creación
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
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        // Validar datos
        $this->filtrar();
        if (!empty(M_Funciones::$errores)) {
            // Si hay errores, volver al formulario con los datos y errores
            $datos = $_POST;
            $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/admin/tareas/crear';
            return view('tareas.alta_edicion', $datos);
        }

        $modelo = new M_Tareas();
        $modelo->crear($_POST); // Usar el método crear del modelo Tareas
        $mensaje = 'Tarea creada correctamente';

        // Recargar la lista de tareas para la vista
        $paginationData = $this->getPaginationData($modelo);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];
        $tareas = $modelo->listar(self::TAREAS_POR_PAGINA, $paginaActual);

        $datos = ['mensaje' => $mensaje, 'tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
        return view('tareas.lista', $datos);
    }

    /**
     * Muestra los detalles de una tarea específica.
     *
     * @return \Illuminate\Http\Response
     */
    public function mostrar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $id = $_GET['id'] ?? null;
        $modelo = new M_Tareas();
        $tarea = null;
        $tarea = $modelo->buscar((int)$id);

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
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $id = $_GET['id'] ?? null;
        $modelo = new M_Tareas();
        $tarea = null;
        $tarea = $modelo->buscar((int)$id);

        if (!$tarea) {
            $datos = ['errorGeneral' => 'No se pudo cargar la tarea para edición.'];
            return view('tareas.lista', $datos);
        }

        // Preparar datos para la vista de edición
        $datos = $tarea; // Los datos de la tarea encontrada
        $datos['id'] = (int)$id;
        $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/admin/tareas/editar?id=' . $id; // Ruta para el POST de edición

        return view('tareas.alta_edicion', $datos);
    }

    /**
     * Actualiza una tarea existente en la base de datos.
     *
     * @return \Illuminate\Http\Response
     */
    public function actualizar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $id = $_GET['id'] ?? null; // El ID viene por GET en la URL para la edición
        if (!isset($_POST['id'])) { // Asegurarse de que el ID también esté en POST para el modelo
            $_POST['id'] = $id;
        }

        $this->filtrar();
        if (!empty(M_Funciones::$errores)) {
            $datos = $_POST;
            $datos['id'] = (int)$id;
            $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/admin/tareas/editar?id=' . $id;
            return view('tareas.alta_edicion', $datos);
        }

        $modelo = new M_Tareas();
        $modelo->actualizar((int)$id, $_POST); // Usar el método actualizar del modelo Tareas
        $mensaje = 'Tarea actualizada correctamente';

        // Recargar la lista de tareas para la vista
        $paginationData = $this->getPaginationData($modelo);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];
        $tareas = $modelo->listar(self::TAREAS_POR_PAGINA, $paginaActual);

        $datos = ['mensaje' => $mensaje, 'tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
        return view('tareas.lista', $datos);
    }

    /**
     * Elimina una tarea de la base de datos.
     *
     * @return \Illuminate\Http\Response
     */
    public function eliminar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $id = $_POST['id'] ?? null; // El ID para eliminar viene por POST
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $modelo = new M_Tareas(); // Corregido de 'Tareas()' a 'M_Tareas()'
        $modelo->eliminar((int)$id);
        $mensaje = 'Tarea eliminada correctamente';

        // Recargar la lista de tareas para la vista
        $paginationData = $this->getPaginationData($modelo);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];
        $tareas = $modelo->listar(self::TAREAS_POR_PAGINA, $paginaActual);

        $datos = ['mensaje' => $mensaje, 'tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
        return view('tareas.lista', $datos);
    }

    /**
     * Muestra una vista de confirmación antes de eliminar la tarea.
     *
     * @return \Illuminate\Http\Response
     */
    public function confirmarEliminacion()
    {
        $id = $_GET['id'] ?? null;
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $modelo = new M_Tareas(); // Corregido de 'Tareas()' a 'M_Tareas()'
        $tarea = null;
        $tarea = $modelo->buscar((int)$id);
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
        M_Funciones::$errores = M_Tareas::validarDatos($_POST); // Corregido de 'Tareas::validarDatos' a 'M_Tareas::validarDatos'
    }
}
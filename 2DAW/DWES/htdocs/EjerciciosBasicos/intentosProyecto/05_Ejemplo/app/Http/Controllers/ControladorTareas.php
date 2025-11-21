<?php

namespace App\Http\Controllers;

use App\Models\Funciones;
use App\Models\Tareas;

/**
 * Controlador HTTP para gestionar tareas: alta, listado, edición y eliminación.
 * Incluye validación de datos mediante la clase `Funciones`.
 */
class ControladorTareas extends Controller
{
    /**
     * Crea una nueva tarea.
     *
     * GET: muestra el formulario vacío.
     * POST: valida los datos y crea la tarea, devolviendo el listado con mensaje.
     *
     * @return mixed Vista de alta o de listado según el flujo.
     */
    public function crear()
    {
        // Crear nueva tarea: GET muestra formulario vacío; POST valida y crea
        if ($_POST) {
            // Validar datos
            $this->filtrar();
            if (!empty(Funciones::$errores)) return view('alta', $_POST);

            $modelo = new Tareas();
            try {
                $modelo->crear($_POST);
                // Devolver listado con mensaje sin usar sesiones
                $tareas = $modelo->listar();
                $porPagina = 20;
                $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                if ($paginaActual < 1) $paginaActual = 1;
                $totalElementos = $modelo->contar();
                $totalPaginas = (int) max(1, ceil($totalElementos / $porPagina));
                return view('tareas_lista', ['tareas' => $tareas, 'mensaje' => 'Tarea creada correctamente', 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas]);
            } catch (\Throwable $e) {
                $datos = $_POST;
                $datos['errorGeneral'] = 'No se pudo guardar la tarea. Revise la conexión y la tabla.';
                return view('alta', $datos);
            }
        }

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
        ];
        return view('alta', $datos);
    }

    /**
     * Lista las tareas paginadas y maneja errores de obtención.
     *
     * @return mixed Vista con la lista de tareas y datos de paginación.
     */
    public function listar()
    {
        $modelo = new Tareas();
        $error = '';
        try {
            $tareas = $modelo->listar();
        } catch (\Throwable $e) {
            $tareas = [];
            $error = 'No se pudo obtener el listado de tareas.';
        }
        $porPagina = 20;
        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        if ($paginaActual < 1) $paginaActual = 1;
        $totalElementos = 0;
        $totalPaginas = 1;
        try {
            $totalElementos = $modelo->contar();
            $totalPaginas = (int) max(1, ceil($totalElementos / $porPagina));
        } catch (\Throwable $e) {
        }
        $datos = ['tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
        if ($error) $datos['errorGeneral'] = $error;
        return view('tareas_lista', $datos);
    }

    /**
     * Edita una tarea existente.
     *
     * GET: carga los datos para edición.
     * POST: valida y actualiza la tarea.
     *
     * @param int|string $id Identificador de la tarea.
     * @return mixed Vista de alta con datos o listado con mensaje.
     */
    public function editar($id)
    {
        // Si es POST, validar y actualizar; si es GET, cargar datos
        if ($_POST) {
            $this->filtrar();
            if (!empty(Funciones::$errores)) {
                $datos = $_POST;
                $datos['id'] = (int)$id;
                return view('alta', $datos);
            }
            $modelo = new Tareas();
            try {
                $modelo->actualizar((int)$id, $_POST);
                $tareas = $modelo->listar();
                $porPagina = 20;
                $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                if ($paginaActual < 1) $paginaActual = 1;
                $totalElementos = $modelo->contar();
                $totalPaginas = (int) max(1, ceil($totalElementos / $porPagina));
                return view('tareas_lista', ['tareas' => $tareas, 'mensaje' => 'Tarea actualizada correctamente', 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas]);
            } catch (\Throwable $e) {
                $datos = $_POST;
                $datos['id'] = (int)$id;
                $datos['errorGeneral'] = 'No se pudo actualizar la tarea. Revise la conexión y la tabla.';
                return view('alta', $datos);
            }
        }

        $modelo = new Tareas();
        try {
            $tarea = $modelo->buscar((int)$id);
        } catch (\Throwable $e) {
            $tareas = [];
            return view('tareas_lista', ['tareas' => $tareas, 'errorGeneral' => 'No se pudo cargar la tarea para edición.']);
        }
        if (!$tarea) {
            // Si no existe la tarea, mostrar listado con mensaje de error sin redirigir a raíz
            $tareas = [];
            try {
                $tareas = $modelo->listar();
            } catch (\Throwable $e2) {
                $tareas = [];
            }
            $porPagina = 20;
            $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($paginaActual < 1) $paginaActual = 1;
            $totalElementos = 0;
            $totalPaginas = 1;
            try {
                $totalElementos = $modelo->contar();
                $totalPaginas = (int) max(1, ceil($totalElementos / $porPagina));
            } catch (\Throwable $e3) {
            }
            return view('tareas_lista', ['tareas' => $tareas, 'errorGeneral' => 'No se pudo cargar la tarea para edición.', 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas]);
        }
        $tarea['id'] = (int)$id;
        return view('alta', $tarea);
    }

    /**
     * Elimina una tarea por su identificador.
     *
     * @param int|string $id Identificador de la tarea.
     * @return mixed Vista de listado con mensaje o error.
     */
    public function eliminar($id)
    {
        $modelo = new Tareas();
        try {
            $modelo->eliminar((int)$id);
            $tareas = $modelo->listar();
            $porPagina = 20;
            $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($paginaActual < 1) $paginaActual = 1;
            $totalElementos = $modelo->contar();
            // ceil devuelve el entero más pequeño que es mayor o igual que un número dado
            $totalPaginas = (int) max(1, ceil($totalElementos / $porPagina));
            return view('tareas_lista', ['tareas' => $tareas, 'mensaje' => 'Tarea eliminada correctamente', 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas]);
        } catch (\Throwable $e) {
            try {
                $tareas = $modelo->listar();
            } catch (\Throwable $e2) {
                $tareas = [];
            }
            $porPagina = 20;
            $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($paginaActual < 1) $paginaActual = 1;
            $totalElementos = 0;
            $totalPaginas = 1;
            try {
                $totalElementos = $modelo->contar();
                $totalPaginas = (int) max(1, ceil($totalElementos / $porPagina));
            } catch (\Throwable $e3) {
            }
            return view('tareas_lista', ['tareas' => $tareas, 'errorGeneral' => 'No se pudo eliminar la tarea.', 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas]);
        }
    }

    /**
     * Muestra una vista de confirmación antes de eliminar la tarea.
     *
     * @param int|string $id Identificador de la tarea.
     * @return mixed Vista de confirmación con datos de la tarea.
     */
    public function confirmarEliminar($id)
    {
        $modelo = new Tareas();
        try {
            $tarea = $modelo->buscar((int)$id);
        } catch (\Throwable $e) {
            $tarea = null;
        }
        if (!$tarea) {
            // Si no existe la tarea, mostrar listado con mensaje de error sin redirigir a raíz
            $tareas = [];
            try {
                $tareas = $modelo->listar();
            } catch (\Throwable $e2) {
                $tareas = [];
            }
            $porPagina = 20;
            $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($paginaActual < 1) $paginaActual = 1;
            $totalElementos = 0;
            $totalPaginas = 1;
            try {
                $totalElementos = $modelo->contar();
                $totalPaginas = (int) max(1, ceil($totalElementos / $porPagina));
            } catch (\Throwable $e3) {
            }
            return view('tareas_lista', ['tareas' => $tareas, 'errorGeneral' => 'No se pudo cargar la tarea para eliminación.', 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas]);
        }
        $tarea['id'] = (int)$id;
        return view('confirmarEliminar', $tarea);
    }

    /**
     * Valida y normaliza los datos recibidos del formulario de tarea.
     * Población de mensajes de error en `Funciones::$errores`.
     *
     * @return void
     */
    private function filtrar()
    {
        // Reiniciar errores y extraer datos del formulario
        Funciones::$errores = [];
        extract($_POST);

        if ($nifCif == "") {
            Funciones::$errores['nif_cif'] = "Debe introducir el NIF/CIF de la persona encargada de la tarea";
        } else {
            $resultado = Funciones::validarNif($nifCif);
            if ($resultado !== true) Funciones::$errores['nif_cif'] = $resultado;
        }

        if ($personaNombre === "") Funciones::$errores['nombre_persona'] = "Debe introducir el nombre de la persona encargada de la tarea";
        if ($descripcionTarea === "") Funciones::$errores['descripcion_tarea'] = "Debe introducir la descripción de la tarea";

        if ($correo === "") {
            Funciones::$errores['correo'] = "Debe introducir el correo de la persona encargada de la tarea";
        } else if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Funciones::$errores['correo'] = "El correo introducido no es válido";
        }

        if ($telefono == "") {
            Funciones::$errores['telefono'] = "Debe introducir el teléfono de la persona encargada de la tarea";
        } else {
            $resultado = Funciones::telefonoValido($telefono);
            if ($resultado !== true) Funciones::$errores['telefono'] = $resultado;
        }

        if ($codigoPostal != "" && !preg_match("/^[0-9]{5}$/", $codigoPostal)) {
            Funciones::$errores['codigo_postal'] = "El código postal introducido no es válido, debe tener 5 números";
        }

        if ($provincia === "") Funciones::$errores['provincia'] = "Debe introducir la provincia";

        $fechaActual = date('Y-m-d');
        if ($fechaRealizacion == "") {
            Funciones::$errores['fecha_realizacion'] = "Debe introducir la fecha de realización de la tarea";
        } else if ($fechaRealizacion <= $fechaActual) {
            Funciones::$errores['fecha_realizacion'] = "La fecha de realización debe ser posterior a la fecha actual";
        }
    }
}

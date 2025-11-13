<?php

namespace App\Http\Controllers;

use App\Models\Funciones;
use App\Models\Tareas;

class ControladorTareas extends Controller
{
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
                return view('tareas_lista', ['tareas' => $tareas, 'mensaje' => 'Tarea creada correctamente']);
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
        $datos = ['tareas' => $tareas];
        if ($error) $datos['errorGeneral'] = $error;
        return view('tareas_lista', $datos);
    }

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
                return view('tareas_lista', ['tareas' => $tareas, 'mensaje' => 'Tarea actualizada correctamente']);
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
        if (!$tarea) return redirect('/tareas');
        $tarea['id'] = (int)$id;
        return view('alta', $tarea);
    }

    public function eliminar($id)
    {
        $modelo = new Tareas();
        try {
            $modelo->eliminar((int)$id);
            $tareas = $modelo->listar();
            return view('tareas_lista', ['tareas' => $tareas, 'mensaje' => 'Tarea eliminada correctamente']);
        } catch (\Throwable $e) {
            try {
                $tareas = $modelo->listar();
            } catch (\Throwable $e2) {
                $tareas = [];
            }
            return view('tareas_lista', ['tareas' => $tareas, 'errorGeneral' => 'No se pudo eliminar la tarea.']);
        }
    }

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

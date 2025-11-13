<?php

namespace App\Http\Controllers;

use App\Models\Funciones;
use App\Models\ModeloVacio;
use App\Models\Tareas;

class AltaCtrl
{
    /* Recordad que hay que desactivar CSRF para permitir procesar el formulario en laravel
        sin problemas */

    public function alta()
    {
        if ($_POST) {
            // Tenemos que filtrar
            $this->filtraDatos();
            if (!empty(Funciones::$errores)) {
                return view('alta', $_POST);
            }
            else {
                // Procedemos a guardar los datos y mostrar la página que proceda
                $model=new Tareas();
                $model->registraAlta($_POST);
                return "Mostramos VENTANA QUE PROCEDA ".print_r($_POST, true);
            }
        } else {

            $datos = [
                'nifCif' => '',
                'personaNombre' => '',
                'telefono' => "",
                'descripcionTarea' => "",
                'correo' => "",
                'direccionTarea' => "",
                'poblacion' => "",
                'codigoPostal' => "",
                'provincia' => "",
                'estadoTarea' => "",
                'operarioEncargado' => "",
                'fechaRealizacion' => "",
                'anotacionesAnteriores' => "",
                'anotacionesPosteriores' => "",

            ];
            return view('alta', $datos);
        }
    }


    private function filtraDatos()
    {
        extract($_POST);


        if ($nifCif == "") {
            Funciones::$errores['nif_cif'] = "Debe introducir el NIF/CIF de la persona encargada de la tarea";
        } else {
            $resultado = Funciones::validarNif($nifCif);
            if ($resultado !== true) {
                Funciones::$errores['nifCif'] = $resultado;
            }
        }

        if ($personaNombre === "") {
            Funciones::$errores['nombre_persona'] = "Debe introducir el nombre de la persona encargada de la tarea";
        }

        if ($descripcionTarea === "") {
            Funciones::$errores['descripcion_tarea'] = "Debe introducir la descripción de la tarea";
        }

        if ($correo === "") {
            Funciones::$errores['correo'] = "Debe introducir el correo de la persona encargada de la tarea";
        } else if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Funciones::$errores['correo'] = "El correo introducido no es válido";
        }

        if ($telefono == "") {
            Funciones::$errores['telefono'] = "Debe introducir el teléfono de la persona encargada de la tarea";
        } else {
            $resultado = telefonoValido($telefono);
            if ($resultado !== true) {
                Funciones::$errores['telefono'] = $resultado;
            }
        }

        if ($codigoPostal != "" && !preg_match("/^[0-9]{5}$/", $codigoPostal)) {
            Funciones::$errores['codigo_postal'] = "El código postal introducido no es válido, debe tener 5 números";
        }

        if ($provincia === "") {
            Funciones::$errores['provincia'] = "Debe introducir la provincia";
        }

        $fechaActual = date('Y-m-d');
        if ($fechaRealizacion == "") {
            Funciones::$errores['fecha_realizacion'] = "Debe introducir la fecha de realización de la tarea";
        } else {
            if ($fechaRealizacion <= $fechaActual) {
                Funciones::$errores['fecha_realizacion'] = "La fecha de realización debe ser posterior a la fecha actual";
            }
        }
    }
}

<?php
namespace App\Http\Controllers;

/**
 * Controlador de la página de inicio.
 */
class ControladorInicio extends Controller
{
    /**
     * Muestra la vista principal de la aplicación.
     *
     * @return mixed Vista `index`.
     */
    public function inicio()
    {
        return view('index');
    }
}

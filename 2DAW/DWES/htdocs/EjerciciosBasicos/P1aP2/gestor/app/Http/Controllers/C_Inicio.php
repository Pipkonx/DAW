<?php
namespace App\Http\Controllers;

/**
 * Controlador de la página de inicio.
 */
class C_Inicio extends C_Controller
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

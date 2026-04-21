<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Muestra la página de Términos y Condiciones.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function terms()
    {
        return view('legal.terms');
    }

    /**
     * Muestra la página de Política de Privacidad.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function privacy()
    {
        return view('legal.privacy');
    }

    /**
     * Muestra la página de Aviso Legal.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function notice()
    {
        return view('legal.notice');
    }
}

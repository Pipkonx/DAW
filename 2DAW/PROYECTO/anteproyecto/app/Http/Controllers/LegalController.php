<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LegalController extends Controller
{
    /**
     * Muestra la página de Términos y Condiciones.
     *
     * @return Response
     */
    public function terms(): Response
    {
        return Inertia::render('Legal/Terms');
    }

    /**
     * Muestra la página de Política de Privacidad.
     *
     * @return Response
     */
    public function privacy(): Response
    {
        return Inertia::render('Legal/Privacy');
    }

    /**
     * Muestra la página de Aviso Legal.
     *
     * @return Response
     */
    public function notice(): Response
    {
        return Inertia::render('Legal/LegalNotice');
    }
}

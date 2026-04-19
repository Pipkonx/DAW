<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * La plantilla raíz que se carga en la primera visita a la página.
     */
    protected $rootView = 'app';

    /**
     * Determina la versión actual de los archivos (CSS, JS).
     */
    public function version(Request $request): ?string
    {
        /*
        |--------------------------------------------------------------------------
        | EXPLICACIÓN: parent::version($request)
        |--------------------------------------------------------------------------
        | "parent" significa "padre". Aquí le decimos a Laravel: 
        | "Ejecuta la función original que viene heredada del padre (Inertia)".
        | Básicamente, dejamos que Laravel haga su trabajo por defecto.
        */
        return parent::version($request);
    }

    /**
     * Define qué datos se comparten con todas las vistas automáticamente.
     */
    public function share(Request $request): array
    {
        return [
            /*
            | EXPLICACIÓN: ...parent::share($request)
            |--------------------------------------------------------------------------
            | "..." significa "copia todo lo que hay aquí dentro".
            | Le decimos: "Trae todos los datos que Laravel comparte por defecto,
            | y después añade los míos (como el usuario logueado)".
            */
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}

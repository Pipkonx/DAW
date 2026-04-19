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
        | "parent" hace referencia a la clase base. Aquí se ejecuta la lógica 
        | de versionado predeterminada de Inertia.
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
            |--------------------------------------------------------------------------
            | EXPLICACIÓN: ...parent::share($request)
            |--------------------------------------------------------------------------
            | El operador '...' (spread) se utiliza para incluir todos los 
            | datos compartidos por defecto de la clase base, permitiendo 
            | añadir atributos personalizados a continuación.
            */
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}

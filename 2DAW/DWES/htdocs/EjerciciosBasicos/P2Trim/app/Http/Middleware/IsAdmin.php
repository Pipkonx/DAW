<?php

namespace App\Http\Middleware;

/*
|--------------------------------------------------------------------------
| EXPLICACIÓN: use Closure;
|--------------------------------------------------------------------------
| Un "Closure" es simplemente una función que se puede guardar en una 
| variable y pasarse de un lado a otro. Aquí se usa para decirle a Laravel: 
| "Si todo está bien, pasa a la siguiente función ($next)".
*/
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Gestiona una petición que entra a la web.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está identificado y su rol es 'admin', le dejamos pasar
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Si no es admin, le cortamos el paso con un error 403
        abort(403, 'Acceso no autorizado. Tienes que ser administrador.');
    }
}

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php', // Aquí cargamos nuestras rutas del archivo web.php
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    /*
    |--------------------------------------------------------------------------
    | EXPLICACIÓN: function (Middleware $middleware) { ... }
    |--------------------------------------------------------------------------
    | Esto es una "función anónima" o "Closure". Le estamos dando una serie 
    | de instrucciones a Laravel para que configure los intermediarios (Middlewares) 
    | de nuestra web.
    */
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Aquí ponemos los "apodos" para nuestros middlewares
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'operator' => \App\Http\Middleware\IsOperator::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Aquí se configuraría qué hacer cuando hay errores graves
    })->create();

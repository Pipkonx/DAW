<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Nosecaen API Documentation",
    description: "API para la gestión de incidencias y tareas",
    contact: new OA\Contact(email: "soporte@nosecaen.com")
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Servidor de Desarrollo"
)]
/*
|--------------------------------------------------------------------------
| EXPLICACIÓN: __()
|--------------------------------------------------------------------------
| Esta función de ayuda (helper) se utiliza para la localización. 
| Permite buscar traducciones de cadenas de texto de manera dinámica, 
| facilitando la internacionalización de la aplicación.
*/
abstract class Controller
{
    //
}

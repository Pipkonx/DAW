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
| Esta función doble guion __() sirve para "traducir" textos. 
| Si escribes __('Login'), Laravel buscará si existe una traducción al 
| español para esa palabra. Si no la encuentra, pondrá 'Login' tal cual.
| Es muy útil para que tu web pueda estar en varios idiomas fácilmente.
*/
abstract class Controller
{
    //
}

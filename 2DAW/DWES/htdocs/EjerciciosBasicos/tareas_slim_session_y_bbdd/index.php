<?php
// Evitamos errores "deprecated" en php 8.1 que tenemos con la versión de jessengers blade
error_reporting(E_ERROR | E_WARNING | E_PARSE);
/* 
 * CONTROLADOR FRONTAL utilizando slim (Véase ejemplos anteriores para explicaciónes sobre como instalar
 */

// Estamos trabajando con espacios de nombres, hay objetos que queremos simplificar su nombre
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//
// URL en la que se encuentra la aplicación. Se precisa para crear los enlaces
// BASE_URL si utilizáis XAMMP será 
// http://localhost/carpeta/index.php/
//
// Si utilizamos como servidor el interprete de php ejecutando en el terminal
// php -S localhost:8000
define('BASE_URL', 'http://localhost:3000/index.php/');

require __DIR__ . '/vendor/autoload.php'; // Autocargador para los componentes instalados desde composer (en este caso Slim y blade)
require __DIR__ . '/ctes.php'; // definimos constantes que facilitan el trabajo

include(MODEL_PATH . 'Session.php');     // Clase session
include(CTRL_PATH . 'LoginCtrl.php');      // Controlador de tarea
include(CTRL_PATH . 'TareasCtrl.php');      // Controlador de tarea

// Habilitamos errores detallados para que nos informe de cualquier contratiempo
// https://www.slimframework.com/docs/v3/handlers/error.html
/**
 * Instantiate App
 * Creamos la aplicación
 */
$app = new \Slim\App(['settings' => ['displayErrorDetails' => true,],]);

// Definimos rutas que procesamos

//
//  RUTAS PARA LOGIN/LOGOUT
//

// Página de login (observad que entramos por get/post/put/... al poner any())
$app->any('/login', function (Request $request, Response $response, $args) {
    return LoginCtrl::getInstance()->login();
});

// Página de logout
$app->get('/logout', function (Request $request, Response $response, $args) {
    LoginCtrl::getInstance()->logout();
});


//
// RUTAS QUE PROCESAN LAS TAREAS
//

// Página principal
$app->get('/', function (Request $request, Response $response, $args) {
    /* Session->onlyLogged() chequea si esta logeado y si no está devuelve una redirección
    y finaliza el script (no continua el código)
    */
    Session::getInstance()->onlyLogged();
    return (new TareasCtrl())->Inicio();
});

// Listar
$app->get('/listar', function (Request $request, Response $response, $args) {
    Session::getInstance()->onlyLogged();
    return (new TareasCtrl())->Listar();
});

// Alta
// Observad que aquí no se pone 'get' pues la petición puede llegar por GET o por POST
$app->post('/add', function (Request $request, Response $response, $args) {
    Session::getInstance()->onlyLogged();
    return (new TareasCtrl())->Add();
});

// Alta
// Repetimos ruta con GET. Lo más sencillo hubiese sido poner $app->any(...)
$app->get('/add', function (Request $request, Response $response, $args) {
    Session::getInstance()->onlyLogged();
    return (new TareasCtrl())->Add();
});

// Modificar
$app->any('/edit', function (Request $request, Response $response, $args) {
    Session::getInstance()->onlyLogged();
    return (new TareasCtrl())->Edit();
});

// Borrar
$app->get('/del', function (Request $request, Response $response, $args) {
    Session::getInstance()->onlyLogged();
    return (new TareasCtrl())->Del();
});


// Página de prueba
$app->get('/pag1', function (Request $request, Response $response, $args) {
    return (new TareasCtrl())->Pag1();
});


// Run app
$app->run();

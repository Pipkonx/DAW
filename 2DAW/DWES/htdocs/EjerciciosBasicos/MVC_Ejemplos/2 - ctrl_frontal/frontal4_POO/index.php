<?php
/* 
 * CONTROLADOR FRONTAL - Aproximación 4ª
 * 
 * Todas las peticiones pasaran por esta página
 * 
 * Enfoque orientado a objetos usando objetos para Controlador, ControladorFrontal
 * Modelo
 * 
 */

// definimos constantes que facilitan el trabajo
define('APP_PATH', __DIR__.'/');
define('CTRL_PATH', __DIR__.'/ctrl/');
define('MODEL_PATH', __DIR__.'/model/');
define('VIEW_PATH', __DIR__.'/view/');
define('TEMPLATE_PATH', __DIR__.'/plantilla/');
define('LIB_PATH', __DIR__.'/lib/');
define('HELPERS_PATH', __DIR__.'/helpers/');

include (HELPERS_PATH.'vistas.php');
include (CTRL_PATH.'Front_controller.php');

$FC=Front_Controller::getInstance('Tareas');
$FC->Run();

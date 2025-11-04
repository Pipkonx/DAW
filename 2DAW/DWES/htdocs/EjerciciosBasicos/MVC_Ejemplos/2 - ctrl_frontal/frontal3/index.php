<?php
/* 
 * CONTROLADOR FRONTAL - Aproximación 2ª
 * 
 * Todas las peticiones pasaran por esta página
 * 
 * En este enfoque en el controlador frontal se dedica unica y exclusivamente
 * a dirigir la petión sin presentar nada. De eso se encargará el controlador
 * 
 */

// definimos constantes que facilitan el trabajo
define('CTRL_PATH', __DIR__.'/ctrl/');
define('MODEL_PATH', __DIR__.'/model/');
define('VIEW_PATH', __DIR__.'/view/');
define('TEMPLATE_PATH', __DIR__.'/plantilla/');
define('LIB_PATH', __DIR__.'/lib/');
define('HELPERS_PATH', __DIR__.'/helpers/');

include (HELPERS_PATH.'vistas.php');

// Usamos constantes por comodidad
DEFINE('CTRL_VAR', 'c');
DEFINE('CTRL_HOME', '1');
DEFINE('CTRL_EDIT', '2');
DEFINE('CTRL_LIST', '3');
DEFINE('CTRL_PAG1', '6');

$mapaAcciones=array(
    CTRL_HOME=>'inicio',
    CTRL_EDIT=>'edit',
    CTRL_LIST=>'listar',
    CTRL_PAG1=>'pag1',
);
        
// Cuerpo del controlador frontal
$accion = isset($_GET[CTRL_VAR]) ? $_GET[CTRL_VAR] : CTRL_HOME;

if (isset($mapaAcciones[$accion]))
{
    // Nombre del fichero a incluir
    $ctrl_file=CTRL_PATH.$mapaAcciones[$accion].'.php';
    if (file_exists($ctrl_file))
    {
        include($ctrl_file);
    }
    else
    {   // Error 404
        echo CargaVista('plantilla/layout', array(
            'titulo'=>'Página no encontrada',
            'menu'=>CargaVista('plantilla/menu'),
            'cuerpo'=>CargaVista('error404', array('file'=>$ctrl_file)),
        ));
    }
}
else 
{
    // Error 404
     echo CargaVista('plantilla/layout', array(
         'titulo'=>'Acción inexistente',
         'menu'=>CargaVista('plantilla/menu'),
         'cuerpo'=>'<div>No está definida la acción indicada</div>',
     ));
}
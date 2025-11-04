<?php
/* 
 * CONTROLADOR FRONTAL - Aproximación 1ª
 * 
 * Todas las peticiones pasaran por esta página
 * 
 * En este enfoque en el controlador frontal mezclamos vista y código
 * de control.
 * 
 * Además tenemos el problema de que se ha enviado código al cliente antes
 * de completar la acción, por lo que no podemos redirigir a otras páginas
 * enviando un encabezado.
 * 
 */

// definimos constantes que facilitan el trabajo
define('CTRL_PATH', __DIR__.'/ctrl/');
define('MODEL_PATH', __DIR__.'/model/');
define('VIEW_PATH', __DIR__.'/view/');
define('TEMPLATE_PATH', __DIR__.'/plantilla/');
define('LIB_PATH', __DIR__.'/lib/');
define('HELPERS_PATH', __DIR__.'/helpers/');

?>
<html>
    <head>
        <title>Controlador frontal</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>    
<body>
<?php 
include(TEMPLATE_PATH.'encabezado.php');
include(TEMPLATE_PATH.'menu.php'); 

// Cuerpo del controlador frontal
$ctrl=isset($_GET['ctrl']) ? $_GET['ctrl'] : 'inicio';

// Nombre del fichero a incluir
$file=CTRL_PATH.$ctrl.'.php';
if (file_exists($file))
{
    include($file);
}
else
{   // Error 404
    include(CTRL_PATH.'error404.php');
}

include(TEMPLATE_PATH.'pie.php');
?>
</body>
</html>

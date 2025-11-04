<?php
/**
 * VISTA QUE MUESTA LA LISTA DE TAREAS.
 * El controlador será el que nos proporcine en la variable $tareas
 * las tareas a mostrar
 */
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
?>
<h1>EDITANDO TAREA</h1>
<p>Se ha guardado la tarea ...</p>
<?php
include(TEMPLATE_PATH.'pie.php');
?>
</body>
</html>
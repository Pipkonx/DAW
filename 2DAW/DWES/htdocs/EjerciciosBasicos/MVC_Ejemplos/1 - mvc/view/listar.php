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
<h1>Listado de tareas</h1>
<table>
    <tr>
        <td>Id</td>
        <td>Nombre</td>
        <td></td>
    </tr>
<?php foreach($tareas as $tarea) : ?>
    <tr>
        <td><?=$tarea['id']?></td>
        <td><?=$tarea['nombre']?></td>
        <td><a href="edit.php?id=<?=$tarea['id']?>">Modificar</a></td>
    </tr>
<?php endforeach; ?>
</table>
<?php
include(TEMPLATE_PATH.'pie.php');
?>
</body>
</html>
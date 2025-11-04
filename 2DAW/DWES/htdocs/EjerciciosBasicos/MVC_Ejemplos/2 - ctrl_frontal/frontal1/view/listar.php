<?php
/**
 * VISTA QUE MUESTA LA LISTA DE TAREAS.
 * El controlador será el que nos proporcine en la variable $tareas
 * que contiene las tareas a mostrar
 */
?>
<h1>Listado de tareas</h1>
<table>
    <tr>
        <td>Id</td>
        <td>Nombre</td>
        <td>Prioridad</td>
        <td></td>
    </tr>
<?php foreach($tareas as $tarea) : ?>
    <tr>
        <td><?=$tarea['id']?></td>
        <td><?=$tarea['nombre']?></td>
        <td><?=$tarea['prioridad']?></td>
        <td>
            <a href="?ctrl=edit&id=<?=$tarea['id']?>">[Modificar]</a> 
            <a href="?ctrl=del&id=<?=$tarea['id']?>">[Borrar]</a> 
        </td>
    </tr>
<?php endforeach; ?>
</table>
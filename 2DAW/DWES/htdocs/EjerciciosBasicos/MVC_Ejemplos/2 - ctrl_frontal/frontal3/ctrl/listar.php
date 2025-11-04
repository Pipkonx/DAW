<?php
/* Muesta la lista de tareas */

include(MODEL_PATH.'tareas.php');

$tareas=  GetTareas();

// En un planteamiento real puede que incluyesemos más cosas
echo CargaVista('plantilla/layout', array(
    'titulo'=>'Listado de tarea',
    'menu'=>CargaVista('plantilla/menu'),
    'cuerpo'=>CargaVista('listar', array('tareas'=>$tareas)),
));


<?php
/* Muesta la lista de tareas */

include(MODEL_PATH.'tareas.php');

$tareas=  GetTareas();

// En un controlador real esto haría más cosas
include(VIEW_PATH.'listar.php');


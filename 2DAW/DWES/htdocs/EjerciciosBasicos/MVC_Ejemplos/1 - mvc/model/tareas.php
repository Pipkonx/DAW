<?php
$model_tareas=array(
    array('id'=>1, 'nombre'=>'tarea1','prioridad'=>1),
    array('id'=>2, 'nombre'=>'tarea2','prioridad'=>1),
    array('id'=>4, 'nombre'=>'tarea4','prioridad'=>1),
    array('id'=>5, 'nombre'=>'tarea5','prioridad'=>1),
    array('id'=>10, 'nombre'=>'tarea10','prioridad'=>1),
);

/**
 * Devuelve las tareas existentes.
 * Simulamos lectura de base de datos
 * @global array $model_tareas
 * @return array
 */
function GetTareas()
{
    global $model_tareas;
    return $model_tareas;
}

/**
 * Devuelve los datos de una tarea
 * @param type $id
 * @return boolean
 */
function GetTarea($id)
{
    global $model_tareas;
    return $model_tareas[$id];
}


/**
 * Guarda la tarea
 */
function SaveTarea($id, $datos_tarea)
{
    // No hacemos nada pues no podemos guardar nada en el código
    // Simulamos como si estuviese implementado
}

/**
 * Guarda una nueva la tarea
 */
function NuevaTarea($datos_tarea)
{
    // No hacemos nada pues no podemos guardar nada en el código
    // Simulamos como si estuviese implementado

    // return id;
}
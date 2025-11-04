<?php
$model_tareas=array(
    1=>array('id'=>1, 'nombre'=>'tarea1', 'prioridad'=>1),
    2=>array('id'=>2, 'nombre'=>'tarea2', 'prioridad'=>1),
    4=>array('id'=>4, 'nombre'=>'tarea4', 'prioridad'=>1),
    5=>array('id'=>5, 'nombre'=>'tarea5', 'prioridad'=>1),
    10=>array('id'=>10, 'nombre'=>'tarea10', 'prioridad'=>1),
);

/**
 * Devuelve las tareas existentes.
 * Simulamos lectura de base de datos
 * @return array
 */
function GetTareas()
{
    return $GLOBALS['model_tareas'];
}

/**
 * Devuelve los datos de una tarea
 * @param type $id
 * @return boolean
 */
function GetTarea($id)
{
    if (! isset($GLOBALS['model_tareas'][$id]))
    {
        return FALSE;
    }
    else
    {
        return $GLOBALS['model_tareas'][$id];
    }
}

/**
 * Guarda los datos de una tarea
 * @param int $id
 * @param array $tarea
 */
function SaveTarea($id, $tarea)
{
    // Guarda los datos de una tarea
    // NO hacemos nada pues no tiene sentido tal como estamos guardando los
    // datos. No serviría para nada
}




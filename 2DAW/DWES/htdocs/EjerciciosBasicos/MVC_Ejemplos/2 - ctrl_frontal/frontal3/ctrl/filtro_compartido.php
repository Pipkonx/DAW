<?php
/**
 * Realiza el filtrado de campos y almacena los errores en el gestor de errores
 * @param GestorErrores $errores
 */
function FiltraCamposPost(GestorErrores $errores)
{
    // Filtramos el nombre
    if (VPost('nombre')=='')
    {
        $errores->AnotaError('nombre', 'Se debe introducir texto');
    } 
    else if ( strlen(VPost('nombre'))<5)
    {
        $errores->AnotaError('nombre', 'El nombre debe tener al menos 5 letras');
    }
    
    // Filtramos la prioridad
    $prioridad=VPost('prioridad');
    if ($prioridad=='')
    {
        $errores->AnotaError('prioridad', 'Se debe introducir texto');
    } 
    else if ( !is_numeric($prioridad) || ($prioridad<1 || $prioridad>5))
    {
        $errores->AnotaError('prioridad', 'La prioridad debe ser un número entre 1 y 5');
    }
}
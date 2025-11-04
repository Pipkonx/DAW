<?php
include('constantes.php');
// Librerias
include(LIB_PATH.'GestorErrores.php');

// Modelo
include(MODEL_PATH.'tareas.php');
include(HELPERS_PATH.'form.php');

// Inicializamos el gestor de errores que utilizaremos en la vista
$errores=new GestorErrores('<span style="color:red; background:#EEE; padding:.2em 1em; margin:1em">', '</span>');

if (! isset($_GET['id']))
{
    // No existe la tarea, error
    $descripcion_error='No existe la tarea seleccionada';
    include(VIEW_PATH.'edit_error.php');
}
else
{ // Han indicado el id
    $id=$_GET['id'];


    if (! $_POST)
    {
        // Primera vez.
        // Leo el regitro y muestro los datos
        $tarea=GetTarea($id);
        if (! $tarea )
        {
            // No existe la tarea, error
            $descripcion_error='No existe la tarea seleccionada';
            include(VIEW_PATH.'edit_error.php');
        }
        else
        {
            // Mostramos los datos
            include(VIEW_PATH.'edit.php');
        }
    }
     else {
         // Filtrar datos
         include ('filtro_compartido.php');
         FiltraCamposPost($errores);

        // Creamos el objeto tarea que es el que se utiliza en el formulario
        // Lo creamos a partir de los datos recibidos del POST
        $tarea=array(
            'nombre'=>  VPost('nombre'),
            'prioridad'=>VPost('prioridad')
        );

         if ($errores->HayErrores())
         {
             // Mostrar ventana de nuevo
             include(VIEW_PATH.'edit.php');
         }
         else
         {
             // Guardamos la tarea
             SaveTarea($id, $tarea);

             include(VIEW_PATH.'edit_saved.php');
         }
    }
} // de Han indicado el id


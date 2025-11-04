<?php
include (LIB_PATH.'GestorErrores.php');
include (HELPERS_PATH.'form.php');
include (MODEL_PATH.'tareas.php');

// Inicializamos el gestor de errores que utilizaremos en la vista
$errores=new GestorErrores('<span style="color:red; background:#EEE; padding:.2em 1em; margin:1em">', '</span>');

if (! isset($_GET['id']))
{
    // No existe la tarea, error
    
    $htmlCuerpo=  CargaVista('edit_error', array(
        'descripcion_error'=>'No existe la tarea seleccionada'
    ));
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
            $htmlCuerpo=  CargaVista('edit_error', array(
                'descripcion_error'=>'No existe la tarea seleccionada'
            ));
        }
        else
        {
            // Mostramos los datos
            $htmlCuerpo=  CargaVista('edit', array('tarea'=>$tarea, 'errores'=>$errores));            
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
            $htmlCuerpo=  CargaVista('edit', array('tarea'=>$tarea, 'errores'=>$errores));            
         }
         else
         {
             // Guardamos la tarea
             SaveTarea($id, $tarea);

             $htmlCuerpo="<p>Se ha guardado la tarea ....</p>";
         }
    }
} // de Han indicado el id

// Al llegar aquí aun no he devuelto nada, podría hacer cualquier tipo de acción

// Muestro la plantilla
    echo CargaVista('plantilla/layout', array(
        'titulo'=>'Edición - xxxx',
        'menu'=>CargaVista('plantilla/menu_1'),
        'cuerpo'=>$htmlCuerpo,
    ));

<?php
include('constantes.php');
// Librerias
include(LIB_PATH.'GestorErrores.php');

// Modelo
include(MODEL_PATH.'tareas.php');
include(HELPERS_PATH.'form.php');

// Inicializamos el gestor de errores que utilizaremos en la vista
$errores=new GestorErrores('<span style="color:red; background:#EEE; padding:.2em 1em; margin:1em">', '</span>');

if (! $_POST)
{
    // Primera vez.
    // Mostramos los formulario
        include(VIEW_PATH.'new.php');
}
else {
    // Filtrar datos
    include ('filtro_compartido.php');
    FiltraCamposPost($errores);

    if ($errores->HayErrores())
    {
        // Mostrar ventana de nuevo
        include(VIEW_PATH.'new.php');
    }
    else
    {
        // Guardamos la tarea
        NuevaTarea(['nombre'=>VPost('nombre')]);

        include(VIEW_PATH.'edit_saved.php');
    }
}


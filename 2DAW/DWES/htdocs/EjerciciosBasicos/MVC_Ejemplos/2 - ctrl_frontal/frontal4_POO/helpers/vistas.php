<?php

/**
 * Función que carga una vista y la devuelve como una cadena
 * @param string $vista
 * @param array $variablesDeVista
 * @return string
 */
function CargaVista($vista, array  $variablesDeVista=NULL)
{
    $ficheroVista=VIEW_PATH.$vista.'.php';
	if (! file_exists($ficheroVista)) {	
        // Nada que incluir
		$htmlError="<div>No existe la vista $vista<br/>Fichero:$ficheroVista</div>"; 
		return $htmlError; 
	}

    if (is_array($variablesDeVista))
    {
        // Creamos variables que hemos pasado en el array para que sean locales
        // de la función
        extract($variablesDeVista);
    }

    /* La función anterior hace lo mismo que  el siguiente código */
    /*
        foreach($variablesDeVista as $__nombreVariable__=>$__valorVariable__)
        {   // OJO al doble $
            $__nombreVariable__=$__valorVariable__;
        }
    */
    
	// Interpretamos plantilla
	ob_start();
	include($ficheroVista);
	$html = ob_get_clean();
	return $html;
}

/**
 * Muestra una vista
 * @param type $vista
 * @param array $variablesDeVista
 */
function MuestraVista($vista, array $variablesDeVista=NULL)
{
    echo CargaVista($vista, $variablesDeVista);
}
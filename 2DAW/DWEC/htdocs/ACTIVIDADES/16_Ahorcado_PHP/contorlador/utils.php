<?php
/**
 * Redirige al navegador a una nueva ubicación y termina la ejecución del script.
 *
 * @param string $path La URL a la que se debe redirigir.
 */
function redirect($path)
{
    header('Location: ' . $path);
    exit;
}

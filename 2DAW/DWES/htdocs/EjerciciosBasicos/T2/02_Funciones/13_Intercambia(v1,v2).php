<?php

// Crea la función Intercambia(v1, v2) la cual intercambiará el valor de las dos variables.
// Realizar una página en la que se pruebe el funcionamiento de dicha función
// intercambiando el valor de dos variables. Mostrar las variables antes y después de la
// invocación de la función.

$v1 = 1;
$v2 = 2;

function Intercambia($v1, $v2)
{
    $v1 = 10;
    $v2 = $v1 + $v2;
    return $v1 . " " . $v2;
}


echo $v1 . " " . $v2 . " Con funcion : " . Intercambia($v1, $v2);

<?php

// Crea la función EstNoSeDebeHacer() -sin parámetros, que hará uso de la palabra
// reservada global- que modifica la variable $num asignándole el doble de su valor. La
// variable está iniciada fuera de la función. Crea una página que cree y pruebe el
// funcionamiento de la función

function EstNoSeDebeHacer() {
    global $num;
    $num = $num * 2;
}

$num = 5;
EstNoSeDebeHacer();
print($num);

<?php

// Comprueba si los arrays se pasan por valor o referencia como parámetros de una función.

// Modifica los datos de una array pasado como parámetro a una función y comprueba si se
// han modificado al salir de esta.

$coche5 = [
    "123456",
    "azul",
    "2023",
    "Seat",
];

echo $coche5[0] . "<br>";

$coche2 = $coche5;
    $coche2[0] = "nuevo valor";
    echo $coche5[0] . "<br>";


function modificarReferencia(Array $coche5) {
    $coche5[] = "ano";
    $coche5["ano"] = "ano = valor modificado";
    echo $coche5["ano"]  . "<br>";
}



modificarReferencia($coche5);
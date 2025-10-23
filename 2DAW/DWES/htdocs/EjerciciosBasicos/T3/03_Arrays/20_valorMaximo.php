<?php

//Crea la función Max(array) que nos devolverá el valor máximo de un array. Realiza una página que pruebe dicha función

$array = [1, 2, 3, 4, 5, 6, 7, 8, 9];

unset($array[1]);

function maximo($array)
{
    $mayor = 0;
    // for ($i = 0; $i < count($array); $i++) {
    //     if ($array[$i] > $mayor) {
    //         $mayor = $array[$i];
    //     }
    // }

    foreach ($array as $valor) {
        if ($valor > $mayor) {
            $mayor = $valor;
        }
    }
    return $mayor;
}

echo maximo($array);

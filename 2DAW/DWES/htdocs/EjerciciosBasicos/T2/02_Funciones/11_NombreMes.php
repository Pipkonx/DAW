<?php

// Crea la función NombreMes(num_mes) que devolverá una cadena que será el nombre de mes que corresponde al parámetro. Modifica el ejercicio anterior para que en cada línea aparezca el nombre de més y el año y a continuación solo aparezca el número de día.

$num_mes = 4 - 1;

function NombreMes($num_mes) {
    $mes = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    return $mes[$num_mes];


    // switch ($num_mes) {
    //     case 1:
    //         return "Enero";
    //         break;
    //     case 2:
    //         return "Febrero";
    //         break;
    //     case 3:
    //         return "Marzo";
    //         break;
    //     case 4:
    //         return "Abril";
    //         break;
    //     case 5:
    //         return "Mayo";
    //         break;
    //     case 6:
    //         return "Junio";
    //         break;
    //     case 7:
    //         return "Julio";
    //         break;
    //     case 8:
    //         return "Agosto";
    //         break;
    //     case 9:
    //         return "Septiembre";
    //         break;
    //     case 10:
    //         return "Octubre";
    //         break;
    //     case 11:
    //         return "Noviembre";
    //         break;
    //     case 12:
    //         return "Diciembre";
    //         break;
    // }
};

echo NombreMes($num_mes);
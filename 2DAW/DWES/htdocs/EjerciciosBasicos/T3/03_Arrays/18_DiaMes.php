<?php

// Utilizando arrays crea la función DiasMes(num_mes) que devolverá un entero que será el número de días que tiene un mes. Utilizando dicha función realiza un programa que imprima el número de días que tienes los distintos meses. El nombre del mes se almacenará en una array igualmente.

$mes = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

function DiaMes($mes){
    $dia = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    return $dia[$mes];
}

for ($i= 0; $i < 12 ; $i++ ) {
    echo $mes[$i] . " tiene " . DiaMes($i) . " dias<br> ";
}
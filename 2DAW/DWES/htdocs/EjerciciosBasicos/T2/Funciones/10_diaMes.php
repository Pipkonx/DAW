<?php
// Crea la función DiasMes(num_mes) que devolverá un entero que será el número de días 
// que tiene un mes. Utilizando dicha función realizar un programa que imprima las fechas 
// existentes entre el 1 de enero de 1999 y el 31 de diciembre de 2012. Las fechas se mostrarán 
// separadas por una coma y cada mes aparecerá en una línea diferentes.

function diaMes(int $num_mes = 1): int
{
    switch ($num_mes) {
        case 1;
        case 3;
        case 5;
        case 7;
        case 8;
        case 10;
        case 12:
            return 31;
        case 2:
            return 28;
        default:
            return 30;
    }
}
echo (diaMes(2));

// fechas existentes entre el 1 de enero de 1999 y el 31 de diciembre de 2012. Las fechas se mostrarán separadas por una coma y cada mes aparecerá en una línea diferentes.

for ($year = 1999 ; $year = 2012 ;)


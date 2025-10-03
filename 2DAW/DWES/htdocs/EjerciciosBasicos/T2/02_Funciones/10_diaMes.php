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

for ($year = 1999 ; $year <= 2012 ; $year++) {
    for ($month = 1 ; $month <= 12 ; $month++) {
        echo "<br><br>";
        for ($day = 1 ; $day <= diaMes($month) ; $day++) {
            echo "$day/$month/$year, ";
        }
    }
}
echo (diaMes(2));

// Crea la función NombreMes(num_mes) que devolverá una cadena que será el nombre de mes que corresponde al parámetro. Modifica el ejercicio anterior para que en cada línea aparezca el nombre de més y el año y a continuación solo aparezca el número de día.

function NombreMes($num_mes = 1) {

}

// 12. Crea la función EstNoSeDebeHacer() -sin parámetros, que hará uso de la palabra reservada global- que modifica la variable $num asignándole el doble de su valor. La variable está iniciada fuera de la función. Crea una página que cree y pruebe el funcionamiento de la función.



// 13. Crea la función Intercambia(v1, v2) la cual intercambiará el valor de las dos variables. Realizar una página en la que se pruebe el funcionamiento de dicha función intercambiando el valor de dos variables. Mostrar las variables antes y después de la invocación de la función.



// 14. Utilizando la función predefinida date(), realiza una página en la que se muestre la fecha y hora actual.




// 15. Utilizando la función date() y time() escribe una página que muestre la fecha que será dentro de 50 segundos, y dentro de 2 horas, 4 minutos y 3 segundos.



// 16. Utilizando la función date() y la función NombreMes() creada anteriormente, muestra el nombre del mes en el que estamos.



// Crea la función en el fichero “funciones_fecha.php” que luego incluirás (include o require) en la solución.



// 17. Crea la función MuestraFecha(dia, mes, anyo) que mostrará la fecha que se le pase como parámetro en el formato “dia _semana (lunes...), num_dia de nombre_mes de num_anyo”.


// ! REALIZAR EJERCICIOS DE FUNCIONES

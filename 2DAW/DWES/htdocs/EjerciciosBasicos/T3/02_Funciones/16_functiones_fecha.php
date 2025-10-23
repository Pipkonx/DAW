<?php

// Utilizando la función date() y la función NombreMes() creada anteriormente, muestra el
// nombre del mes en el que estamos.
// Crea la función en el fichero “funciones_fecha.php” que luego incluirás (include o require)
// en la solución


include("11_NombreMes.php");
include("10_diaMes.php");

$num_mes = 9;

echo "<br>El mes {$num_mes} tiene " . diaMes($num_mes) . " días y corresponde al mes: " . nombreMes($num_mes);

<?php

// Utilizando la función date() y la función NombreMes() creada anteriormente, muestra el
// nombre del mes en el que estamos.
// Crea la función en el fichero “funciones_fecha.php” que luego incluirás (include o require)
// en la solución


include("11_NombreMes.php");
include("10_diaMes.php");

// Obtener el número del mes actual (1-12)
$num_mes = date('n');

// Mostrar el nombre del mes actual
echo NombreMes($num_mes);

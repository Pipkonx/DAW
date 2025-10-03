<?php

// Crea la función MuestraFecha(dia, mes, anyo) que mostrará la fecha que se le pase como
// parámetro en el formato “dia _semana (lunes...), num_dia de nombre_mes de num_anyo”.
// Ejemplo: MuestraFecha(9,10,2018) mostrará
// martes 9 de octubre de 2018 / o / Tuesday 9 october 2018
// Prueba la función.
// Utiliza el fichero “funciones_fecha.php” creado anteriormente


include("10_diaMes.php");
include("11_NombreMes.php");

function MuestraFecha($dia, $mes, $anyo)
{
    $nombre = nombreMes($mes);
    $dia_semana = ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado", "Domingo"];
    return "<center>El día {$dia} de {$nombre} del año {$anyo} era {$dia_semana[($dia / 7) - 1]} </center>";
}
echo MuestraFecha(30, 9, 2025);
$mes = 10;
$num_mes = $mes;

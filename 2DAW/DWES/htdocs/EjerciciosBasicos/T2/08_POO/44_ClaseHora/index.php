<?php
// Utilizando la clase Hora, creada anteriormente, realiza un programa, que nos permita crear
// una tabla de tareas con el siguiente formato:
//     | Hora | Tarea |
//     | ---- | ----- |
//     | 08:00 | Tarea 1 |
//     | 09:00 | Tarea 2 |
//     | 10:00 | Tarea 3 |
// Las tareas a mostrar serán las que tengáis almacenadas en un array, como “tarea 1”,  “tarea 2”, etc.
// La hora de inicio será las 9:00, la de finalización las 15:00, y se mostrarán las horas en intervalos de 45 min.

// con require_once es para incluir el archivo
//! DUDA

require '../43_DefinirSiguienteClase/index.php';

$tareas = ["Tarea1", "Tarea2", "Tarea3", "Tarea4", "Tarea5", "Tarea6", "Tarea7", "Tarea8"];
$indiceTarea = 0;

$horaActual = new Hora(9, 0);
$horaFin = new Hora(15, 0);

echo "<table border='1'><tr><th>Hora</th><th>Tarea</th></tr>";

while ($horaActual->EsMenor($horaFin)) {
    echo "<tr><td>" . $horaActual->getHora() . ":" . str_pad($horaActual->getMinutos(), 2, "0", STR_PAD_LEFT) . "</td>";
    echo "<td>" . $tareas[$indiceTarea % count($tareas)] . "</td></tr>";

    $horaActual->incrementarMinutos(45);
    $indiceTarea++;
}

echo "</table>";

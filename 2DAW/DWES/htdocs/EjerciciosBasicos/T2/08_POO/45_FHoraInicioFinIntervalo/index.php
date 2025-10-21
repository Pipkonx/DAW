<?php
// Amplia el ejercicio anterior para que mediante un formulario podamos escoger la hora de inicio, la hora de fin, y el intervalo

require '../43_DefinirSiguienteClase/index.php';

$tareas = ["Tarea1", "Tarea2", "Tarea3", "Tarea4", "Tarea5", "Tarea6", "Tarea7", "Tarea8"];

$horaInicio = 9;
$minInicio = 0;
$horaFin = 15;
$minFin = 0;
$intervalo = 45;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $horaInicio = isset($_POST['horaInicio']) ? (int)$_POST['horaInicio'] : 9;
    $minInicio = isset($_POST['minInicio']) ? (int)$_POST['minInicio'] : 0;
    $horaFin = isset($_POST['horaFin']) ? (int)$_POST['horaFin'] : 15;
    $minFin = isset($_POST['minFin']) ? (int)$_POST['minFin'] : 0;
    $intervalo = isset($_POST['intervalo']) ? (int)$_POST['intervalo'] : 45;
}

$horaActual = new Hora($horaInicio, $minInicio);
$horaFinal = new Hora($horaFin, $minFin);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planificador de Tareas</title>
    <style>
        table {
            border-collapse: collapse;
            width: 50%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Planificador de Tareas</h1>
    
    <form method="post" action="">
        <div>
            <label for="horaInicio">Hora de inicio:</label>
            <input type="number" id="horaInicio" name="horaInicio" min="0" max="23" value="<?php echo $horaInicio; ?>">
            <label for="minInicio">Minutos:</label>
            <input type="number" id="minInicio" name="minInicio" min="0" max="59" value="<?php echo $minInicio; ?>">
        </div>
        
        <div>
            <label for="horaFin">Hora de fin:</label>
            <input type="number" id="horaFin" name="horaFin" min="0" max="23" value="<?php echo $horaFin; ?>">
            <label for="minFin">Minutos:</label>
            <input type="number" id="minFin" name="minFin" min="0" max="59" value="<?php echo $minFin; ?>">
        </div>
        
        <div>
            <label for="intervalo">Intervalo (min):</label>
            <input type="number" id="intervalo" name="intervalo" min="1" max="120" value="<?php echo $intervalo; ?>">
        </div>
        
        <button type="submit">Generar Tabla</button>
    </form>
    
    <?php
    echo "<h2>Tabla de Tareas</h2><table><tr><th>Hora</th><th>Tarea</th></tr>";
    
    $indiceTarea = 0;
    
    // Generamos las filas de la tabla mientras la hora actual sea menor que la hora final
    while ($horaActual->EsMenor($horaFinal)) {
        echo "<tr><td>" . $horaActual->getHora() . ":" . str_pad($horaActual->getMinutos(), 2, "0", STR_PAD_LEFT) . "</td>";
        echo "<td>" . $tareas[$indiceTarea % count($tareas)] . "</td></tr>";
        
        // Incrementamos la hora actual según el intervalo
        $horaActual->incrementarMinutos($intervalo);
        $indiceTarea++;
    }
    
    echo "</table>";
    ?>
</body>
</html>
<?php
header('Content-Type: text/plain; charset=utf-8');

include 'conexion.php';

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
$id = $_GET["id"];

$sql = "SELECT * FROM notas WHERE codigoAlumno=" . intval($id);
$resultado = $conexion->query($sql);

$lineas = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $asignatura = isset($fila['asignatura']) ? $fila['asignatura'] : (isset($fila['codigoAsignatura']) ? $fila['codigoAsignatura'] : 'Asignatura');
        $nota = isset($fila['nota']) ? $fila['nota'] : '';
        $lineas[] = $asignatura . ': ' . $nota;
    }
    echo implode("\n", $lineas);
} else {
    echo "No hay registros en la tabla notas.";
}

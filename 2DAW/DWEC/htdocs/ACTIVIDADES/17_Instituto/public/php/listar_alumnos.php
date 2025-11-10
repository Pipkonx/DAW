<?php
require_once "Conexion.php";

// alumnos
$sqlAlumnos = "SELECT * FROM alumnos ORDER BY id";
$resultado = $con->query($sqlAlumnos);

$alumnos = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $alumnos[] = $fila;
    }
    echo json_encode($alumnos);
} else {
    echo "No se encontraron alumnos.";
}
<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/conexion.php';

$sql = "SELECT * FROM provincias";
$resultado = $conn->query($sql);
$datos = array();
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }
}
echo json_encode($datos);
$conn->close();
?>

<?php
include 'conexion.php';

header('Content-Type: application/json');

$response = ['success' => false, 'compras' => []];

$sql = "SELECT marca, modelo, precio_final FROM compras ORDER BY id DESC";
$result = $conexion->query($sql);

if ($result) {
    $response['success'] = true;
    while ($row = $result->fetch_assoc()) {
        $response['compras'][] = $row;
    }
} else {
    error_log("Error al obtener compras: " . $conexion->error);
}

$conexion->close();
echo json_encode($response);
?>
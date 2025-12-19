<?php
header('Content-Type: application/json; charset=utf-8');
include 'conexion.php';

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conexion->connect_error]));
}

if (!isset($_POST['id_modelo']) || !isset($_POST['precio'])) {
    echo json_encode(["error" => "Parámetros insuficientes."]);
    exit();
}

$id_modelo = (int)$_POST['id_modelo'];
$precio = floatval($_POST['precio']);

$sql = "UPDATE modelos SET precio = $precio WHERE id = $id_modelo";

if ($conexion->query($sql) === TRUE) {
    echo json_encode(["success" => "Precio actualizado correctamente"]);
} else {
    echo json_encode(["error" => "Error al actualizar: " . $conexion->error]);
}

$conexion->close();

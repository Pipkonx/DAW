<?php
header('Content-Type: application/json; charset=utf-8');
include 'conexion.php';

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conexion->connect_error]));
}

if (!isset($_GET['id_marca'])) {
    echo json_encode(["error" => "Parámetro 'id_marca' no proporcionado."]);
    $conexion->close();
    exit();
}

$id_marca = (int)$_GET['id_marca'];

$sql = "SELECT id, nombre, precio FROM modelos WHERE id_marca = $id_marca";
$resultado = $conexion->query($sql);

$modelos = [];
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $modelos[] = $row;
    }
}

$conexion->close();
echo json_encode($modelos);

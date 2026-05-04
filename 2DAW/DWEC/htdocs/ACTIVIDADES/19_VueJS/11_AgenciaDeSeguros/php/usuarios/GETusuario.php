<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$id = $_GET['id'] ?? null;
$datos = array();

if ($id) {
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($fila = $resultado->fetch_assoc()) {
            $datos = $fila;
        }
        $stmt->close();
    }
} else {
    $sql = "SELECT * FROM usuarios";
    $resultado = $conn->query($sql);
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
    }
}

echo json_encode($datos);
$conn->close();
?>

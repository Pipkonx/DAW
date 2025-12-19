<?php
include 'conexion.php';

header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = $_POST['marca'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $precio_final = $_POST['precio_final'] ?? 0;

    if (!empty($marca) && !empty($modelo) && $precio_final > 0) {
        $stmt = $conexion->prepare("INSERT INTO compras (marca, modelo, precio_final) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $marca, $modelo, $precio_final);

        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            error_log("Error al insertar compra: " . $stmt->error);
        }

        $stmt->close();
    } else {
        error_log("Datos de compra incompletos o inválidos.");
    }
}

$conexion->close();
echo json_encode($response);
?>
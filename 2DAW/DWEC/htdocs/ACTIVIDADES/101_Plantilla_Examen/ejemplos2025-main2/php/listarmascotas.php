<?php
header('Content-Type: application/json');

include 'conexion.php';

$id_cliente = $_GET['id'] ?? null;

if ($id_cliente === null) {
    echo json_encode(['error' => 'ID de cliente no proporcionado']);
    exit();
}

$sql = "SELECT * FROM mascotas WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

$mascotas = [];
while ($row = $result->fetch_assoc()) {
    $mascotas[] = $row;
}

echo json_encode($mascotas);

$stmt->close();
$conn->close();
?>
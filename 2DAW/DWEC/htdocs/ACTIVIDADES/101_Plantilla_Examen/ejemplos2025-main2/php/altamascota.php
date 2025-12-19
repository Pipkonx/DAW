<?php
header('Content-Type: application/json');

include 'conexion.php';

$nombre = $_GET['nombre'] ?? '';
$especie = $_GET['especie'] ?? '';
$raza = $_GET['raza'] ?? '';
$fech-nacimiento = $_GET['fech-nacimiento'] ?? '';
$id_cliente = $_GET['id_cliente'] ?? null;

if (empty($nombre) || empty($especie) || empty($raza) || empty($fech-nacimiento) || $id_cliente === null) {
    echo json_encode(['message' => 'Faltan datos para dar de alta la mascota.']);
    exit();
}

// Consulta preparada para evitar inyección SQL
$sql = "INSERT INTO mascotas (nombre, especie, raza, fech-nacimiento, id_cliente) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// "sssi" indica que esperamos 3 strings y 1 integer
$stmt->bind_param("ssssi", $nombre, $especie, $raza, $fech-nacimiento, $id_cliente);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Mascota dada de alta correctamente.']);
} else {
    echo json_encode(['message' => 'Error al dar de alta la mascota: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
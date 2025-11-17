<?php
header('Content-Type: application/json; charset=utf-8');
include 'conexion.php';

// Recoger datos de GET o POST
$nombre = $_REQUEST['nombre'] ?? null;
$apellido = $_REQUEST['apellido'] ?? null;
$nota = $_REQUEST['nota'] ?? null;

// Validar que vengan los datos
if (!$nombre || !$apellido || !$nota) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos (nombre, apellido o nota)']);
    exit;
}

try {
    // Consulta preparada
    $sql = "INSERT INTO alumnos (nombre, apellido, nota) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nombre, $apellido, $nota]);

    // No es necesario cerrar $stmt ni $conn en PDO, se liberan automáticamente
    echo json_encode([
        'exito' => true,
        'mensaje' => "Alumno: {$nombre} {$apellido} con nota: {$nota} dado de alta correctamente"
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}

// Cerrar conexión PDO (opcional)
$conn = null;

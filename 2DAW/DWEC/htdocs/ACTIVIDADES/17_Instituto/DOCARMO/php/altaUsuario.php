<?php
header('Content-Type: application/json; charset=utf-8');
include 'conexion.php';

if ($_POST) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $nota = $_POST['nota'];

    try {
        $sql = "INSERT INTO alumnos (nombre, apellido, nota) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);


        $stmt->execute([$nombre, $apellido, $nota]);

        $stmt->close();
        $conn->close();

        echo json_encode(['exito' => true, 'mensaje' => 'Alumno dado de alta correctamente']);
    } catch (Exception $e) {
        error_log($e->getMessage());

        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

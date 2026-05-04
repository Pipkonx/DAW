<?php
session_start();
header('Content-Type: application/json');

include '../config/db_connection.php';

$response = ['status' => 'error', 'message' => ''];

if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 0) {
    $response['message'] = 'Acceso denegado. Solo los administradores pueden añadir logins.';
    echo json_encode($response);
    exit();
}

$data = $_POST ?: json_decode(file_get_contents('php://input'), true);

if ($data) {
    $nombre = $data['nombre'] ?? null;
    $password = $data['password'] ?? null;
    $tipo = $data['tipo'] ?? null;

    if ($nombre && $password !== null && $tipo !== null) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM logins WHERE nombre = :nombre");
        $stmt->execute([':nombre' => $nombre]);
        
        if ($stmt->fetchColumn() > 0) {
            $response['message'] = 'El nombre de usuario ya existe.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO logins (nombre, password, tipo) VALUES (:nombre, :password, :tipo)");
            $stmt->execute([
                ':nombre' => $nombre,
                ':password' => $password,
                ':tipo' => $tipo
            ]);

            $response['status'] = 'success';
            $response['message'] = 'Usuario registrado correctamente.';
        }
    } else {
        $response['message'] = 'Datos incompletos.';
    }
} else {
    $response['message'] = 'No se han recibido datos.';
}

echo json_encode($response);
?>

<?php
session_start();
header('Content-Type: application/json');

include '../config/db_connection.php';

$response = ['status' => 'error', 'message' => ''];

if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 0) {
    $response['message'] = 'Acceso denegado. Solo los administradores pueden modificar logins.';
    echo json_encode($response);
    exit();
}

$data = $_POST ?: json_decode(file_get_contents('php://input'), true);

if ($data) {
    $id = $data['id'] ?? null;
    $nombre = $data['nombre'] ?? null;
    $tipo = $data['tipo'] ?? null;
    $password = $data['password'] ?? null;

    if ($id && $nombre && $tipo !== null) {
        $sql = "UPDATE logins SET nombre = :nombre, tipo = :tipo";
        $params = [
            ':nombre' => $nombre,
            ':tipo' => $tipo,
            ':id' => $id
        ];

        if ($password) {
            $sql .= ", password = :password";
            $params[':password'] = $password;
        }

        $sql .= " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $response['status'] = 'success';
        $response['message'] = 'Login modificado correctamente.';
    } else {
        $response['message'] = 'Datos incompletos.';
    }
} else {
    $response['message'] = 'No se han recibido datos.';
}

echo json_encode($response);
?>

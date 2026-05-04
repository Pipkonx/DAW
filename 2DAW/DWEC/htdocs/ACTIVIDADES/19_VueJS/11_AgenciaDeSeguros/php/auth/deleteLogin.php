<?php
session_start();
header('Content-Type: application/json');

include '../config/db_connection.php';

$response = ['status' => 'error', 'message' => ''];

if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 0) {
    $response['message'] = 'Acceso denegado. Solo los administradores pueden eliminar logins.';
    echo json_encode($response);
    exit();
}

$id = $_POST['id'] ?? $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM logins WHERE id = :id");
$stmt->execute([':id' => $id]);

if ($stmt->rowCount()) {
    $response['status'] = 'success';
    $response['message'] = 'Login eliminado correctamente.';
} else {
    $response['message'] = 'No se encontró el login para eliminar.';
}
} else {
    $response['message'] = 'ID de login no proporcionado.';
}

echo json_encode($response);
?>

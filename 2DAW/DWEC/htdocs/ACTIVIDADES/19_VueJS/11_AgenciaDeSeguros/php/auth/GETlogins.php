<?php
session_start();
header('Content-Type: application/json');

include '../config/db_connection.php';

$response = ['status' => 'error', 'message' => ''];

if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 0) {
    $response['message'] = 'Acceso denegado. Solo los administradores pueden ver los logins.';
    echo json_encode($response);
    exit();
}

$stmt = $pdo->query("SELECT id, nombre, tipo FROM logins");
$logins = $stmt->fetchAll();
echo json_encode($logins);
?>

<?php
session_start();
header('Content-Type: application/json');

include '../config/db_connection.php';

$response = ['ok' => false, 'error' => ''];

if (isset($_GET['inputUsuario']) && isset($_GET['inputPassword'])) {
    $inputUsuario = $_GET['inputUsuario'];
    $inputPassword = $_GET['inputPassword'];

    $stmt = $pdo->prepare("SELECT id, nombre, password, tipo FROM logins WHERE nombre = :nombre");
$stmt->execute([':nombre' => $inputUsuario]);
$user = $stmt->fetch();

if ($user && $inputPassword === $user['password']) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nombreLogin'] = $user['nombre'];
    $_SESSION['tipoUsuario'] = $user['tipo']; // 0 for admin, 1 for normal user

    $response['ok'] = true;
    $response['nombre'] = $user['nombre'];
    $response['tipo'] = $user['tipo'];
} else {
    $response['error'] = 'Usuario o contraseña incorrectos.';
}
} else {
    $response['error'] = 'Faltan parámetros de usuario o contraseña.';
}

echo json_encode($response);
?>

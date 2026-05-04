<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include '../config/db_connection.php';

$response = ['status' => 'error', 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Acceso denegado. Debe iniciar sesión para ver las pólizas.';
    echo json_encode($response);
    exit();
}

$datos = array();
if (isset($_GET['idUsuario'])) {
    $idUsuario = intval($_GET['idUsuario']);
    // COALESCE(valor, 0) devuelve el primer valor que no sea NULL. 
    // Si SUM(importe) es NULL (porque no hay pagos), devuelve 0.
    $sql = "SELECT p.*, COALESCE((SELECT SUM(importe) FROM pagos WHERE idPoliza = p.idPoliza), 0) as total_pagado FROM polizas p WHERE p.idUsuario = :idUsuario";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':idUsuario' => $idUsuario]);
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Si no hay pagos, SUM daría NULL. COALESCE lo convierte en 0.
    $sql = "SELECT p.*, COALESCE((SELECT SUM(importe) FROM pagos WHERE idPoliza = p.idPoliza), 0) as total_pagado FROM polizas p";
    $stmt = $pdo->query($sql);
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($datos);
?>

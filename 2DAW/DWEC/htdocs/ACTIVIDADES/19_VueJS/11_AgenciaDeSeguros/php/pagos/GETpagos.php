<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once '../config/db_connection.php';

$idPoliza = $_GET['idPoliza'] ?? null;

if (!$idPoliza) {
    echo json_encode(['status' => 'error', 'message' => 'ID de póliza no proporcionado.']);
    exit();
}

$sql = "SELECT * FROM pagos WHERE idPoliza = :idPoliza ORDER BY fecha DESC, idPago DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':idPoliza' => $idPoliza]);
$pagos = $stmt->fetchAll();

echo json_encode($pagos);
?>

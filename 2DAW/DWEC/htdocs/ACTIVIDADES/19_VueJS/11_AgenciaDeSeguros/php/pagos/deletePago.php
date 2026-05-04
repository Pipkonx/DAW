<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once '../config/db_connection.php';

$idPago = $_POST['idPago'] ?? $_GET['idPago'] ?? null;

if ($idPago) {
    $idPago = intval($idPago);
$sql = "DELETE FROM pagos WHERE idPago = :idPago";
$stmt = $pdo->prepare($sql);
if ($stmt->execute([':idPago' => $idPago])) {
    echo json_encode(['status' => 'success', 'message' => 'Pago eliminado correctamente']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar el pago.']);
}
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID de pago no proporcionado']);
}
?>

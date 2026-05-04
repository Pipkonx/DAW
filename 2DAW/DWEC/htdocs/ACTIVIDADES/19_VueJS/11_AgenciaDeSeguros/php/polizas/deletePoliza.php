<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include '../config/db_connection.php';

$response = ['status' => 'error', 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Acceso denegado. Debe iniciar sesión para eliminar pólizas.';
    echo json_encode($response);
    exit();
}

$idPoliza = $_GET['idPoliza'] ?? $_POST['idPoliza'] ?? null;

if ($idPoliza) {
    $idPoliza = intval($idPoliza);
    $pdo->beginTransaction();
$sqlPagos = "DELETE FROM pagos WHERE numero_poliza = :idPoliza";
$stmtPagos = $pdo->prepare($sqlPagos);
$stmtPagos->execute([':idPoliza' => $idPoliza]);

$sqlPoliza = "DELETE FROM polizas WHERE idPoliza = :idPoliza";
$stmtPoliza = $pdo->prepare($sqlPoliza);
$stmtPoliza->execute([':idPoliza' => $idPoliza]);
    
if ($stmtPoliza->rowCount() > 0) {
    $pdo->commit();
    $response = ["status" => "success", "message" => "Póliza y sus pagos borrados correctamente."];
} else {
    $pdo->rollBack();
    $response = ["status" => "info", "message" => "No se encontró la póliza o ya fue borrada."];
}
} else {
    $response = ["status" => "error", "message" => "ID de póliza no proporcionado."];
}

echo json_encode($response);
?>

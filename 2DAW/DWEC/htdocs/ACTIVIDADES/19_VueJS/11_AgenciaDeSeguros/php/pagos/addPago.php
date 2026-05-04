<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include '../config/db_connection.php';

$response = ['status' => 'error', 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Acceso denegado. Debe iniciar sesión para añadir pagos.';
    echo json_encode($response);
    exit();
}

$data = $_POST ?: json_decode(file_get_contents('php://input'), true);

if ($data) {
    $idPoliza = $data['idPoliza'] ?? $data['numero_poliza'] ?? null;
    $importePago = $data['importe'] ?? null;
    $fecha = $data['fecha'] ?? null;

    if ($idPoliza && $importePago !== null && $fecha) {
        $idPoliza = intval($idPoliza);
        $importePago = floatval($importePago);

        $sqlPoliza = "SELECT importe FROM polizas WHERE idPoliza = :idPoliza";
        $stmtPoliza = $pdo->prepare($sqlPoliza);
        $stmtPoliza->execute([':idPoliza' => $idPoliza]);
        $rowPoliza = $stmtPoliza->fetch(PDO::FETCH_ASSOC);
        if (!$rowPoliza) {
            $response['message'] = 'No se encontró la póliza indicada.';
            echo json_encode($response);
            exit();
        }
        $importePoliza = floatval($rowPoliza['importe']);

        $sqlPagos = "SELECT SUM(importe) as total FROM pagos WHERE idPoliza = :idPoliza";
        $stmtPagos = $pdo->prepare($sqlPagos);
        $stmtPagos->execute([':idPoliza' => $idPoliza]);
        $rowPagos = $stmtPagos->fetch(PDO::FETCH_ASSOC);
        $total_pagado = floatval($rowPagos['total'] ?? 0);

        if (($total_pagado + $importePago) > ($importePoliza + 0.01)) {
            $restante = $importePoliza - $total_pagado;
            $response['message'] = "El pago excede el importe total de la póliza. Restante: " . number_format($restante, 2, '.', '') . " €";
        } else {
            $sqlInsert = "INSERT INTO pagos (idPoliza, fecha, importe) VALUES (:idPoliza, :fecha, :importe)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            if ($stmtInsert->execute([':idPoliza' => $idPoliza, ':fecha' => $fecha, ':importe' => $importePago])) {
                $response['status'] = 'success';
                $response['message'] = 'Pago añadido correctamente.';
                
                if (abs($importePoliza - ($total_pagado + $importePago)) < 0.01) {
                    $sqlUpdate = "UPDATE polizas SET estado = '0' WHERE idPoliza = :idPoliza";
                    $stmtUpdate = $pdo->prepare($sqlUpdate);
                    $stmtUpdate->execute([':idPoliza' => $idPoliza]);
                }
            }
        }
    } else {
        $response['message'] = 'Faltan datos requeridos.';
    }
} else {
    $response['message'] = 'No se han recibido datos.';
}
echo json_encode($response);
?>

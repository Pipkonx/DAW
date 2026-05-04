<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include '../config/db_connection.php';

$response = ['status' => 'error', 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Acceso denegado. Debe iniciar sesión para modificar pólizas.';
    echo json_encode($response);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['idPoliza'])) {
    $idPoliza = intval($data['idPoliza']);
    
        // COALESCE asegura que si no hay pagos (SUM es NULL), recibamos un 0.
        $sql_check = "SELECT p.importe, p.estado, COALESCE((SELECT SUM(importe) FROM pagos WHERE numero_poliza = p.idPoliza), 0) as total_pagado FROM polizas p WHERE p.idPoliza = :idPoliza";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([':idPoliza' => $idPoliza]);
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $estadoActual = strval($row['estado']);
            $importeActual = $row['importe'];
            $totalPagado = floatval($row['total_pagado']);
            
            if (isset($data['importe'])) {
                $nuevoImporte = floatval($data['importe']);
                if ($nuevoImporte < $totalPagado) {
                    $response['status'] = 'error';
                    $response['message'] = "El importe de la póliza ($nuevoImporte €) no puede ser menor al total ya pagado ($totalPagado €).";
                    echo json_encode($response);
                    exit;
                }
                if ($estadoActual === '0' && $nuevoImporte > $totalPagado) {
                    $data['estado'] = '1'; 
                }
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Póliza no encontrada.';
            echo json_encode($response);
            exit;
        }

    $allowed_fields = ['importe', 'fecha', 'estado', 'observaciones'];
    $update_parts = [];
    $params = [];

    foreach ($allowed_fields as $key) {
        if (isset($data[$key])) {
            $update_parts[] = "$key = :$key";
            $params[":$key"] = $data[$key];
        }
    }

    if (empty($update_parts)) {
        $response['status'] = 'info';
        $response['message'] = 'No se proporcionaron campos para actualizar.';
    } else {
        $sql = "UPDATE polizas SET " . implode(", ", $update_parts) . " WHERE idPoliza = :idPoliza";
        $params[':idPoliza'] = $idPoliza;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $response['status'] = 'success';
        $response['message'] = 'Póliza actualizada correctamente.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'No se recibieron los datos o falta el ID de la póliza.';
}

echo json_encode($response);
?>

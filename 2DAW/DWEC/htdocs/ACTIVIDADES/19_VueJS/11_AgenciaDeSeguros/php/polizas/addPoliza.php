<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$response = array();

if ($data) {
    $importe = isset($data['importe']) ? floatval($data['importe']) : 0.0;
    $fecha = $data['fecha'] ?? '';
    $estado = $data['estado'] ?? '';
    $observaciones = $data['observaciones'] ?? '';
    $idusuario = isset($data['idUsuario']) ? intval($data['idUsuario']) : 0;

    $sql = "INSERT INTO polizas (importe, fecha, estado, observaciones, idUsuario) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("dsssi", $importe, $fecha, $estado, $observaciones, $idusuario);
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Póliza añadida correctamente.';
            $response['idPoliza'] = $stmt->insert_id;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error al ejecutar la consulta: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error al preparar la consulta: ' . $conn->error;
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'No se recibieron datos.';
}
$conn->close();
echo json_encode($response);
?>

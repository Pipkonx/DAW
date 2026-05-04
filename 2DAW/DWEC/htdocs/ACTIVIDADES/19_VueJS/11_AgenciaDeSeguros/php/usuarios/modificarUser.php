<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$response = array();

if ($data && isset($data['id'])) {
    $id = $data['id'];
    $allowed_fields = [
        'nombre' => 's', 'apellidos' => 's', 'tlf' => 'i', 'localidad' => 's',
        'cp' => 'i', 'provincia' => 's', 'tipo' => 'i', 'login' => 's', 'contrasena' => 's' 
    ];

    $update_parts = [];
    $types = "";
    $params = [];

    foreach ($allowed_fields as $key => $type) {
        if (isset($data[$key])) {
            $column_name = ($key === 'contrasena') ? 'password' : $key;
            $value = $data[$key];
            if ($key === 'tipo') $value = $value ? 1 : 0;
            $update_parts[] = "$column_name = ?";
            $types .= $type;
            $params[] = $value;
        }
    }

    if (empty($update_parts)) {
        $response['status'] = 'info';
        $response['message'] = 'No se proporcionaron campos para actualizar.';
    } else {
        $sql = "UPDATE usuarios SET " . implode(", ", $update_parts) . " WHERE id = ?";
        $types .= "i"; 
        $params[] = $id;

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param($types, ...$params);
            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Usuario actualizado correctamente.';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Error al ejecutar la actualización: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error al preparar la consulta: ' . $conn->error;
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'No se recibieron los datos o falta el ID del usuario.';
}
$conn->close();
echo json_encode($response);
?>

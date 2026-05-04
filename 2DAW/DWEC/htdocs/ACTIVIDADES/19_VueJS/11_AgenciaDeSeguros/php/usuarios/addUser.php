<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$response = array();

if ($data) {
    $nombre = $data['nombre'] ?? '';
    $apellidos = $data['apellidos'] ?? '';
    $tlf = $data['tlf'] ?? 0;
    $localidad = $data['localidad'] ?? '';
    $cp = $data['cp'] ?? 0;
    $provincia = $data['provincia'] ?? '';
    $tipo = (isset($data['tipo']) && $data['tipo']) ? 1 : 0;
    $password = $data['contrasena'] ?? '';
    $login = $data['login'] ?? '';

    $sql = "INSERT INTO usuarios (nombre, apellidos, tlf, localidad, cp, provincia, tipo, password, login) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssisisiss", $nombre, $apellidos, $tlf, $localidad, $cp, $provincia, $tipo, $password, $login);
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Usuario añadido correctamente.';
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

<?php
include 'conexion.php';

header('Content-Type: application/json');

$response = ['success' => false, 'modelos' => []];

if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    $stmt = $conexion->prepare("SELECT m.id, m.nombre AS modelo_nombre, m.precio, ma.nombre AS marc_nombre FROM modelos m JOIN marcas ma ON m.id_marca = ma.id WHERE m.nombre LIKE ?");
    $stmt->bind_param("s", $query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $response['success'] = true;
        while ($row = $result->fetch_assoc()) {
            $response['modelos'][] = $row;
        }
    }
    $stmt->close();
}

$conexion->close();
echo json_encode($response);
?>
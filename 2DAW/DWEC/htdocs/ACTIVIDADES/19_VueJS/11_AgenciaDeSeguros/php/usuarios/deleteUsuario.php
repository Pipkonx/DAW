<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/conexion.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if ($id) {
    $sql = "DELETE FROM usuarios WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Usuario borrado correctamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID no proporcionado."]);
}
$conn->close();
?>

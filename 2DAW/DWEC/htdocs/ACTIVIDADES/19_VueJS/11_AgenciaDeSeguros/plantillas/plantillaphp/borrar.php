<?php
header("Content-Type: application/json");
include '../db_connection.php';

// Leer JSON desde Axios
$data = json_decode(file_get_contents("php://input"));

// Solo necesitamos el ID para borrar
if (!empty($data->id)) {
    
    // PRECAUCIÓN EXAMEN: Cambia 'usuarios' por el nombre de tu tabla
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    // "i" indica que el comodín '?' es de tipo Integer (entero)
    $stmt->bind_param("i", $data->id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "mensaje" => "Registro eliminado"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Error al eliminar"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "mensaje" => "ID no proporcionado"]);
}
$conn->close();
?>

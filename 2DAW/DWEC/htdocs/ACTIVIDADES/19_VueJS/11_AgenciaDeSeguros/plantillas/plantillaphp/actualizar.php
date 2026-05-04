<?php
header("Content-Type: application/json");
include '../db_connection.php';

// Leer los datos JSON que manda Axios desde Vue
$data = json_decode(file_get_contents("php://input"));

// Para actualizar necesitamos forzosamente el ID, además de los datos modificados
if (!empty($data->id) && !empty($data->nombre) && !empty($data->email)) {
    
    // PRECAUCIÓN EXAMEN: Cambia el nombre de la tabla y las columnas a actualizar
    $sql = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    // "ssi" significa: String(nombre), String(email), Integer(id)
    $stmt->bind_param("ssi", $data->nombre, $data->email, $data->id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "mensaje" => "Registro actualizado"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Error al actualizar"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "mensaje" => "Faltan datos obligatorios"]);
}
$conn->close();
?>

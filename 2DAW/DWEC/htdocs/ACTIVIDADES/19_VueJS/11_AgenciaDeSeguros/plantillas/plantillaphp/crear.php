<?php
// Indicamos que devolvemos un JSON
header("Content-Type: application/json");
include '../db_connection.php';

// Cuando usamos Axios, los datos POST llegan en crudo (raw JSON), no en $_POST.
// file_get_contents("php://input") lee ese JSON y json_decode lo convierte a objeto PHP.
$data = json_decode(file_get_contents("php://input"));

// Comprobamos que no lleguen datos vacíos (los obligatorios)
if (!empty($data->nombre) && !empty($data->email)) {
    
    // PRECAUCIÓN EXAMEN: Cambia 'usuarios' por tu tabla, y 'nombre', 'email' por tus columnas
    // Usamos '?' como comodines de seguridad para evitar Inyecciones SQL (Consultas Preparadas)
    $sql = "INSERT INTO usuarios (nombre, email) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Asignamos las variables a los comodines '?'.
    // La "ss" significa: El primer comodín es String(s), el segundo es String(s). 
    // Si tuvieras un entero sería "i" (ej: "ssi").
    $stmt->bind_param("ss", $data->nombre, $data->email);
    
    // Ejecutamos la consulta preparada
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "mensaje" => "Registro creado correctamente"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Error al ejecutar la consulta"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "mensaje" => "Faltan datos obligatorios"]);
}
$conn->close();
?>

<?php
// Indicamos al navegador que la respuesta será un JSON (esencial para Vue y Axios)
header("Content-Type: application/json");

// Incluimos nuestro archivo de conexión
include '../db_connection.php';

// PRECAUCIÓN EXAMEN: Asegúrate de poner el nombre de tu tabla real aquí (ej: 'alumnos', 'clientes')
$sql = "SELECT * FROM usuarios ORDER BY id DESC"; 
$result = $conn->query($sql);

$datos = []; // Array vacío donde guardaremos las filas

// Si la consulta fue bien y devolvió filas (mayor a 0)
if ($result && $result->num_rows > 0) {
    // Extraemos cada fila como un array asociativo (clave-valor)
    while($row = $result->fetch_assoc()) {
        $datos[] = $row; // Añadimos la fila al array principal
    }
}

// Convertimos el array de PHP a formato JSON para que JavaScript (Axios) lo entienda
echo json_encode($datos);

// Cerramos la conexión para liberar recursos
$conn->close();
?>

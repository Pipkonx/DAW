<?php
// Encabezado para indicar que la respuesta es JSON
header('Content-Type: application/json; charset=utf-8');

// Incluir el archivo de conexión
include 'conexion.php'; // o require 'conexion.php';

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}
$id = $_GET["id"];

// Consulta SQL
$sql = "SELECT * FROM clientes where id=".$id;
$resultado = $conexion->query($sql);

// Array para guardar los resultados
$alumnos = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $alumnos[] = $fila;
    }
    // Devolver los datos como JSON
    echo json_encode($alumnos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} else {
    echo json_encode(["mensaje" => "No hay registros en la tabla alumnos."]);
}

// Cerrar conexión
$conexion->close();
?>

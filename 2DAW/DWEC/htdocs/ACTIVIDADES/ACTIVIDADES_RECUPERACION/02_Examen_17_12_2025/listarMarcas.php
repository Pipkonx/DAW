<?php
// Encabezado para indicar que la respuesta es JSON
header('Content-Type: application/json; charset=utf-8');

// Incluir el archivo de conexión
include 'conexion.php'; // o require 'conexion.php';

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

// Consulta SQL
$sql = "SELECT id, nombre FROM marcas";
$resultado = $conexion->query($sql);

// Array para guardar los resultados
$marcas = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $marcas[] = $fila;
    }
    // Devolver los datos como JSON
    echo json_encode($marcas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    echo json_encode(["mensaje" => "No hay registros en la tabla marcas"]);
}

// Cerrar conexión
$conexion->close();

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Encabezado para indicar que la respuesta es JSON
header('Content-Type: application/json; charset=utf-8');

// Incluir el archivo de conexión
include 'conexion.php'; // o require 'conexion.php';

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

// Consulta SQL
$sql = "SELECT * FROM clientes";
$resultado = $conn->query($sql);

// Array para guardar los resultados
$clientes = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $clientes[] = $fila;
    }
    // Devolver los datos como JSON
    echo json_encode($clientes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} else {
    echo json_encode(["mensaje" => "No hay registros en la tabla clientes."]);
}

// Cerrar conexión
$conn->close();

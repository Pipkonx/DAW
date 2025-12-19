<?php

// Encabezado para indicar que la respuesta es JSON
header('Content-Type: application/json; charset=utf-8');

// Incluir el archivo de conexión
include 'conexion.php'; // o require 'conexion.php';

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

$nombre    = $_GET['nombre'];
$apellidos = $_GET['apellidos'];
$nota      = $_GET['nota'];

// Consulta (NO segura si los datos vienen del usuario)
$sql = "INSERT INTO alumnos (nombre, apellidos, nota)
        VALUES ('$nombre', '$apellidos', '$nota')";

// Ejecutar
if (mysqli_query($conexion, $sql)) {
    echo json_encode([
        "status" => "success",
        "message" => "Alumno insertado correctamente.",
        "data" => [
            "nombre" => $nombre,
            "apellidos" => $apellidos,
            "nota" => $nota
        ]
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => mysqli_error($conexion)
    ]);
}
?>
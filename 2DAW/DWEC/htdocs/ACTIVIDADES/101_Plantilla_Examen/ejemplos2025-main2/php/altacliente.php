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
$telefono  = $_GET['telefono'];
$email     = $_GET['email'];
$direccion = $_GET['direccion'];

// Consulta (NO segura si los datos vienen del usuario)
$sql = "INSERT INTO clientes (nombre, apellidos, telefono, email, direccion, fecha_alta)
        VALUES ('$nombre', '$apellidos', '$telefono', '$email', '$direccion', NOW())";

// Ejecutar
if (mysqli_query($conexion, $sql)) {
    echo json_encode([
        "status" => "success",
        "message" => "Cliente insertado correctamente.",
        "data" => [
            "nombre" => $nombre,
            "apellidos" => $apellidos,
            "telefono" => $telefono,
            "email" => $email,
            "direccion" => $direccion
        ]
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => mysqli_error($conexion)
    ]);
}
?>
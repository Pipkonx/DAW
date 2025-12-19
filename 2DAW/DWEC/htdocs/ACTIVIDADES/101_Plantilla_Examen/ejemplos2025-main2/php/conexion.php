<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = "localhost";
$usuario = "root";
$contraseña = "";
$base_datos = "veterinario";

// Crear conexión
$conn = new mysqli($host, $usuario, $contraseña, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

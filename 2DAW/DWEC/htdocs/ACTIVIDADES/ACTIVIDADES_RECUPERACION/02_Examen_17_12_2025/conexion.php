<?php
$host = "localhost";
$usuario = "root";
$contraseña = "";
$base_datos = "recuperacion";

// Crear conexión
$conexion = new mysqli($host, $usuario, $contraseña, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
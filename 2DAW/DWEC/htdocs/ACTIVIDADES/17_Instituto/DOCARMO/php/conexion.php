<?php
// Datos de conexión
$host = "localhost";
$usuario = "root";
$contraseña = "";
$base_datos = "instituto";

//TODO mysqli
// $conn = new mysqli($host, $usuario, $contraseña, $base_datos);
// if ($conn->connect_error) {
//     die("Error de conexión MySQLi: " . $conn->connect_error);
// }

//TODO PDO
try {
    $dsn = "mysql:host=$host;dbname=$base_datos;charset=utf8mb4";
    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $conn = new PDO($dsn, $usuario, $contraseña, $opciones);
} catch (PDOException $e) {
    error_log("PDO connection error: " . $e->getMessage());
    die("Connection failed. Please try again later.");
}

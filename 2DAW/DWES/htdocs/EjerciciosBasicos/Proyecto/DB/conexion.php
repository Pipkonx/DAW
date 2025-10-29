<?php
$user = "root";
$pass = "";
$host = "localhost";
$dbname = "proyecto";

try {
    //todo el proyecto debe estar en PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    //set attribute para mostrar la excepcion
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Conexión fallida: " . $e->getMessage());
}

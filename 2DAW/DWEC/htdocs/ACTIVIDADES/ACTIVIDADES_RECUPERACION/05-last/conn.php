<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 

$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "tienda_ejemplo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

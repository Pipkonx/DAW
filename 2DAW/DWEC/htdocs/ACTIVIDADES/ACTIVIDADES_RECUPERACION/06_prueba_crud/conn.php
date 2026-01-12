<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 

$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "crud_fetch";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

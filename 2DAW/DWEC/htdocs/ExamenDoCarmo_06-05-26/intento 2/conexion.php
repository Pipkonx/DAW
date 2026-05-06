<?php
$host = "localhost"; 
$db = "rafaelcordero"; 
$user = 'root';
$pass = '';
//$user = "rafaelcordero";      
//$pass = "vl985pK!4";
//$pass = "@GAEj!2noi4p2say";          

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) { 
    die(json_encode(['error' => $e->getMessage()])); 
}
?>

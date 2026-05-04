<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'aeternaseguros'); 
define('DB_USER', 'root'); 
define('DB_PASS', ''); 

// Establish database connection
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
?>

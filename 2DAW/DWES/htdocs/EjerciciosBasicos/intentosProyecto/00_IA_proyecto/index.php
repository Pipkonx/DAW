<?php
$controller = $_GET['controller'] ?? 'Auth';
$action = $_GET['action'] ?? 'login';

$controllerFile = "controllers/" . ucfirst($controller) . "Controller.php";

if (!file_exists($controllerFile)) {
    die("Controlador no encontrado.");
}

require_once $controllerFile;

$controllerName = ucfirst($controller) . "Controller";
$obj = new $controllerName();

if (!method_exists($obj, $action)) {
    die("Acción no encontrada.");
}

$obj->$action();

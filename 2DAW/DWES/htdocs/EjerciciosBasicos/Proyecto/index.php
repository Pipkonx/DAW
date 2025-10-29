<?php
require "controllers/UsuarioController.php";

$controller = new UsuarioController();

$action = $_GET['action'] ?? 'listar';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'alta':
        $controller->alta();
        break;
    case 'modificar':
        $controller->modificar($id);
        break;
    case 'borrar':
        $controller->borrar($id);
        break;
    default:
        $controller->listar();
        break;
}

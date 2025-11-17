<?php

namespace App\Http\Controllers;

use Database\Seeders\Database;
use App\Models\Usuario;

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../Utils/DatabaseConecction.php';
require_once __DIR__ . '/../../models/Usuario.php';


function json_response($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if ($_POST) {
    json_response(['success' => false, 'error' => 'Método no permitido'], 405);
}

$action = $_POST['action'] ?? null;

$pdo = Database::getConnection();
$usuarioModel = new Usuario($pdo);

try {
    switch ($action) {
        case 'register':
            $nombre = trim((string)($_POST['nombre'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            if (!$nombre || !$email || !$password) {
                json_response(['success' => false, 'error' => 'Campos incompletos'], 400);
            }
            $res = $usuarioModel->registrarUsuario($nombre, $email, $password);
            if (!$res['success']) json_response($res, 400);
            $_SESSION['user_id'] = $res['id'];
            json_response(['success' => true, 'message' => 'Usuario registrado', 'user_id' => $res['id']]);
            break;

        case 'login':
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            if (!$email || !$password) {
                json_response(['success' => false, 'error' => 'Campos incompletos'], 400);
            }
            $res = $usuarioModel->iniciarSesion($email, $password);
            if (!$res['success']) json_response($res, 401);
            $_SESSION['user_id'] = $res['user']['id'];
            json_response(['success' => true, 'message' => 'Inicio de sesión correcto', 'user' => $res['user']]);
            break;

        case 'logout':
            session_destroy();
            json_response(['success' => true, 'message' => 'Sesión cerrada']);
            break;

        default:
            json_response(['success' => false, 'error' => 'Acción no válida'], 400);
    }
} catch (Exception $e) {
    json_response(['success' => false, 'error' => 'Error interno', 'detail' => $e->getMessage()], 500);
}
<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Finanzas.php';

ensure_session();

function json_response($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    json_response(['success' => false, 'error' => 'No autorizado'], 401);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
    json_response(['success' => false, 'error' => 'Método no permitido'], 405);
}

$action = $_POST['action'] ?? null;
$csrf = $_POST['csrf_token'] ?? null;
if (!verify_csrf_token($csrf)) {
    json_response(['success' => false, 'error' => 'CSRF token inválido'], 400);
}

$pdo = Database::getConnection();
$finModel = new Finanzas($pdo);
$userId = (int)$_SESSION['user_id'];

try {
    switch ($action) {
        case 'add':
            $tipo = trim((string)($_POST['tipo'] ?? ''));
            $monto = (float)($_POST['monto'] ?? 0);
            $descripcion = trim((string)($_POST['descripcion'] ?? ''));
            $fecha = (string)($_POST['fecha'] ?? date('Y-m-d'));
            if (!$tipo || $monto <= 0) {
                json_response(['success' => false, 'error' => 'Datos inválidos'], 400);
            }
            $res = $finModel->registrarMovimiento($userId, $tipo, $monto, $descripcion, $fecha);
            json_response($res);
            break;

        case 'list':
            $rows = $finModel->obtenerMovimientosPorUsuario($userId);
            json_response(['success' => true, 'items' => $rows]);
            break;

        case 'summary':
            $anio = (int)($_POST['anio'] ?? date('Y'));
            $sum = $finModel->obtenerResumenAnual($userId, $anio);
            json_response(['success' => true, 'summary' => $sum]);
            break;

        case 'monthly':
            $anio = (int)($_POST['anio'] ?? date('Y'));
            $mensual = $finModel->obtenerResumenMensual($userId, $anio);
            json_response(['success' => true, 'monthly' => $mensual]);
            break;

        default:
            json_response(['success' => false, 'error' => 'Acción no válida'], 400);
    }
} catch (Throwable $e) {
    json_response(['success' => false, 'error' => 'Error interno', 'detail' => $e->getMessage()], 500);
}
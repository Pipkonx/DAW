<?php
// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la base de datos
require_once 'config/config.php';

// Cargar controladores
require_once 'controllers/UsuarioController.php';
require_once 'controllers/TareaController.php';

// Obtener parámetros de la URL
$controller = $_GET['controller'] ?? 'usuario';
$action = $_GET['action'] ?? 'index';

// Rutas
try {
    switch ($controller) {
        case 'usuario':
            $usuarioController = new UsuarioController();
            switch ($action) {
                case 'index':
                    $usuarioController->index();
                    break;
                case 'crear':
                    $usuarioController->crear();
                    break;
                case 'editar':
                    $usuarioController->editar();
                    break;
                case 'eliminar':
                    $usuarioController->eliminar();
                    break;
                default:
                    header('HTTP/1.0 404 Not Found');
                    echo 'Página no encontrada';
                    break;
            }
            break;
            
        case 'tarea':
            $tareaController = new TareaController();
            switch ($action) {
                case 'index':
                    $tareaController->index();
                    break;
                case 'crear':
                    $tareaController->crear();
                    break;
                case 'editar':
                    $tareaController->editar();
                    break;
                case 'eliminar':
                    $tareaController->eliminar();
                    break;
                case 'marcarCompletada':
                    $tareaController->marcarCompletada();
                    break;
                case 'marcarPendiente':
                    $tareaController->marcarPendiente();
                    break;
                default:
                    header('HTTP/1.0 404 Not Found');
                    echo 'Página no encontrada';
                    break;
            }
            break;
            
        default:
            header('HTTP/1.0 404 Not Found');
            echo 'Página no encontrada';
            break;
    }
} catch (Exception $e) {
    header('HTTP/1.0 500 Internal Server Error');
    echo 'Error interno del servidor: ' . htmlspecialchars($e->getMessage());
}
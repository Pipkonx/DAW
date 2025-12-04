<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Http\Controllers\C_Administrador;
use App\Http\Controllers\C_Auth;
use App\Http\Controllers\C_Usuarios;
use App\Http\Controllers\C_Inicio;
use App\Http\Controllers\C_Operario;

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
//$basePath = str_replace(basename($scriptName), '', $scriptName);
$basePath = str_replace('/public/index.php', '', $scriptName);

// Eliminar el basePath del requestUri para obtener la ruta relativa
$route = str_replace($basePath, '', $requestUri);

// Limpiar la ruta de parámetros GET y barras finales
$route = strtok($route, '?');
$route = rtrim($route, '/');

// Definir rutas
$routes = [
    '' => [C_Inicio::class, 'inicio'],
    '/' => [C_Inicio::class, 'inicio'],
    '/login' => [C_Auth::class, 'login'],
    '/logout' => [C_Auth::class, 'logout'],

    // Rutas de administrador
    '/admin/tareas' => [C_Administrador::class, 'listar'],
    '/admin/tareas/crear' => [C_Administrador::class, 'crear'],
    '/admin/tareas/guardar' => [C_Administrador::class, 'guardar'],
    '/admin/tareas/editar' => [C_Administrador::class, 'editar'],
    '/admin/tareas/actualizar' => [C_Administrador::class, 'actualizar'],
    '/admin/tareas/confirmar-eliminacion' => [C_Administrador::class, 'confirmarEliminacion'],
    '/admin/tareas/eliminar' => [C_Administrador::class, 'eliminar'],
    '/admin/tareas/eliminar-fichero' => [C_Administrador::class, 'eliminarFichero'],

    '/admin/usuarios' => [C_Usuarios::class, 'listar'],
    '/admin/usuarios/crear' => [C_Usuarios::class, 'crear'],
    '/admin/usuarios/guardar' => [C_Usuarios::class, 'guardar'],
    '/admin/usuarios/editar' => [C_Usuarios::class, 'editar'],
    '/admin/usuarios/actualizar' => [C_Usuarios::class, 'actualizar'],
    '/admin/usuarios/confirmar-eliminacion' => [C_Usuarios::class, 'confirmarEliminacion'],
    '/admin/usuarios/eliminar' => [C_Usuarios::class, 'eliminar'],

    // Rutas de operario
    '/operario/tareas' => [C_Operario::class, 'listar'],
    '/operario/tareas/editar' => [C_Operario::class, 'editar'],
    '/operario/tareas/actualizar' => [C_Operario::class, 'actualizar'],
    '/operario/tareas/confirmar-finalizacion' => [C_Operario::class, 'confirmarFinalizacion'],
    '/operario/tareas/finalizar' => [C_Operario::class, 'finalizar'],
];

// Dispatcher
if (array_key_exists($route, $routes)) {
    $controllerName = $routes[$route][0];
    $methodName = $routes[$route][1];

    $controller = new $controllerName();
    $controller->$methodName();
} else {
    // Manejar 404
    http_response_code(404);
    echo "404 Not Found: " . htmlspecialchars($route);
}

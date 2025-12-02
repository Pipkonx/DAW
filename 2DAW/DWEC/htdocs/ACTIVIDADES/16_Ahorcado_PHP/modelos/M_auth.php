<?php
// Incluye los archivos necesarios para la conexión a la base de datos y utilidades.
require_once __DIR__ . '/../conexion/DB.php';
require_once __DIR__ . '/../contorlador/C_utils.php';

// Obtiene una instancia de la conexión a la base de datos.
$pdo = DB::getInstance()->getConnection();

function manejarInicioSesion(PDO $conexion, string $nombreUsuario, string $contrasena): array
{
    if ($nombreUsuario === '' || $contrasena === '') {
        return ['success' => false, 'message' => 'Credenciales inválidas.'];
    }

    $declaracion = $conexion->prepare('SELECT id_jugador, contrasena, login FROM JUGADORES WHERE login = ? LIMIT 1');
    $declaracion->execute([$nombreUsuario]);
    $usuario = $declaracion->fetch();

    if (!$usuario) {
        return ['success' => false, 'message' => 'Usuario no encontrado.'];
    }

    if ($contrasena !== $usuario['contrasena']) {
        return ['success' => false, 'message' => 'Contraseña incorrecta.'];
    }

    return ['success' => true, 'message' => 'Inicio de sesión exitoso.', 'login' => $usuario['login']];
}

function manejarRegistro(PDO $conexion, string $nombreUsuario, string $contrasena, string $confirmarContrasena): array
{
    if ($nombreUsuario === '' || $contrasena === '' || $confirmarContrasena === '') {
        return ['success' => false, 'message' => 'Completa todos los campos.'];
    }
    if ($contrasena !== $confirmarContrasena) {
        return ['success' => false, 'message' => 'Las contraseñas no coinciden.'];
    }

    $verificarExistencia = $conexion->prepare('SELECT COUNT(*) AS c FROM JUGADORES WHERE login = ?');
    $verificarExistencia->execute([$nombreUsuario]);
    $existeUsuario = (int)$verificarExistencia->fetchColumn() > 0;

    if ($existeUsuario) {
        return ['success' => false, 'message' => 'El usuario ya existe.'];
    }

    $insertarUsuario = $conexion->prepare('INSERT INTO JUGADORES (login, contrasena, es_administrador) VALUES (?, ?, 0)');
    if ($insertarUsuario->execute([$nombreUsuario, $contrasena])) {
        return ['success' => true, 'message' => 'Registro exitoso.'];
    } else {
        return ['success' => false, 'message' => 'Error al registrar el usuario.'];
    }
}

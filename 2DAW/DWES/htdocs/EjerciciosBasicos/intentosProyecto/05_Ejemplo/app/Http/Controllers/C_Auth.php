<?php

namespace App\Http\Controllers;

use App\Models\M_Usuarios;
use App\Models\M_Tareas;

/**
 * Controlador de autenticación simple con contraseña plana,
 * sesión y cookie para recordar la clave.
 */
class C_Auth extends C_Controller
{
    /**
     * Muestra el formulario de login y procesa el inicio de sesión.
     */
    public function login()
    {
        // POST: procesar login
        if ($_POST) {
            $nombre = $_POST['usuario'] ?? '';
            $contrasena = $_POST['clave'] ?? '';
            $guardar = !empty($_POST['guardar_clave']);

            $datos = ['nombre' => $nombre, 'contraseña' => $contrasena, 'guardar_clave' => $guardar ? 'on' : ''];

            if ($nombre === '' || $contrasena === '') {
                $datos['errorGeneral'] = 'Debe introducir nombre y contraseña';
                $datos['isLoginPage'] = true;
                return view('autenticacion/login', $datos);
            }

            $user = (new M_Usuarios())->buscar($nombre);

            if (!$user || (string)$user['clave'] !== $contrasena) {
                $datos['errorGeneral'] = 'Nombre o contraseña incorrectos';
                return view('autenticacion/login', array_merge($datos, ['isLoginPage' => true]));
            }

            // Iniciar sesión
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['usuario_id'] = (int)$user['id'];
            $_SESSION['usuario_nombre'] = $_SESSION['nombre_operario'] = (string)$user['usuario'];
            $_SESSION['rol'] = strtolower((string)($user['rol'] ?? 'operario'));

            // Guardar sesión y cookies
            (new M_Usuarios())->actualizar((int)$user['id'], session_id(), $guardar);
            setcookie('guardar_clave', $guardar ? '1' : '0', time() + 2592000, '/'); // 30 días
            setcookie('clave_plana', $guardar ? $contrasena : '', $guardar ? time() + 2592000 : time() - 3600, '/');

            // Redirección según rol
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            $destino = $_SESSION['rol'] === 'admin' ? '/admin/tareas' : '/operario/tareas';
            header("Location: $baseUrl$destino");
            exit;
        }

        // GET: precargar valores desde cookies
        $nombre = $_COOKIE['usuario'] ?? '';
        $contrasena = $_COOKIE['clave_plana'] ?? '';
        $guardar = !empty($_COOKIE['guardar_clave']) && $_COOKIE['guardar_clave'] === '1';

        return view('autenticacion/login', [
            'nombre' => $nombre,
            'contraseña' => $contrasena,
            'guardar_clave' => $guardar ? 'on' : '',
            'isLoginPage' => true
        ]);
    }

    /**
     * Cierra la sesión y limpia cookies si es necesario.
     */
    public function logout()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $id = (int)($_SESSION['usuario_id'] ?? 0);

        if ($id) {
            (new M_Usuarios())->actualizar($id, null, !empty($_COOKIE['guardar_clave']) && $_COOKIE['guardar_clave'] === '1');
        }

        session_unset();
        session_destroy();

        // Solo borrar la cookie de la clave
        setcookie('clave_plana', '', time() - 3600, '/');

        return view('autenticacion/login', [
            'mensaje' => 'Sesión cerrada',
            'isLoginPage' => true
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\M_Usuarios;
use App\Models\M_Tareas;

/**
 * Controlador para manejar la autenticación de usuarios.
 * Incluye funciones para iniciar y cerrar sesión.
 */
class C_Auth extends C_Controller
{
    /**
     * Muestra el formulario de inicio de sesión y procesa los datos enviados.
     *
     * Si se envían datos por POST, intenta autenticar al usuario.
     * Si la autenticación es exitosa, inicia la sesión y redirige.
     * Si no, muestra el formulario de login con un mensaje de error.
     *
     * @return \Illuminate\View\View|void Retorna la vista del formulario de login o redirige a otra página.
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

            $user = (new M_Usuarios())->buscarPorNombre($nombre);

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
            setcookie('usuario_recordado', $guardar ? $nombre : '', $guardar ? time() + 2592000 : time() - 3600, '/');

            // Redirección según rol
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            $destino = $_SESSION['rol'] === 'admin' ? '/admin/tareas' : '/operario/tareas';
            header("Location: $baseUrl$destino");
            exit;
        }

        // GET: precargar valores desde cookies
        $nombre = $_COOKIE['usuario_recordado'] ?? '';
        $contrasena = '';

        $guardar = !empty($_COOKIE['guardar_clave']) && $_COOKIE['guardar_clave'] === '1';

        return view('autenticacion/login', [
            'nombre' => $nombre,
            'contraseña' => $contrasena,
            'guardar_clave' => $guardar ? 'on' : '',
            'isLoginPage' => true
        ]);
    }

    /**
     * Cierra la sesión del usuario actual.
     *
     * Invalida la sesión y redirige al usuario a la página de inicio de sesión.
     */
    public function logout()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $id = (int)($_SESSION['usuario_id'] ?? 0);
        $nombre_usuario = $_SESSION['usuario_nombre'] ?? '';

        // Verificar si el usuario había marcado "recordarme"
        $guardar_clave = !empty($_COOKIE['guardar_clave']) && $_COOKIE['guardar_clave'] === '1';

        if ($id) {
            (new M_Usuarios())->actualizar($id, null, $guardar_clave);
        }

        session_unset();
        session_destroy();

        // Si el usuario había marcado "recordarme", mantener la cookie de usuario_recordado
        if ($guardar_clave) {
            setcookie('usuario_recordado', $nombre_usuario, time() + 2592000, '/'); // 30 días
        } else {
            // Si no, borrarla
            setcookie('usuario_recordado', '', time() - 3600, '/');
        }

        // Redirigir al login
        $_SESSION['mensaje'] = 'Sesión cerrada';
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        header("Location: $baseUrl/login");
        exit;
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use App\Models\Tareas;

/**
 * Controlador de autenticación simple con contraseña plana,
 * sesión y cookie para recordar la clave.
 */
class ControladorAuth extends Controller
{
    /**
     * Muestra el formulario de login y procesa el inicio de sesión.
     *
     * @return mixed Vista de login o listado de tareas tras autenticación.
     */
    public function login()
    {
        if ($_POST) {
            // Asegurar esquema de usuarios (tabla y admin por defecto)
            try {
                (new Usuarios())->asegurarEsquema();
            } catch (\Throwable $e) {
            }
            $nombre = $_POST['usuario'];
            $contrasena = $_POST['clave'];
            $guardar = isset($_POST['guardar_clave']);
            $datos = ['nombre' => $nombre, 'contraseña' => $contrasena, 'guardar_clave' => $guardar ? 'on' : ''];

            if ($nombre === '' || $contrasena === '') {
                $datos['errorGeneral'] = 'Debe introducir nombre y contraseña';
                return view('autenticacion/login', array_merge($datos, ['isLoginPage' => true]));
            }

            $usuarios = new Usuarios();
            $user = null;
            try {
                $user = $usuarios->buscarPorNombre($nombre);
            } catch (\Throwable $e) {
                $datos['errorGeneral'] = 'No se pudo acceder a usuarios';
                return view('autenticacion/login', array_merge($datos, ['isLoginPage' => true]));
            }

            if (!$user || (string)$user['clave'] !== $contrasena) {
                $datos['errorGeneral'] = 'Nombre o contraseña incorrectos';
                return view('autenticacion/login', array_merge($datos, ['isLoginPage' => true]));
            }

            // Sesión
            $sesionId = session()->getId();
            $rol = isset($user['rol']) ? strtolower((string)$user['rol']) : 'operario';
            session(['usuario_id' => (int)$user['id'], 'usuario_nombre' => (string)$user['usuario'], 'rol' => $rol]);

            // Preferencias y cookie de clave
            try {
                $usuarios->actualizarSesionYPreferencias((int)$user['id'], $sesionId, $guardar);
            } catch (\Throwable $e) {
                // sin bloqueo
            }

            // COOKIES
            if ($guardar) {
                setcookie('guardar_clave', '1', time() + 60 * 60 * 24 * 30, '/');
                setcookie('clave_plana', $contrasena, time() + 60 * 60 * 24 * 30, '/');
            } else {
                setcookie('guardar_clave', '0', time() + 60 * 60 * 24 * 30, '/');
                setcookie('clave_plana', '', time() - 3600, '/');
            }

            // PAGINAION
            // Cargar y devolver listado de tareas directamente
            $modelo = new Tareas();
            $tareas = [];
            $porPagina = ControladorTareas::TAREASXPAGINA;
            $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            try {
                $tareas = $modelo->listar($porPagina, $paginaActual);
            } catch (\Throwable $e) {
                $tareas = [];
            }
            if ($paginaActual < 1) $paginaActual = 1;
            $totalElementos = 0;
            $totalPaginas = 1;
            // ceil es para redondear al enterro mayor o igual
            try {
                $totalElementos = $modelo->contar();
                $totalPaginas = (int) max(1, ceil($totalElementos / $porPagina));
            } catch (\Throwable $e3) {
            }
            return view('tareas/lista', ['tareas' => $tareas, 'mensaje' => 'Sesión iniciada correctamente', 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas]);
        }

        // GET: precargar valores desde cookies
        $nombre = '';
        $contrasena = isset($_COOKIE['clave_plana']);
        $guardar = isset($_COOKIE['guardar_clave']) && $_COOKIE['guardar_clave'] === '1';
        return view('autenticacion/login', ['nombre' => $nombre, 'contraseña' => $contrasena, 'guardar_clave' => $guardar ? 'on' : '', 'isLoginPage' => true]);
    }

    /**
     * Cierra la sesión y limpia cookies si es necesario.
     *
     * @return mixed Vista de login con mensaje.
     */
    public function logout()
    {
        $id = (int) session('usuario_id', 0);
        if ($id) {
            try {
                (new Usuarios())->actualizarSesionYPreferencias($id, null, isset($_COOKIE['guardar_clave']) && $_COOKIE['guardar_clave'] === '1');
            } catch (\Throwable $e) {
            }
        }
        session()->forget(['usuario_id', 'usuario_nombre']);

        // No borrar la cookie de preferencia; sólo la clave si existe
        setcookie('clave_plana', '', time() - 3600, '/');

        return view('autenticacion/login', ['mensaje' => 'Sesión cerrada', 'isLoginPage' => true]);
    }
}

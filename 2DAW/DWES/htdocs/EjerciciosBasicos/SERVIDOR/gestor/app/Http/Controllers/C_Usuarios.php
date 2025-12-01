<?php

namespace App\Http\Controllers;

use App\Models\M_Usuarios;
use Illuminate\Http\Request;
use App\Models\M_Funciones;

class C_Usuarios extends C_Controller
{
    const USUARIOS_POR_PAGINA = 10; // Puedes ajustar esto según tus necesidades

    public function listar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $modelo = new M_Usuarios();
        $error = '';
        $usuarios = [];

        $paginationData = $this->getPaginationData($modelo, self::USUARIOS_POR_PAGINA);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];

        $offset = (int)($paginaActual - 1) * self::USUARIOS_POR_PAGINA;
        $usuarios = $modelo->listar(self::USUARIOS_POR_PAGINA, $offset);

        $datos = ['usuarios' => $usuarios, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas, 'baseUrl' => 'admin/usuarios'];
        if ($error) $datos['errorGeneral'] = $error;

        return view('usuarios.listar', $datos);
    }

    public function crear()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        return view('usuarios.crear', ['usuario' => null, 'modo' => 'alta']);
    }

    public function guardar(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $errores = M_Funciones::validarUsuario($_POST);

        if (!empty($errores)) {
            return view('usuarios.crear', ['errores' => $errores, 'usuario' => (object)$_POST, 'modo' => 'alta']);
        }

        $modelo = new M_Usuarios();
        $modelo->insertar($_POST);

        return redirect('/admin/usuarios')->with('success', 'Usuario creado correctamente.');
    }

    public function editar(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $id = $request->input('id');
        $modelo = new M_Usuarios();
        $usuario = $modelo->buscar((int)$id);

        if (!$usuario) {
            return redirect('/admin/usuarios')->with('error', 'Usuario no encontrado.');
        }

        return view('usuarios.editar', ['usuario' => $usuario, 'modo' => 'edicion']);
    }

    public function actualizar(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $errores = M_Funciones::validarUsuario($_POST, true); // true para indicar que es edición

        if (!empty($errores)) {
            return view('usuarios.editar', ['errores' => $errores, 'usuario' => (object)$_POST, 'modo' => 'edicion']);
        }

        $modelo = new M_Usuarios();
        $modelo->actualizarUsuario($_POST);

        return redirect('/admin/usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function confirmarEliminacion(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $id = $request->input('id');
        $modelo = new M_Usuarios();
        $usuario = $modelo->buscar((int)$id);

        if (!$usuario) {
            return redirect('/admin/usuarios')->with('error', 'Usuario no encontrado.');
        }

        return view('usuarios.confirmar_eliminacion', ['usuario' => $usuario]);
    }

    public function eliminar(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $id = $request->input('id');
        $modelo = new M_Usuarios();
        $modelo->eliminar((int)$id);

        return redirect('/admin/usuarios')->with('success', 'Usuario eliminado correctamente.');
    }
}

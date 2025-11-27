<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;

/**
 * Gestión de usuarios (solo Admin): listar, crear, editar, eliminar.
 */
class C_Usuarios extends C_Controller
{
    /**
     * Verifica rol Admin, devuelve vista de login si no.
     */
    private function requireAdmin()
    {
        if (session('rol') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        return null;
    }

    public function listar()
    {
        if ($r = $this->requireAdmin()) return $r;
        $m = new Usuarios();
        $usuarios = [];
        try {
            $usuarios = $m->listar();
        } catch (\Throwable $e) {
            $usuarios = [];
        }
        return view('usuarios/lista', ['usuarios' => $usuarios]);
    }

    public function crear()
    {
        if ($r = $this->requireAdmin()) return $r;
        if ($_POST) {
            $usuario = trim((string)($_POST['usuario'] ?? ''));
            $clave = (string)($_POST['clave'] ?? '');
            $rol = strtolower((string)($_POST['rol'] ?? 'operario'));
            //con in_array comprobamos si el rol es valido
            if ($usuario === '' || $clave === '' || !in_array($rol, ['admin', 'operario'])) {
                return view('usuarios/formulario', ['errorGeneral' => 'Datos inválidos', 'usuario' => $usuario, 'clave' => $clave, 'rol' => $rol]);
            }
            try {
                (new Usuarios())->crear($usuario, $clave, $rol);
            } catch (\Throwable $e) {
                return view('usuarios/formulario', ['errorGeneral' => 'No se pudo crear el usuario', 'usuario' => $usuario, 'clave' => $clave, 'rol' => $rol]);
            }
            return $this->listar();
        }
        return view('usuarios/formulario', ['usuario' => '', 'clave' => '', 'rol' => 'operario']);
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;

        if ($r = $this->requireAdmin()) return $r;
        $m = new Usuarios();
        if ($_POST) {
            $usuario = trim((string)($_POST['usuario'] ?? ''));
            $clave = (string)($_POST['clave'] ?? '');
            $rol = strtolower((string)($_POST['rol'] ?? 'operario'));
            if ($usuario === '' || $clave === '' || !in_array($rol, ['admin', 'operario'])) {
                return view('usuarios/formulario', ['errorGeneral' => 'Datos inválidos', 'id' => (int)$id, 'usuario' => $usuario, 'clave' => $clave, 'rol' => $rol]);
            }
            try {
                $m->actualizar((int)$id, $usuario, $clave, $rol);
            } catch (\Throwable $e) {
                return view('usuarios/formulario', ['errorGeneral' => 'No se pudo actualizar', 'id' => (int)$id, 'usuario' => $usuario, 'clave' => $clave, 'rol' => $rol]);
            }
            return $this->listar();
        }
        $u = null;
        try {
            $u = $m->buscarPorId((int)$id);
        } catch (\Throwable $e) {
            $u = null;
        }
        if (!$u) return $this->listar();
        $u['id'] = (int)$id;
        return view('usuarios/formulario', $u);
    }
    public function eliminar()
    {
        if ($r = $this->requireAdmin()) return $r;
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            // Manejar el error si el ID no está presente o es inválido
            return $this->listar(); // O redirigir a una página de error
        }
        try {
            (new Usuarios())->eliminar($id);
        } catch (\Throwable $e) {
            // Manejar el error de eliminación si es necesario
        }
        return $this->listar();
    }
}

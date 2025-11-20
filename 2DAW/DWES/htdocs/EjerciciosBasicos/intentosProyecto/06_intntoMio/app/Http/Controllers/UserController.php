<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController
{
    public static function create($datos)
    {
        // Solo admin puede crear usuarios
        if ($_SESSION['user']['role'] !== 'admin') return false;

        $datosUsuario = [
            'username' => $datos['username'],
            'password' => $datos['password'],
            'role' => $datos['role'] ?? 'operario'
        ];
        return User::create($datosUsuario);
    }

    public static function all()
    {
        return User::all();
    }
}

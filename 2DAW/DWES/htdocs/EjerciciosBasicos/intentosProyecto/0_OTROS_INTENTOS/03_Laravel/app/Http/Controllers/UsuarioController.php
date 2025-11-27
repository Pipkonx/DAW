<?php

namespace App\Http\Controllers;

use App\Legacy\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = (new Usuario())->getAll();
        return view('usuarios.index', compact('usuarios'));
    }
}
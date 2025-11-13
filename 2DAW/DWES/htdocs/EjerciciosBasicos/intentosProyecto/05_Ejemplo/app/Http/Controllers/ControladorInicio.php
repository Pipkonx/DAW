<?php
namespace App\Http\Controllers;

class ControladorInicio extends Controller
{
    public function inicio()
    {
        return view('index');
    }
}
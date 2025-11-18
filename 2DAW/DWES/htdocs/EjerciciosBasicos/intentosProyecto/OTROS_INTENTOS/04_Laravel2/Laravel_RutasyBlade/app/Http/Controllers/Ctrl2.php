<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Ctrl2 extends Controller
{
    public $calificaciones = [
            ['nombre' => 'Juan', 'calificacion' => 5],
            ['nombre' => 'Maria', 'calificacion' => 7],
            ['nombre' => 'Carmen', 'calificacion' => 3],
            ['nombre' => 'Andrea', 'calificacion' => 4],
            ['nombre' => 'Luis', 'calificacion' => 2],
            ['nombre' => 'Manuel', 'calificacion' => 9],
            ['nombre' => 'Pilar', 'calificacion' => 7],
            ['nombre' => 'David', 'calificacion' => 3],
        ];

    public function ej01()
    {
        return view('01holamundo');
    }

    public function ej02_1()
    {
        return view('02hola', ['nombre' => 'Pepe']);
    }

    public function ej02()
    {
        return view('02hola')->with('nombre', 'PEPE');
    }

    public function ej03()
    {
        return view(
            '03saludos',
            [
                'nombres' => ['Pepe', 'Juan', 'Maria', 'Andres', 'Pedro', 'Ana', 'Graciela']
            ]
        );
    }

    public function ej04()
    {
        //$plantilla = '04calificaciones'; // Error
        $plantilla = '04calificaciones1'; // Bien
        return view($plantilla, [
            'calificaciones' => $this->calificaciones
        ]);
    }

    public function ej10()
    {
        return view('10calificaciones', [
            'calificaciones'=>$this->calificaciones
        ]);
    }

    public function ej11()
    {
        return view('11holaconplantilla');
    }
}

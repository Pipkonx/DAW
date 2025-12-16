<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index()
    {
        $alumnos = Alumno::all();
        return response()->json($alumnos);
    }

    public function pluck()
    {
        $alumno = Alumno::all()->pluck("nombre");
        return response()->json($alumno);
    }

    // la diferencia de select y pluck es que select devuelve un objeto y pluck devuelve un array
    public function select()
    {
        $alumno = Alumno::all()->select("nombre");
        return response()->json($alumno);
    }

    public function find()
    {
        $alumno = Alumno::find('2');
        return response()->json($alumno);
    }

    public function findMany()
    {
        $alumno = Alumno::findMany([2, 3, 4]);
        return response()->json($alumno);
    }

    public function findNombre()
    {
        $alumno = Alumno::find([3, 'nombre']);
        return response()->json($alumno);
    }

    public function findNacimiento()
    {
        $alumno = Alumno::where('fecha_nac', '>=', '1990-01-01')->get();
        return response()->json($alumno);
    }

    public function editar(){
        $alumno = Alumno::find(2);
        $alumno->nombre = "Rafael";
        $alumno->save();
    }


}

<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index()
    {
        $alumnos = Alumno::all();
        // return response()->json($alumnos);
        // otra posibilidad es hacerlo directamente con el echo
        echo $alumnos;
    }

    public function pluck()
    {
        $alumno = Alumno::all()->pluck("nombre");
        // return response()->json($alumno);
        echo $alumno;

    }

    // la diferencia de select y pluck es que select devuelve un objeto y pluck devuelve un array
    public function select()
    {
        $alumno = Alumno::all()->select("nombre");
        // return response()->json($alumno);
        echo $alumno;
    }

    public function find()
    {
        $alumno = Alumno::find('2');
        // return response()->json($alumno);
        echo $alumno;
    }

    public function findMany()
    {
        $alumno = Alumno::findMany([2, 3, 4]);
        // return response()->json($alumno);
        echo $alumno;
    }

    public function findNombre()
    {
        $alumno = Alumno::find([3, 'nombre']);
        // return response()->json($alumno);
        echo $alumno;
    }

    public function findNacimiento()
    {
        $alumno = Alumno::where('fecha_nac', '>=', '1990-01-01')->get();
        // return response()->json($alumno);
        echo $alumno;
    }

    public function editar(){
        $alumno = Alumno::find(2);
        $alumno->nombre = "Rafael";
        $alumno->save();
    }


    //borrar alumno anterior

    public function borrar(){
        $Alumno = Alumno::find(2);
        $Alumno->delete();
        echo $Alumno;
    }
    
    // borrar aluumno cuyo id es 7
    public function borrar7(){
        $Alumno = Alumno::find(7);
        $Alumno->delete();
        echo $Alumno;
    }

    public function crear() {
        $Alumno = Alumno::create(['nombre' => 'Nuevo']);
        // $Alumno = new Alumno(['nombre' => 'Nuevo']);
        $Alumno->save();
        echo $Alumno;
    }
}


// La primera actividad de tinker sería

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumnos';

    // el fillable es para hacer el valor asignable, sino da error
    protected $fillable = ['nombre'];

}

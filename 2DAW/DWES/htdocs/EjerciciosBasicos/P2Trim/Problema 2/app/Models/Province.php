<?php

/**
 * 
 * Clase Province
 * Esta clase representa la tabla de provincias en la base de datos.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase Province
 * 
 * Modelo que gestiona la información de las provincias.
 */
class Province extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code', 'name'];
}


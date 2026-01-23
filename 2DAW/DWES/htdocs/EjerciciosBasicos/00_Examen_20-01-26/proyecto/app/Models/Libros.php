<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Libros extends Model
{
    use HasFactory;

    // Esto permite que todos los campos se puedan rellenar desde Filament
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    # En app/Models/User.php
    public function libros()
    {
        return $this->hasMany(\App\Models\Libros::class);
    }
}

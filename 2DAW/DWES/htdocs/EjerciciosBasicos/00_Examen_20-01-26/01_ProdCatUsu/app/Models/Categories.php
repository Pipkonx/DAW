<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categories extends Model
{
    use HasFactory;

    // Esto permite que todos los campos se puedan rellenar desde Filament
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'category_id');
    }
}

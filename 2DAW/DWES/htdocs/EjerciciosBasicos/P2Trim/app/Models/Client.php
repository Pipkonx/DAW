<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    // Estos son los campos que podemos guardar en la tabla de Clientes
    protected $fillable = [
        'cif', 'name', 'phone', 'email', 'bank_account', 
        'country', 'currency', 'monthly_fee', 'is_active'
    ];

    // Relación: Un cliente puede tener muchas cuotas (facturas)
    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    // Relación: Un cliente puede tener muchas tareas de incidencias
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

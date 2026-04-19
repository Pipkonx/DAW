<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // Todos los campos que forman una Tarea de incidencia
    protected $fillable = [
        'client_id', 'contact_person', 'contact_phone', 'contact_email',
        'description', 'address', 'city', 'postal_code', 'province_code',
        'status', 'operator_id', 'completion_date', 'previous_notes',
        'posterior_notes', 'attachment_path'
    ];

    // Relación: La tarea pertenece a un Cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relación: La tarea tiene un Operario asignado (que es un Usuario)
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    // Relación: La tarea ocurre en una Provincia concreta
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }
}

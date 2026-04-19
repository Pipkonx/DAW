<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'dni',
        'cif',
        'name',
        'email',
        'phone',
        'address',
        'hire_date',
        'role',
        'is_active',
        'password',
    ];

    /**
     * Relación: Un operario tiene muchas tareas asignadas.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'operator_id');
    }

    /**
     * Comprueba si el usuario es administrador.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Comprueba si el usuario es un operario.
     */
    public function isOperator()
    {
        return $this->role === 'operator';
    }

    /**
     * Atributos que deben ocultarse (no se envían en JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Define cómo se deben transformar los datos al leerlos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

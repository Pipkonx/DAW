<?php
/**
 * Autor: Rafael
 * Fecha: 19/04/2026
 * Versión: 1.0
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Clase User (Usuario / Empleado)
 * 
 * Esta clase representa a los trabajadores de la empresa (Administradores y Operarios).
 * Controla el acceso a la aplicación y los datos básicos del personal.
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atributos que se pueden asignar masivamente.
     * @var array<string>
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
     * Relación: Un operario (usuario) tiene muchas tareas asignadas.
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'operator_id');
    }

    /**
     * Comprueba si el usuario tiene el rol de administrador.
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Comprueba si el usuario tiene el rol de operario.
     * @return bool
     */
    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Atributos que deben ocultarse al convertir el modelo a una lista (JSON).
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Define cómo se deben transformar los datos al leerlos (Casting).
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}


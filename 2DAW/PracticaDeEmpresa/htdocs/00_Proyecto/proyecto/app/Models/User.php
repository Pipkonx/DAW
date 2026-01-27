<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;

/**
 * @class User
 * @brief Modelo que representa a un usuario del sistema.
 * 
 * Centraliza la autenticación y las relaciones con los diferentes perfiles
 * de usuario. Utiliza el trait Notifiable para la gestión de notificaciones.
 */
class User extends Authenticatable implements HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    /**
     * @brief Trait Notifiable
     * Esencial para que Filament pueda enviar notificaciones a la base de datos
     * y mostrarlas en el panel de usuario.
     */
    use HasFactory, Notifiable, HasRoles;

    /**
     * @brief Obtiene la URL del avatar para Filament.
     * 
     * @return string|null
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    /**
     * @brief Obtiene el nombre del primer rol asignado.
     * 
     * @return string Nombre del rol o 'Sin Rol'.
     */
    public function getRoleNameAttribute(): string
    {
        return $this->getRoleNames()->first() ?? 'Sin Rol';
    }

    /**
     * @brief Comprueba si el usuario tiene el rol de administrador.
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * @brief Comprueba si el usuario tiene el rol de tutor de curso.
     * @return bool
     */
    public function isTutorCurso(): bool
    {
        return $this->hasRole('tutor_curso');
    }

    /**
     * @brief Comprueba si el usuario tiene el rol de tutor de empresa.
     * @return bool
     */
    public function isTutorEmpresa(): bool
    {
        return $this->hasRole('tutor_empresa');
    }

    /**
     * @brief Comprueba si el usuario tiene el rol de alumno.
     * @return bool
     */
    public function isAlumno(): bool
    {
        return $this->hasRole('alumno');
    }

    /**
     * @brief Define la relación 1:1 con el perfil de Alumno.
     * 
     * @return HasOne Relación con el modelo Alumno.
     */
    public function alumno(): HasOne
    {
        return $this->hasOne(Alumno::class);
    }

    /**
     * @brief Define la relación 1:1 con el perfil de Tutor de Curso.
     * 
     * @return HasOne Relación con el modelo TutorCurso.
     */
    public function perfilTutorCurso(): HasOne
    {
        return $this->hasOne(TutorCurso::class);
    }

    /**
     * @brief Define la relación 1:1 con el perfil de Tutor de Prácticas.
     * 
     * @return HasOne Relación con el modelo TutorPracticas.
     */
    public function perfilTutorPracticas(): HasOne
    {
        return $this->hasOne(TutorPracticas::class);
    }

    /**
     * @brief Define la relación 1:1 con el perfil de Empresa.
     * 
     * @return HasOne Relación con el modelo Empresa.
     */
    public function empresa(): HasOne
    {
        return $this->hasOne(Empresa::class);
    }

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'reference_id',
        'avatar_url',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtiene los atributos que deben ser casteados.
     *
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

<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Clase Client (Cliente)
 * 
 * Gestiona la información de los clientes de la empresa, incluyendo su CIF,
 * datos de contacto y configuración de facturación (cuota mensual y moneda).
 */
class Client extends Model
{
    /**
     * Campos que se pueden asignar masivamente.
     * @var array<string>
     */
    protected $fillable = [
        'cif', 'name', 'phone', 'email', 'bank_account', 
        'country', 'currency', 'monthly_fee', 'is_active'
    ];

    /**
     * Relación: Un cliente puede tener muchas cuotas (facturas) asociadas.
     * @return HasMany
     */
    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    /**
     * Relación: Un cliente puede haber registrado muchas tareas o incidencias.
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Método para buscar un cliente validando su identidad (CIF y Teléfono).
     * Esto mueve la lógica de búsqueda fuera del controlador.
     * 
     * @param string|null $cif
     * @param string|null $phone
     * @return Client|null
     */
    public static function findVerified(?string $cif, ?string $phone): ?Client
    {
        if (!$cif || !$phone) {
            return null;
        }

        return self::where('cif', $cif)
                   ->where('phone', $phone)
                   ->first();
    }
}



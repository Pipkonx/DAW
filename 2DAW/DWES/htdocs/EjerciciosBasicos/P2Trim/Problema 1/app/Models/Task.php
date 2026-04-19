<?php
/**
 * Autor: Rafael
 * Fecha: 19/04/2026
 * Versión: 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Clase Task (Tarea)
 * 
 * Representa una incidencia o tarea de mantenimiento en el sistema.
 * Contiene toda la información sobre el cliente, el contacto, el operario
 * asignado y el estado de la reparación.
 */
class Task extends Model
{
    /**
     * Campos que se pueden rellenar automáticamente (Mass Assignment).
     * @var array<string>
     */
    protected $fillable = [
        'client_id', 'contact_person', 'contact_phone', 'contact_email',
        'description', 'address', 'city', 'postal_code', 'province_code',
        'status', 'operator_id', 'completion_date', 'previous_notes',
        'posterior_notes', 'attachment_path'
    ];

    /**
     * Relación: Una tarea pertenece a un Cliente.
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación: Una tarea tiene un Operario asignado (que es un registro de la tabla Users).
     * @return BelongsTo
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * Relación: La tarea está asociada a una Provincia (vía código INE).
     * @return BelongsTo
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    /**
     * Scope (Filtro): Permite filtrar las tareas de forma sencilla desde el controlador.
     * Esto cumple con la norma de "No incluir lógica de consulta en el controlador".
     * 
     * @param Builder $query La consulta que estamos construyendo.
     * @param array $filters Lista de filtros (status, client_id, etc.).
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // Si nos pasan un estado, filtramos por él
        if (isset($filters['status']) && $filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }

        // Si nos pasan un ID de cliente, filtramos por él
        if (isset($filters['client_id']) && $filters['client_id'] !== null) {
            $query->where('client_id', $filters['client_id']);
        }

        // Si el usuario es un operario, solo mostramos sus tareas
        if (isset($filters['operator_id']) && $filters['operator_id'] !== null) {
            $query->where('operator_id', $filters['operator_id']);
        }

        return $query;
    }
}


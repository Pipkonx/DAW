<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $fillable = ['name', 'cif', 'currency'];

    public function cuotas(): HasMany
    {
        return $this->hasMany(Cuota::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'cif', 'name', 'phone', 'email', 'bank_account', 
        'country', 'currency', 'monthly_fee', 'is_active'
    ];

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

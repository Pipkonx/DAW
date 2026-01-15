<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'client_id', 'contact_person', 'contact_phone', 'contact_email',
        'description', 'address', 'city', 'postal_code', 'province_code',
        'status', 'operator_id', 'completion_date', 'previous_notes',
        'posterior_notes', 'attachment_path'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }
}

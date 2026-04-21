<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    protected $fillable = [
        'user_id', 
        'ip_address', 
        'city', 
        'country', 
        'user_agent', 
        'browser', 
        'browser_version', 
        'os', 
        'device', 
        'session_id', 
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

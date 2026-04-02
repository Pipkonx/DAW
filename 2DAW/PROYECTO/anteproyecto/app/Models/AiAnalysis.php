<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'report',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    /** @use HasFactory<\Database\Factories\ProgressFactory> */
    use HasFactory;

    protected $table = 'progress';

    protected $fillable = [
        'user_id',
        'current_mission',
        'unlocked_technologies',
        'resources',
        'stats'
    ];

    protected $casts = [
        'unlocked_technologies' => 'array',
        'resources' => 'array',
        'stats' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

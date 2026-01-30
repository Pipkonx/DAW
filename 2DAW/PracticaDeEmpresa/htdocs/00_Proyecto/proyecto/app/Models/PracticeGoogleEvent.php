<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeGoogleEvent extends Model
{
    protected $fillable = [
        'practice_id',
        'user_id',
        'google_event_id',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

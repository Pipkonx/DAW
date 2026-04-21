<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamousInvestor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'cik', 'description', 'avatar', 'location', 'type', 'last_synced_at'
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function holdings()
    {
        return $this->hasMany(FamousInvestorHolding::class);
    }

    public function trades()
    {
        return $this->hasMany(FamousInvestorTrade::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'guru_followers', 'famous_investor_id', 'user_id')->withTimestamps();
    }

    public function isFollowedBy($userId)
    {
        return $this->followers()->where('user_id', $userId)->exists();
    }
}

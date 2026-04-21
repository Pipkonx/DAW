<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'google_id',
        'avatar',
        'banner_path',
        'bio',
        'pinned_post_id',
        'investment_return_rate',
        'enable_tax_projection',
        'tax_rate',
        'is_admin',
        'google2fa_secret',
        'onboarding_completed_at',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    protected $appends = [
        'tier',
        'subscription_status',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'enable_tax_projection' => 'boolean',
            'tax_rate' => 'decimal:2',
            'investment_return_rate' => 'decimal:2',
            'is_admin' => 'boolean',
        ];
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function aiAnalyses()
    {
        return $this->hasMany(AiAnalysis::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function reposts()
    {
        return $this->belongsToMany(Post::class, 'reposts')->withTimestamps();
    }

    public function bookmarks()
    {
        return $this->belongsToMany(Post::class, 'bookmarks')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_id', 'follower_id')->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followed_id')->withTimestamps();
    }

    public function followingGurus()
    {
        return $this->belongsToMany(FamousInvestor::class, 'guru_followers', 'user_id', 'famous_investor_id')->withTimestamps();
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocker_id', 'blocked_id')->withTimestamps();
    }

    public function getTierAttribute()
    {
        $sub = $this->subscription('default');
        if (!$sub || !$sub->active()) return 'none';
        
        $priceId = $sub->stripe_price;
        if ($priceId === config('services.stripe.price_premium')) return 'premium';
        if ($priceId === config('services.stripe.price_pro')) return 'pro';
        if ($priceId === config('services.stripe.price_basic')) return 'basic';
        
        return 'none';
    }

    public function getSubscriptionStatusAttribute()
    {
        $sub = $this->subscription('default');
        if (!$sub || !$sub->active()) return 'Sin plan activo';
        
        if ($sub->onGracePeriod()) {
            return 'Se cancela el ' . $sub->ends_at->format('d/m/Y');
        }
        
        return 'Activa (Auto-renovable)';
    }

    public function isFollowing($userId)
    {
        return $this->following()->where('followed_id', $userId)->exists();
    }

    public function hasBlocked($userId)
    {
        return $this->blockedUsers()->where('blocked_id', $userId)->exists();
    }
}


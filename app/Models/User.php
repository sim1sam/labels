<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // User type constants
    const TYPE_ADMIN = 'admin';
    const TYPE_MERCHANT = 'merchant';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::TYPE_ADMIN;
    }

    /**
     * Check if user is merchant
     */
    public function isMerchant(): bool
    {
        return $this->user_type === self::TYPE_MERCHANT;
    }

    /**
     * Get all available user types
     */
    public static function getUserTypes(): array
    {
        return [
            self::TYPE_ADMIN => 'Admin',
            self::TYPE_MERCHANT => 'Merchant',
        ];
    }

    /**
     * Relationship with Merchant
     */
    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }
}

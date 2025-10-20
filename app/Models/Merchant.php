<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $fillable = [
        'merchant_id',
        'shop_name',
        'logo',
        'email',
        'phone',
        'address',
        'api_key',
        'api_secret',
        'api_config',
        'status',
        'user_id',
    ];

    protected $casts = [
        'api_config' => 'array',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with couriers (many-to-many)
    public function couriers()
    {
        return $this->belongsToMany(Courier::class, 'merchant_courier')
                    ->withPivot('merchant_custom_id', 'status', 'merchant_api_key', 'merchant_api_secret', 'merchant_api_config', 'is_primary')
                    ->withTimestamps();
    }

    // Get active couriers for this merchant
    public function activeCouriers()
    {
        return $this->couriers()->wherePivot('status', 'active');
    }

    // Generate unique merchant ID
    public static function generateMerchantId()
    {
        do {
            $merchantId = 'MERCH' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('merchant_id', $merchantId)->exists());
        
        return $merchantId;
    }
}

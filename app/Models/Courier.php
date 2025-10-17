<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'courier_name',
        'phone',
        'email',
        'vehicle_type',
        'status',
        'rating',
        'total_deliveries',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
    ];

    // Relationship with merchants (many-to-many)
    public function merchants()
    {
        return $this->belongsToMany(Merchant::class, 'merchant_courier')
                    ->withPivot('merchant_custom_id', 'status')
                    ->withTimestamps();
    }

    // Get active merchants for this courier
    public function activeMerchants()
    {
        return $this->merchants()->wherePivot('status', 'active');
    }
}

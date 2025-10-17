<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    protected $fillable = [
        'parcel_id',
        'merchant_id',
        'customer_name',
        'mobile_number',
        'delivery_address',
        'cod_amount',
        'status',
        'courier_id',
        'notes',
        'pickup_date',
        'delivery_date',
        'printed_at',
        'created_by',
    ];

    protected $casts = [
        'cod_amount' => 'decimal:2',
        'pickup_date' => 'datetime',
        'delivery_date' => 'datetime',
        'printed_at' => 'datetime',
    ];

    // Relationship with Merchant
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    // Relationship with Courier
    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    // Generate unique parcel ID
    public static function generateParcelId()
    {
        do {
            $parcelId = 'PARCEL' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('parcel_id', $parcelId)->exists());
        
        return $parcelId;
    }

    // Get status badge color
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'warning',
            'assigned' => 'info',
            'picked_up' => 'primary',
            'in_transit' => 'secondary',
            'delivered' => 'success',
            'failed' => 'danger',
            default => 'secondary'
        };
    }

    // Get status display text
    public function getStatusDisplayText()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'assigned' => 'Assigned',
            'picked_up' => 'Picked Up',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'failed' => 'Failed',
            default => 'Unknown'
        };
    }

    // Check if parcel label has been printed
    public function isPrinted()
    {
        return !is_null($this->printed_at);
    }

    // Mark parcel as printed
    public function markAsPrinted()
    {
        $this->update(['printed_at' => now()]);
    }

    // Check if parcel was created by merchant
    public function isCreatedByMerchant()
    {
        return $this->created_by === 'merchant';
    }

    // Get creator display text
    public function getCreatedByDisplayText()
    {
        return $this->created_by === 'merchant' ? 'Merchant' : 'Admin';
    }
}

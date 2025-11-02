<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    protected $fillable = [
        'parcel_id',
        'merchant_id',
        'tracking_number',
        'courier_tracking_number',
        'tracking_history',
        'last_tracking_update',
        'customer_name',
        'sender_name',
        'sender_phone',
        'sender_address',
        'mobile_number',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'delivery_address',
        'cod_amount',
        'weight',
        'description',
        'declared_value',
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
        'weight' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'tracking_history' => 'array',
        'pickup_date' => 'datetime',
        'delivery_date' => 'datetime',
        'printed_at' => 'datetime',
        'last_tracking_update' => 'datetime',
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

    // Generate tracking number
    public static function generateTrackingNumber()
    {
        do {
            $trackingNumber = 'TRK' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('tracking_number', $trackingNumber)->exists());
        
        return $trackingNumber;
    }

    // Check if parcel has tracking
    public function hasTracking(): bool
    {
        return (!empty($this->tracking_number) || !empty($this->courier_tracking_number)) && !empty($this->courier_id);
    }

    // Get tracking URL
    public function getTrackingUrl(): ?string
    {
        if (!$this->courier) {
            return null;
        }
        
        $trackingNumber = $this->courier_tracking_number ?: $this->tracking_number;
        if (!$trackingNumber) {
            return null;
        }

        return $this->courier->getTrackingUrl($trackingNumber);
    }

    // Update tracking history
    public function updateTrackingHistory(array $trackingData): void
    {
        $history = $this->tracking_history ?? [];
        $history[] = [
            'status' => $trackingData['status'] ?? $this->status,
            'location' => $trackingData['location'] ?? null,
            'timestamp' => $trackingData['timestamp'] ?? now()->toISOString(),
            'description' => $trackingData['description'] ?? null,
        ];

        $this->update([
            'tracking_history' => $history,
            'last_tracking_update' => now(),
        ]);
    }

    // Get latest tracking status
    public function getLatestTrackingStatus(): ?array
    {
        if (empty($this->tracking_history)) {
            return null;
        }

        return end($this->tracking_history);
    }
}

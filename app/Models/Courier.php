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
        'api_endpoint',
        'api_key',
        'api_secret',
        'api_config',
        'has_tracking',
        'tracking_url_template',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'api_config' => 'array',
        'has_tracking' => 'boolean',
    ];

    // Relationship with merchants (many-to-many)
    public function merchants()
    {
        return $this->belongsToMany(Merchant::class, 'merchant_courier')
                    ->withPivot('merchant_custom_id', 'status', 'merchant_api_key', 'merchant_api_secret', 'merchant_api_config', 'is_primary')
                    ->withTimestamps();
    }

    // Get active merchants for this courier
    public function activeMerchants()
    {
        return $this->merchants()->wherePivot('status', 'active');
    }

    // Check if courier has API integration
    public function hasApiIntegration(): bool
    {
        // For Steadfast, we can use config-based endpoint, so only api_key is required
        $isSteadfast = stripos($this->courier_name, 'steadfast') !== false;
        
        if ($isSteadfast) {
            // For Steadfast, only api_key is required (endpoint comes from config)
            $hasKey = !empty($this->api_key);
            
            // Log for debugging if key is missing
            if (!$hasKey) {
                \Log::warning('Steadfast courier missing API key', [
                    'courier_id' => $this->id,
                    'courier_name' => $this->courier_name,
                    'api_key_set' => false
                ]);
            }
            
            return $hasKey;
        }
        
        // For other couriers, both endpoint and key are required
        $hasEndpoint = !empty($this->api_endpoint);
        $hasKey = !empty($this->api_key);
        
        if (!$hasEndpoint || !$hasKey) {
            \Log::debug('Courier missing API configuration', [
                'courier_id' => $this->id,
                'courier_name' => $this->courier_name,
                'has_endpoint' => $hasEndpoint,
                'has_key' => $hasKey
            ]);
        }
        
        return $hasEndpoint && $hasKey;
    }

    // Check if courier supports tracking
    public function supportsTracking(): bool
    {
        return $this->has_tracking && !empty($this->tracking_url_template);
    }

    // Generate tracking URL for a parcel
    public function getTrackingUrl(string $trackingNumber): ?string
    {
        if (!$this->supportsTracking()) {
            return null;
        }

        return str_replace('{tracking_number}', $trackingNumber, $this->tracking_url_template);
    }

    // Get merchant-specific API credentials
    public function getMerchantApiCredentials(int $merchantId): array
    {
        $merchant = $this->merchants()->where('merchants.id', $merchantId)->first();
        
        if (!$merchant) {
            return [];
        }

        return [
            'api_key' => $merchant->pivot->merchant_api_key ?: $this->api_key,
            'api_secret' => $merchant->pivot->merchant_api_secret ?: $this->api_secret,
            'api_config' => $merchant->pivot->merchant_api_config ?: $this->api_config,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Courier;
use App\Models\Parcel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourierApiService
{
    /**
     * Get tracking information for a parcel
     */
    public function getTrackingInfo(Parcel $parcel): array
    {
        $courier = $parcel->courier;
        
        if (!$courier || !$courier->hasApiIntegration()) {
            return [
                'success' => false,
                'message' => 'Courier does not support API tracking',
                'data' => null
            ];
        }

        // Check if this is a Steadfast courier
        if (strtolower($courier->courier_name) === 'steadfast' || strtolower($courier->courier_name) === 'steadfast courier') {
            return $this->getSteadfastTracking($parcel, $courier);
        }

        // For other couriers, return a message that tracking is not implemented
        return [
            'success' => false,
            'message' => 'Live tracking is not yet implemented for ' . $courier->courier_name . ' courier',
            'data' => null
        ];
    }

    /**
     * Get Steadfast tracking information
     */
    private function getSteadfastTracking(Parcel $parcel, Courier $courier): array
    {
        try {
            $credentials = $courier->getMerchantApiCredentials($parcel->merchant_id);
            
            if (empty($credentials['api_key']) || empty($credentials['api_secret'])) {
                return [
                    'success' => false,
                    'message' => 'Steadfast API credentials not configured for this merchant',
                    'data' => null
                ];
            }

            $steadfastService = new \App\Services\SteadfastApiService($credentials['api_key'], $credentials['api_secret']);
            
            // Use courier tracking number if available, otherwise use parcel ID
            $trackingNumber = $parcel->courier_tracking_number ?: $parcel->parcel_id;
            
            $result = $steadfastService->getTrackingStatus($trackingNumber);
            
            if ($result['success'] && isset($result['data'])) {
                // Update parcel status if we got new tracking info
                if (isset($result['data']['status'])) {
                    $parcel->update(['status' => $result['data']['status']]);
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Steadfast tracking error: ' . $e->getMessage(), [
                'parcel_id' => $parcel->id,
                'courier_id' => $courier->id,
                'tracking_number' => $parcel->courier_tracking_number ?: $parcel->parcel_id
            ]);

            return [
                'success' => false,
                'message' => 'Steadfast API request failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Create shipment with courier
     */
    public function createShipment(Parcel $parcel): array
    {
        $courier = $parcel->courier;
        
        if (!$courier || !$courier->hasApiIntegration()) {
            return [
                'success' => false,
                'message' => 'Courier does not support API integration',
                'data' => null
            ];
        }

        try {
            $credentials = $courier->getMerchantApiCredentials($parcel->merchant_id);
            
            $shipmentData = [
                'merchant_id' => $parcel->merchant_id,
                'tracking_number' => $parcel->tracking_number,
                'sender' => [
                    'name' => $parcel->sender_name,
                    'phone' => $parcel->sender_phone,
                    'address' => $parcel->sender_address,
                ],
                'receiver' => [
                    'name' => $parcel->receiver_name,
                    'phone' => $parcel->receiver_phone,
                    'address' => $parcel->receiver_address,
                ],
                'package' => [
                    'weight' => $parcel->weight,
                    'description' => $parcel->description,
                    'value' => $parcel->declared_value,
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $credentials['api_key'],
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($courier->api_endpoint . '/shipments', $shipmentData);

            if ($response->successful()) {
                $data = $response->json();
                
                // Update parcel with courier tracking number
                if (isset($data['courier_tracking_number'])) {
                    $parcel->update([
                        'courier_tracking_number' => $data['courier_tracking_number'],
                        'status' => 'in_transit'
                    ]);
                }
                
                return [
                    'success' => true,
                    'message' => 'Shipment created successfully',
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create shipment: ' . $response->body(),
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('Courier API shipment creation error: ' . $e->getMessage(), [
                'parcel_id' => $parcel->id,
                'courier_id' => $courier->id
            ]);

            return [
                'success' => false,
                'message' => 'API request failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Update parcel tracking information
     */
    private function updateParcelTracking(Parcel $parcel, array $trackingData): void
    {
        if (isset($trackingData['status'])) {
            $parcel->update(['status' => $trackingData['status']]);
        }

        if (isset($trackingData['last_updated'])) {
            $parcel->update(['updated_at' => $trackingData['last_updated']]);
        }
    }

    /**
     * Get available couriers for a merchant with API support
     */
    public function getAvailableCouriers(int $merchantId): array
    {
        $merchant = \App\Models\Merchant::find($merchantId);
        
        if (!$merchant) {
            return [];
        }

        return $merchant->couriers()
            ->wherePivot('status', 'active')
            ->get()
            ->map(function ($courier) use ($merchantId) {
                return [
                    'id' => $courier->id,
                    'name' => $courier->courier_name,
                    'merchant_custom_id' => $courier->pivot->merchant_custom_id,
                    'has_api' => $courier->hasApiIntegration(),
                    'has_tracking' => $courier->supportsTracking(),
                    'is_primary' => $courier->pivot->is_primary,
                    'rating' => $courier->rating,
                    'vehicle_type' => $courier->vehicle_type,
                ];
            })
            ->toArray();
    }

    /**
     * Test courier API connection
     */
    public function testApiConnection(Courier $courier, int $merchantId = null): array
    {
        if (!$courier->hasApiIntegration()) {
            return [
                'success' => false,
                'message' => 'Courier does not have API integration configured'
            ];
        }

        try {
            $credentials = $merchantId ? 
                $courier->getMerchantApiCredentials($merchantId) : 
                ['api_key' => $courier->api_key, 'api_secret' => $courier->api_secret];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $credentials['api_key'],
                'Content-Type' => 'application/json',
            ])->timeout(10)->get($courier->api_endpoint . '/health');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'API connection successful',
                    'response_time' => $response->transferStats->getHandlerStat('total_time')
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'API connection failed: ' . $response->status()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'API connection error: ' . $e->getMessage()
            ];
        }
    }
}


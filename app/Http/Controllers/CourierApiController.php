<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Models\Courier;
use App\Services\CourierApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourierApiController extends Controller
{
    protected $courierApiService;

    public function __construct(CourierApiService $courierApiService)
    {
        $this->courierApiService = $courierApiService;
    }

    /**
     * Get tracking information for a parcel
     */
    public function getTracking(Request $request, Parcel $parcel): JsonResponse
    {
        $result = $this->courierApiService->getTrackingInfo($parcel);
        
        return response()->json($result);
    }

    /**
     * Create shipment with courier
     */
    public function createShipment(Request $request, Parcel $parcel): JsonResponse
    {
        $result = $this->courierApiService->createShipment($parcel);
        
        return response()->json($result);
    }

    /**
     * Get available couriers for a merchant
     */
    public function getAvailableCouriers(Request $request): JsonResponse
    {
        $merchantId = $request->get('merchant_id');
        
        if (!$merchantId) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant ID is required'
            ], 400);
        }

        $couriers = $this->courierApiService->getAvailableCouriers($merchantId);
        
        return response()->json([
            'success' => true,
            'data' => $couriers
        ]);
    }

    /**
     * Test courier API connection
     */
    public function testConnection(Request $request, Courier $courier): JsonResponse
    {
        $merchantId = $request->get('merchant_id');
        $result = $this->courierApiService->testApiConnection($courier, $merchantId);
        
        return response()->json($result);
    }

    /**
     * Get live tracking for a parcel (AJAX endpoint)
     */
    public function getLiveTracking(Request $request, Parcel $parcel): JsonResponse
    {
        if (!$parcel->hasTracking()) {
            return response()->json([
                'success' => false,
                'message' => 'Parcel does not have tracking enabled'
            ], 400);
        }

        $result = $this->courierApiService->getTrackingInfo($parcel);
        
        if ($result['success']) {
            // Update tracking history if we got new data
            if (isset($result['data']['tracking_history'])) {
                $parcel->updateTrackingHistory($result['data']['tracking_history']);
            }
        }
        
        return response()->json($result);
    }

    /**
     * Get courier selection options for parcel creation
     */
    public function getCourierOptions(Request $request): JsonResponse
    {
        $merchantId = $request->get('merchant_id');
        
        if (!$merchantId) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant ID is required'
            ], 400);
        }

        $couriers = $this->courierApiService->getAvailableCouriers($merchantId);
        
        // Format for select options
        $options = collect($couriers)->map(function ($courier) {
            return [
                'value' => $courier['id'],
                'label' => $courier['name'] . ' (' . $courier['merchant_custom_id'] . ')',
                'has_api' => $courier['has_api'],
                'has_tracking' => $courier['has_tracking'],
                'is_primary' => $courier['is_primary'],
                'rating' => $courier['rating'],
                'vehicle_type' => $courier['vehicle_type'],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $options
        ]);
    }
}
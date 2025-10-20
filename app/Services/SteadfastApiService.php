<?php

namespace App\Services;

use App\Models\Parcel;
use App\Models\Courier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastApiService
{
    protected $baseUrl;
    protected $apiKey;
    protected $secretKey;

    public function __construct($apiKey = null, $secretKey = null)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->baseUrl = config('courier.steadfast.base_url', 'https://portal.steadfast.com.bd/api/v1');
    }

    /**
     * Create order with Steadfast
     */
    public function createOrder(Parcel $parcel): array
    {
        // Skip API calls in localhost/development environment if configured
        if (config('courier.steadfast.mock_in_local') && app()->environment('local')) {
            \Log::info('Skipping Steadfast API call in local environment', [
                'parcel_id' => $parcel->parcel_id
            ]);
            
            // Return mock success response for localhost testing
            return [
                'success' => true,
                'message' => 'Order created successfully (localhost mode)',
                'data' => [
                    'consignment_id' => 'LOCAL-' . time(),
                    'tracking_code' => 'LOCAL-' . $parcel->parcel_id,
                    'status' => 'pending'
                ]
            ];
        }
        
        try {
            // Validate required fields
            $recipientName = $parcel->receiver_name ?: $parcel->customer_name;
            $recipientPhone = $parcel->receiver_phone ?: $parcel->mobile_number;
            $recipientAddress = $parcel->receiver_address ?: $parcel->delivery_address;
            
            if (empty($recipientName) || empty($recipientPhone) || empty($recipientAddress)) {
                return [
                    'success' => false,
                    'message' => 'Missing required fields: recipient name, phone, or address',
                    'data' => null
                ];
            }
            
            $orderData = [
                'invoice' => $parcel->parcel_id,
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'recipient_address' => $recipientAddress,
                'cod_amount' => (float) ($parcel->cod_amount ?: 0),
                'note' => $parcel->notes ?: 'Parcel from ' . ($parcel->merchant->shop_name ?? 'Merchant'),
                'item_description' => $parcel->description ?: 'General parcel',
                'delivery_type' => 0, // 0 = home delivery, 1 = hub pickup
            ];

            // Add optional fields if available
            if ($parcel->weight) {
                $orderData['total_lot'] = (float) $parcel->weight;
            }

            // Log the request data for debugging
            \Log::info('Steadfast API Request', [
                'url' => $this->baseUrl . '/create_order',
                'data' => $orderData,
                'api_key' => substr($this->apiKey, 0, 10) . '...'
            ]);

            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json'
            ])->timeout(config('courier.steadfast.timeout', 30))->post($this->baseUrl . '/create_order', $orderData);
            
            // Log the response for debugging
            \Log::info('Steadfast API Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] == 200) {
                    // Update parcel with Steadfast tracking information
                    $parcel->update([
                        'courier_tracking_number' => $data['consignment']['tracking_code'],
                        'status' => $this->mapStatus($data['consignment']['status'])
                    ]);

                    return [
                        'success' => true,
                        'message' => $data['message'],
                        'data' => [
                            'consignment_id' => $data['consignment']['consignment_id'],
                            'tracking_code' => $data['consignment']['tracking_code'],
                            'status' => $data['consignment']['status']
                        ]
                    ];
                } else {
                    $errorMessage = $data['message'] ?? 'Unknown error';
                    \Log::error('Steadfast API Error', [
                        'status' => $data['status'] ?? 'unknown',
                        'message' => $errorMessage,
                        'full_response' => $data
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => 'Failed to create order: ' . $errorMessage,
                        'data' => null
                    ];
                }
            } else {
                $errorMessage = 'HTTP ' . $response->status() . ': ' . $response->body();
                \Log::error('Steadfast API HTTP Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'API request failed: ' . $errorMessage,
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Steadfast API order creation error: ' . $e->getMessage(), [
                'parcel_id' => $parcel->id,
                'parcel_id_field' => $parcel->parcel_id,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'API request failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Create bulk orders with Steadfast
     */
    public function createBulkOrders(array $parcels): array
    {
        try {
            $orders = [];
            
            foreach ($parcels as $parcel) {
                $orders[] = [
                    'invoice' => $parcel->parcel_id,
                    'recipient_name' => $parcel->receiver_name ?: $parcel->customer_name,
                    'recipient_phone' => $parcel->receiver_phone ?: $parcel->mobile_number,
                    'recipient_address' => $parcel->receiver_address ?: $parcel->delivery_address,
                    'cod_amount' => (float) $parcel->cod_amount,
                    'note' => $parcel->notes,
                    'item_description' => $parcel->description,
                    'delivery_type' => 0,
                ];
            }

            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json'
            ])->timeout(60)->post($this->baseUrl . '/create_order/bulk-order', [
                'data' => json_encode($orders)
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $results = [];
                foreach ($data as $result) {
                    if ($result['status'] === 'success') {
                        // Find and update the corresponding parcel
                        $parcel = Parcel::where('parcel_id', $result['invoice'])->first();
                        if ($parcel) {
                            $parcel->update([
                                'courier_tracking_number' => $result['tracking_code'],
                                'status' => 'pending'
                            ]);
                        }
                    }
                    
                    $results[] = [
                        'invoice' => $result['invoice'],
                        'success' => $result['status'] === 'success',
                        'tracking_code' => $result['tracking_code'] ?? null,
                        'consignment_id' => $result['consignment_id'] ?? null,
                        'message' => $result['status'] === 'success' ? 'Order created successfully' : 'Order creation failed'
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Bulk orders processed',
                    'data' => $results
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Bulk order API request failed: ' . $response->body(),
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('Steadfast API bulk order creation error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Bulk order API request failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get delivery status by tracking code
     */
    public function getStatusByTrackingCode(string $trackingCode): array
    {
        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json'
            ])->timeout(30)->get($this->baseUrl . '/status_by_trackingcode/' . $trackingCode);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'message' => 'Status retrieved successfully',
                    'data' => [
                        'status' => $data['delivery_status'],
                        'mapped_status' => $this->mapStatus($data['delivery_status'])
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to get status: ' . $response->body(),
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Status check failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get delivery status by invoice
     */
    public function getStatusByInvoice(string $invoice): array
    {
        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json'
            ])->timeout(30)->get($this->baseUrl . '/status_by_invoice/' . $invoice);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'message' => 'Status retrieved successfully',
                    'data' => [
                        'status' => $data['delivery_status'],
                        'mapped_status' => $this->mapStatus($data['delivery_status'])
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to get status: ' . $response->body(),
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Status check failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get current balance
     */
    public function getBalance(): array
    {
        // Skip API calls in localhost/development environment if configured
        if (config('courier.steadfast.mock_in_local') && app()->environment('local')) {
            return [
                'success' => true,
                'message' => 'Balance retrieved successfully (localhost mode)',
                'data' => [
                    'current_balance' => 0
                ]
            ];
        }
        
        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json'
            ])->timeout(config('courier.steadfast.timeout', 30))->get($this->baseUrl . '/get_balance');

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'message' => 'Balance retrieved successfully',
                    'data' => [
                        'current_balance' => $data['current_balance']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to get balance: ' . $response->body(),
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Balance check failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get balance value directly (for testing)
     */
    public function getBalanceValue(): float
    {
        $result = $this->getBalance();
        return $result['success'] ? $result['data']['current_balance'] : 0;
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json'
            ])->timeout(10)->get($this->baseUrl . '/get_balance');

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'message' => 'API connection successful',
                    'data' => [
                        'current_balance' => $data['current_balance'] ?? 0,
                        'response_time' => $response->transferStats->getHandlerStat('total_time')
                    ]
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

    /**
     * Map Steadfast status to system status
     */
    private function mapStatus(string $steadfastStatus): string
    {
        $statusMapping = [
            'pending' => 'pending',
            'in_review' => 'pending',
            'delivered' => 'delivered',
            'delivered_approval_pending' => 'delivered',
            'partial_delivered' => 'in_transit',
            'partial_delivered_approval_pending' => 'in_transit',
            'cancelled' => 'failed',
            'cancelled_approval_pending' => 'failed',
            'hold' => 'pending',
            'unknown' => 'pending'
        ];

        return $statusMapping[$steadfastStatus] ?? 'pending';
    }
}

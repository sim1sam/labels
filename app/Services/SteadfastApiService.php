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
        

        // Check if Steadfast API is enabled
        if (!config('courier.steadfast.enabled', true)) {
            \Log::warning('Steadfast API is disabled', [
                'parcel_id' => $parcel->parcel_id
            ]);
            
            return [
                'success' => false,
                'message' => 'Steadfast API is disabled',
                'data' => null
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
     * Get tracking status for a parcel
     */
    public function getTrackingStatus(string $trackingNumber): array
    {
        // Check if this is a mock/local tracking number
        if (strpos($trackingNumber, 'LOCAL-') === 0 || strpos($trackingNumber, 'TEST-') === 0) {
            // Mock tracking data for local development
            $mockStatuses = ['pending', 'picked_up', 'in_transit', 'delivered'];
            $randomStatus = $mockStatuses[array_rand($mockStatuses)];
            
            return [
                'success' => true,
                'data' => [
                    'tracking_code' => $trackingNumber,
                    'status' => $randomStatus,
                    'status_text' => ucfirst(str_replace('_', ' ', $randomStatus)),
                    'current_location' => 'Dhaka, Bangladesh',
                    'delivery_date' => $randomStatus === 'delivered' ? now()->format('Y-m-d H:i:s') : null,
                    'delivery_attempts' => $randomStatus === 'delivered' ? 1 : 0,
                    'delivery_notes' => $randomStatus === 'delivered' ? 'Delivered successfully' : null,
                    'tracking_history' => [
                        [
                            'status' => 'pending',
                            'status_text' => 'Pending',
                            'location' => 'Dhaka, Bangladesh',
                            'date' => now()->subDays(2)->format('Y-m-d H:i:s'),
                            'notes' => 'Parcel received'
                        ],
                        [
                            'status' => 'picked_up',
                            'status_text' => 'Picked Up',
                            'location' => 'Dhaka, Bangladesh',
                            'date' => now()->subDays(1)->format('Y-m-d H:i:s'),
                            'notes' => 'Parcel picked up from merchant'
                        ],
                        [
                            'status' => 'in_transit',
                            'status_text' => 'In Transit',
                            'location' => 'Dhaka, Bangladesh',
                            'date' => now()->subHours(12)->format('Y-m-d H:i:s'),
                            'notes' => 'Parcel in transit to destination'
                        ]
                    ]
                ],
                'message' => 'Mock tracking status retrieved successfully'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json'
            ])->timeout(30)->get($this->baseUrl . '/track', [
                'tracking_code' => $trackingNumber
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Tracking status retrieved successfully'
                ];
            } else {
                // Check if response is HTML (404 page)
                $contentType = $response->header('content-type');
                if (strpos($contentType, 'text/html') !== false) {
                    return [
                        'success' => false,
                        'data' => null,
                        'message' => 'Tracking not available for this parcel. The tracking number may not exist in Steadfast system.'
                    ];
                }
                
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to get tracking status: HTTP ' . $response->status()
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Steadfast tracking error: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'message' => 'Failed to get tracking status: ' . $e->getMessage()
            ];
        }
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

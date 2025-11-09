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
        
        // Log API configuration for debugging (without sensitive data)
        \Log::debug('SteadfastApiService initialized', [
            'base_url' => $this->baseUrl,
            'api_key_set' => !empty($this->apiKey),
            'secret_key_set' => !empty($this->secretKey),
            'environment' => app()->environment()
        ]);
    }

    /**
     * Build HTTP request with proper SSL verification settings
     */
    protected function buildHttpRequest()
    {
        $request = Http::withHeaders([
            'Api-Key' => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);

        // SSL verification settings:
        // 1. Check explicit config (STEADFAST_VERIFY_SSL in .env)
        // 2. In local environment, automatically disable to avoid certificate issues
        // 3. In production, verify SSL by default unless explicitly disabled
        $verifySsl = config('courier.steadfast.verify_ssl', true);
        $isLocal = app()->environment('local');
        $appEnv = app()->environment();
        
        // In local environment, automatically disable SSL verification to avoid certificate file issues
        // This fixes common errors like "cURL error 77: error setting certificate file"
        if ($isLocal) {
            $verifySsl = false;
        }
        
        // For production servers, if SSL verification fails, allow disabling it via config
        // Some servers may have SSL certificate issues with external APIs
        // IMPORTANT: On production servers, if you get SSL errors, set STEADFAST_VERIFY_SSL=false in .env
        if (!$verifySsl) {
            $request = $request->withoutVerifying();
            \Log::info('SSL verification disabled for Steadfast API request', [
                'environment' => $appEnv,
                'base_url' => $this->baseUrl,
                'reason' => $isLocal ? 'local environment (auto-disabled)' : 'explicitly disabled in config',
                'config_value' => config('courier.steadfast.verify_ssl')
            ]);
        } else {
            // Enable SSL verification for production
            \Log::info('SSL verification enabled for Steadfast API request', [
                'environment' => $appEnv,
                'base_url' => $this->baseUrl,
                'config_value' => config('courier.steadfast.verify_ssl')
            ]);
        }

        return $request;
    }

    /**
     * Create order with Steadfast
     */
    public function createOrder(Parcel $parcel): array
    {
        // Log environment and configuration for debugging
        $appEnv = app()->environment();
        $mockInLocal = config('courier.steadfast.mock_in_local', false);
        $apiEnabled = config('courier.steadfast.enabled', true);
        
        \Log::info('Steadfast createOrder called', [
            'parcel_id' => $parcel->parcel_id,
            'app_environment' => $appEnv,
            'mock_in_local' => $mockInLocal,
            'api_enabled' => $apiEnabled,
            'is_local_env' => $appEnv === 'local',
            'should_mock' => $mockInLocal && $appEnv === 'local'
        ]);
        
        // Skip API calls in localhost/development environment if configured
        // BUT only if explicitly set to mock AND environment is actually local
        if ($mockInLocal && $appEnv === 'local') {
            \Log::info('Skipping Steadfast API call in local environment (mock mode)', [
                'parcel_id' => $parcel->parcel_id,
                'environment' => $appEnv
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
        if (!$apiEnabled) {
            \Log::warning('Steadfast API is disabled in configuration', [
                'parcel_id' => $parcel->parcel_id,
                'config_value' => config('courier.steadfast.enabled')
            ]);
            
            return [
                'success' => false,
                'message' => 'Steadfast API is disabled in configuration. Check STEADFAST_API_ENABLED in .env file.',
                'data' => null
            ];
        }
        
        try {
            // Validate required fields
            $recipientName = $parcel->receiver_name ?: $parcel->customer_name;
            $recipientPhone = $parcel->receiver_phone ?: $parcel->mobile_number;
            $recipientAddress = $parcel->receiver_address ?: $parcel->delivery_address;
            
            // Clean and format phone number for Steadfast API
            // Remove spaces, parentheses, dashes, and other formatting characters
            $recipientPhone = preg_replace('/[\s\(\)\-\.]/', '', $recipientPhone);
            
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

            // Build HTTP request with SSL verification handling (must be done before logging)
            $httpRequest = $this->buildHttpRequest();
            $isLocal = app()->environment('local');
            $verifySsl = !$isLocal && config('courier.steadfast.verify_ssl', true);
            
            // Log the request data for debugging
            \Log::info('Steadfast API Request', [
                'url' => $this->baseUrl . '/create_order',
                'data' => $orderData,
                'api_key' => substr($this->apiKey, 0, 10) . '...',
                'verify_ssl' => $verifySsl,
                'environment' => app()->environment(),
                'ssl_disabled_reason' => $isLocal ? 'local environment' : null
            ]);

            // Make the API request with error handling
            try {
                $response = $httpRequest
                    ->timeout(config('courier.steadfast.timeout', 30))
                    ->post($this->baseUrl . '/create_order', $orderData);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // Network/connection errors
                \Log::error('Steadfast API Connection Error', [
                    'parcel_id' => $parcel->parcel_id,
                    'error' => $e->getMessage(),
                    'url' => $this->baseUrl . '/create_order',
                    'base_url' => $this->baseUrl
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to connect to Steadfast API. Please check your server network connection and SSL configuration. Error: ' . $e->getMessage(),
                    'data' => null
                ];
            } catch (\Exception $e) {
                // Other exceptions
                \Log::error('Steadfast API Request Exception', [
                    'parcel_id' => $parcel->parcel_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'API request failed: ' . $e->getMessage(),
                    'data' => null
                ];
            }
            
            // Log the response for debugging
            \Log::info('Steadfast API Response', [
                'parcel_id' => $parcel->parcel_id,
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500) // Limit log size
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if response is valid JSON
                if ($data === null) {
                    \Log::error('Steadfast API Invalid JSON Response', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => 'Invalid response from Steadfast API. Please check API configuration.',
                        'data' => null
                    ];
                }
                
                // Check if status is 200 (success)
                if (isset($data['status']) && $data['status'] == 200) {
                    // Validate required fields in response
                    if (!isset($data['consignment']) || !isset($data['consignment']['tracking_code'])) {
                        \Log::error('Steadfast API Missing Required Fields', [
                            'response' => $data
                        ]);
                        
                        return [
                            'success' => false,
                            'message' => 'Invalid response structure from Steadfast API. Missing tracking code.',
                            'data' => null
                        ];
                    }
                    
                    // Update parcel with Steadfast tracking information
                    $parcel->update([
                        'courier_tracking_number' => $data['consignment']['tracking_code'],
                        'status' => $this->mapStatus($data['consignment']['status'] ?? 'pending')
                    ]);

                    return [
                        'success' => true,
                        'message' => $data['message'] ?? 'Order created successfully',
                        'data' => [
                            'consignment_id' => $data['consignment']['consignment_id'] ?? null,
                            'tracking_code' => $data['consignment']['tracking_code'],
                            'status' => $data['consignment']['status'] ?? 'pending'
                        ]
                    ];
                } else {
                    // API returned error status
                    $statusCode = $data['status'] ?? 'unknown';
                    
                    // Extract error message - Steadfast returns errors in different formats
                    $errorMessage = 'Unknown error';
                    if (isset($data['message'])) {
                        $errorMessage = $data['message'];
                    } elseif (isset($data['error'])) {
                        $errorMessage = $data['error'];
                    } elseif (isset($data['errors']) && is_array($data['errors'])) {
                        // Steadfast returns validation errors in 'errors' array
                        $errorMessages = [];
                        foreach ($data['errors'] as $field => $messages) {
                            if (is_array($messages)) {
                                $errorMessages[] = ucfirst($field) . ': ' . implode(', ', $messages);
                            } else {
                                $errorMessages[] = ucfirst($field) . ': ' . $messages;
                            }
                        }
                        $errorMessage = implode(' | ', $errorMessages);
                    }
                    
                    \Log::error('Steadfast API Error Response', [
                        'status' => $statusCode,
                        'message' => $errorMessage,
                        'full_response' => $data
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => 'Steadfast API Error: ' . $errorMessage . ' (Status: ' . $statusCode . ')',
                        'data' => null
                    ];
                }
            } else {
                // HTTP request failed
                $statusCode = $response->status();
                $responseBody = $response->body();
                
                // Try to parse error message from response
                $errorMessage = 'HTTP ' . $statusCode;
                try {
                    $errorData = $response->json();
                    if (isset($errorData['message'])) {
                        $errorMessage .= ': ' . $errorData['message'];
                    } elseif (isset($errorData['error'])) {
                        $errorMessage .= ': ' . $errorData['error'];
                    } else {
                        $errorMessage .= ': ' . substr($responseBody, 0, 200);
                    }
                } catch (\Exception $e) {
                    $errorMessage .= ': ' . substr($responseBody, 0, 200);
                }
                
                \Log::error('Steadfast API HTTP Error', [
                    'status' => $statusCode,
                    'body' => $responseBody,
                    'headers' => $response->headers()
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

            $response = $this->buildHttpRequest()
                ->timeout(60)
                ->post($this->baseUrl . '/create_order/bulk-order', [
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
            $response = $this->buildHttpRequest()
                ->timeout(30)
                ->get($this->baseUrl . '/status_by_trackingcode/' . $trackingCode);

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
            $response = $this->buildHttpRequest()
                ->timeout(30)
                ->get($this->baseUrl . '/status_by_invoice/' . $invoice);

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
            $response = $this->buildHttpRequest()
                ->timeout(config('courier.steadfast.timeout', 30))
                ->get($this->baseUrl . '/get_balance');

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
            $response = $this->buildHttpRequest()
                ->timeout(30)
                ->get($this->baseUrl . '/track', [
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
            $response = $this->buildHttpRequest()
                ->timeout(10)
                ->get($this->baseUrl . '/get_balance');

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

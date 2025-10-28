<?php
// Simple Steadfast API Test - Standalone PHP file
// Access: https://wisedynamic.in/labels/test-steadfast.php

// Bootstrap Laravel
require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Steadfast API Test - Simple Version</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #007bff; }
        .status-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 20px; margin: 15px 0; }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        .warning { border-left: 4px solid #ffc107; }
        .info { border-left: 4px solid #17a2b8; }
        .btn { background: #007bff; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 10px; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        .json-output { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 15px; font-family: 'Courier New', monospace; font-size: 12px; white-space: pre-wrap; max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Steadfast API Test - Simple Version</h1>
            <p>Direct PHP test without complex routing</p>
        </div>

        <?php
        $action = $_GET['action'] ?? 'config';
        
        if ($action === 'config') {
            echo '<div class="status-card info"><h3>🔍 Checking Steadfast Configuration...</h3></div>';
            
            try {
                $courier = App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();
                
                if (!$courier) {
                    echo '<div class="status-card error">';
                    echo '<h3>❌ Steadfast Courier Not Found</h3>';
                    echo '<div class="json-output">Steadfast Courier not found in database. Please create it first.</div>';
                    echo '</div>';
                } else {
                    $apiService = new App\Services\SteadfastApiService($courier->api_key, $courier->api_secret);
                    $apiResult = $apiService->testConnection();
                    
                    echo '<div class="status-card success">';
                    echo '<h3>✅ Steadfast Configuration Found</h3>';
                    echo '<div class="json-output">';
                    echo json_encode([
                        'courier_info' => [
                            'id' => $courier->id,
                            'name' => $courier->courier_name,
                            'api_endpoint' => $courier->api_endpoint,
                            'has_api_key' => !empty($courier->api_key),
                            'has_api_secret' => !empty($courier->api_secret),
                            'status' => $courier->status
                        ],
                        'api_test' => $apiResult,
                        'environment' => [
                            'steadfast_enabled' => config('courier.steadfast.enabled'),
                            'mock_in_local' => config('courier.steadfast.mock_in_local'),
                            'base_url' => config('courier.steadfast.base_url'),
                            'timeout' => config('courier.steadfast.timeout')
                        ]
                    ], JSON_PRETTY_PRINT);
                    echo '</div>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="status-card error">';
                echo '<h3>❌ Configuration Error</h3>';
                echo '<div class="json-output">Exception: ' . $e->getMessage() . '</div>';
                echo '</div>';
            }
            
        } elseif ($action === 'run') {
            echo '<div class="status-card info"><h3>🚀 Running Test Parcel Creation...</h3></div>';
            
            try {
                $courier = App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();
                
                if (!$courier) {
                    echo '<div class="status-card error">';
                    echo '<h3>❌ Steadfast Courier Not Found</h3>';
                    echo '<div class="json-output">Steadfast Courier not found in database.</div>';
                    echo '</div>';
                } else {
                    $merchant = App\Models\Merchant::first();
                    if (!$merchant) {
                        echo '<div class="status-card error">';
                        echo '<h3>❌ No Merchant Found</h3>';
                        echo '<div class="json-output">No merchant found in database.</div>';
                        echo '</div>';
                    } else {
                        // Create test parcel
                        $testParcel = new App\Models\Parcel([
                            'parcel_id' => 'TEST-' . time(),
                            'customer_name' => 'Test Customer',
                            'mobile_number' => '01700000000',
                            'delivery_address' => 'Test Address, Dhaka 1205',
                            'cod_amount' => 100,
                            'merchant_id' => $merchant->id,
                            'courier_id' => $courier->id,
                            'status' => 'pending'
                        ]);
                        
                        $testParcel->save();
                        
                        // Test API service
                        $apiService = new App\Services\SteadfastApiService($courier->api_key, $courier->api_secret);
                        $apiResult = $apiService->createOrder($testParcel);
                        
                        echo '<div class="status-card success">';
                        echo '<h3>✅ Test Parcel Created</h3>';
                        echo '<div class="json-output">';
                        echo json_encode([
                            'id' => $testParcel->id,
                            'parcel_id' => $testParcel->parcel_id,
                            'customer_name' => $testParcel->customer_name,
                            'mobile_number' => $testParcel->mobile_number,
                            'delivery_address' => $testParcel->delivery_address,
                            'cod_amount' => $testParcel->cod_amount,
                            'merchant_id' => $testParcel->merchant_id,
                            'courier_id' => $testParcel->courier_id
                        ], JSON_PRETTY_PRINT);
                        echo '</div>';
                        echo '</div>';
                        
                        echo '<div class="status-card ' . ($apiResult['success'] ? 'success' : 'error') . '">';
                        echo '<h3>🔗 Steadfast API Response</h3>';
                        echo '<div class="json-output">';
                        echo json_encode($apiResult, JSON_PRETTY_PRINT);
                        echo '</div>';
                        echo '</div>';
                    }
                }
            } catch (Exception $e) {
                echo '<div class="status-card error">';
                echo '<h3>❌ Test Failed</h3>';
                echo '<div class="json-output">Exception: ' . $e->getMessage() . '</div>';
                echo '</div>';
            }
        }
        ?>

        <div style="text-align: center; margin: 20px 0;">
            <a href="?action=config" class="btn btn-warning">🔍 Check Configuration</a>
            <a href="?action=run" class="btn btn-success">🚀 Run Test Parcel Creation</a>
            <a href="?" class="btn">🔄 Refresh</a>
        </div>

        <div class="status-card info">
            <h3>📚 What This Test Does</h3>
            <ul>
                <li><strong>Configuration Check:</strong> Verifies Steadfast courier exists and API credentials are set</li>
                <li><strong>Test Parcel Creation:</strong> Creates a real parcel and attempts Steadfast API call</li>
                <li><strong>Shows Results:</strong> Displays exactly what happens and any error messages</li>
            </ul>
        </div>

        <div class="status-card info">
            <h3>🔗 Direct URLs</h3>
            <p>
                <a href="?action=config" class="btn">Check Configuration</a>
                <a href="?action=run" class="btn">Run Test</a>
            </p>
        </div>
    </div>
</body>
</html>

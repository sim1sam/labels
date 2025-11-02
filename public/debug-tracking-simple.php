<?php
// Simple tracking debug
// Access: https://wisedynamic.in/labels/debug-tracking-simple.php

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
    <title>Simple Tracking Debug</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 20px; margin: 15px 0; }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        .warning { border-left: 4px solid #ffc107; }
        .info { border-left: 4px solid #17a2b8; }
        .json-output { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 15px; font-family: 'Courier New', monospace; font-size: 12px; white-space: pre-wrap; max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Simple Tracking Debug</h1>
        
        <?php
        try {
            // Get the most recent parcel with Steadfast
            $parcel = App\Models\Parcel::with('merchant', 'courier')
                ->whereHas('courier', function($query) {
                    $query->where('courier_name', 'like', '%Steadfast%');
                })
                ->whereNotNull('courier_tracking_number')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$parcel) {
                echo '<div class="status-card warning">';
                echo '<h3>⚠️ No parcels with Steadfast tracking found</h3>';
                echo '<p>Create a parcel with Steadfast Courier first.</p>';
                echo '</div>';
            } else {
                echo '<div class="status-card info">';
                echo '<h3>📦 Testing Parcel: ' . $parcel->parcel_id . '</h3>';
                echo '<div class="json-output">';
                echo "Parcel ID: " . $parcel->parcel_id . "\n";
                echo "Customer: " . $parcel->customer_name . "\n";
                echo "Courier: " . $parcel->courier->courier_name . "\n";
                echo "Tracking Number: " . $parcel->courier_tracking_number . "\n";
                echo "Status: " . $parcel->status . "\n";
                echo '</div>';
                echo '</div>';
                
                // Test direct Steadfast API call
                echo '<div class="status-card info">';
                echo '<h3>🔗 Testing Direct Steadfast API Call</h3>';
                echo '</div>';
                
                $steadfastService = new App\Services\SteadfastApiService(
                    $parcel->courier->api_key,
                    $parcel->courier->api_secret
                );
                
                $result = $steadfastService->getTrackingStatus($parcel->courier_tracking_number);
                
                echo '<div class="status-card ' . ($result['success'] ? 'success' : 'error') . '">';
                echo '<h3>' . ($result['success'] ? '✅' : '❌') . ' Direct API Result</h3>';
                echo '<div class="json-output">';
                echo json_encode($result, JSON_PRETTY_PRINT);
                echo '</div>';
                echo '</div>';
                
                // Test CourierApiService
                echo '<div class="status-card info">';
                echo '<h3>🔗 Testing CourierApiService</h3>';
                echo '</div>';
                
                $courierApiService = new App\Services\CourierApiService();
                $courierResult = $courierApiService->getTrackingInfo($parcel);
                
                echo '<div class="status-card ' . ($courierResult['success'] ? 'success' : 'error') . '">';
                echo '<h3>' . ($courierResult['success'] ? '✅' : '❌') . ' CourierApiService Result</h3>';
                echo '<div class="json-output">';
                echo json_encode($courierResult, JSON_PRETTY_PRINT);
                echo '</div>';
                echo '</div>';
                
                // Test the actual API endpoint
                echo '<div class="status-card info">';
                echo '<h3>🔗 Testing API Endpoint</h3>';
                echo '</div>';
                
                $apiUrl = 'https://wisedynamic.in/labels/api/live-tracking/' . $parcel->id;
                echo '<div class="status-card info">';
                echo '<h3>🌐 API URL: ' . $apiUrl . '</h3>';
                echo '</div>';
                
                // Test with cURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Accept: application/json',
                    'Content-Type: application/json'
                ]);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);
                
                echo '<div class="status-card ' . ($httpCode === 200 ? 'success' : 'error') . '">';
                echo '<h3>' . ($httpCode === 200 ? '✅' : '❌') . ' API Endpoint Test (HTTP ' . $httpCode . ')</h3>';
                echo '<div class="json-output">';
                if ($error) {
                    echo "cURL Error: " . $error . "\n\n";
                }
                echo "Response: " . $response;
                echo '</div>';
                echo '</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="status-card error">';
            echo '<h3>❌ Error</h3>';
            echo '<div class="json-output">' . $e->getMessage() . "\n\n" . $e->getTraceAsString() . '</div>';
            echo '</div>';
        }
        ?>
        
        <div style="text-align: center; margin: 20px 0;">
            <a href="?" class="btn">🔄 Refresh</a>
        </div>
    </div>
</body>
</html>


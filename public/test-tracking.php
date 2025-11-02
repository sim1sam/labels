<?php
// Test tracking functionality
// Access: https://wisedynamic.in/labels/test-tracking.php

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
    <title>Tracking Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 20px; margin: 15px 0; }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        .warning { border-left: 4px solid #ffc107; }
        .info { border-left: 4px solid #17a2b8; }
        .btn { background: #007bff; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 10px; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .json-output { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 15px; font-family: 'Courier New', monospace; font-size: 12px; white-space: pre-wrap; max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Tracking Test</h1>
        
        <?php
        $action = $_GET['action'] ?? 'list';
        
        if ($action === 'list') {
            echo '<div class="status-card info"><h3>📋 Available Parcels with Steadfast Courier</h3></div>';
            
            try {
                $parcels = App\Models\Parcel::with('merchant', 'courier')
                    ->whereHas('courier', function($query) {
                        $query->where('courier_name', 'like', '%Steadfast%');
                    })
                    ->whereNotNull('courier_tracking_number')
                    ->get();
                
                if ($parcels->count() > 0) {
                    echo '<div class="status-card success">';
                    echo '<h3>✅ Found ' . $parcels->count() . ' parcels with Steadfast tracking</h3>';
                    echo '<div class="json-output">';
                    foreach ($parcels as $parcel) {
                        echo "Parcel ID: " . $parcel->parcel_id . "\n";
                        echo "Customer: " . $parcel->customer_name . "\n";
                        echo "Tracking: " . $parcel->courier_tracking_number . "\n";
                        echo "Status: " . $parcel->status . "\n";
                        echo "---\n";
                    }
                    echo '</div>';
                    echo '</div>';
                    
                    // Test tracking for first parcel
                    $testParcel = $parcels->first();
                    echo '<div style="text-align: center; margin: 20px 0;">';
                    echo '<a href="?action=test&id=' . $testParcel->id . '" class="btn">🔍 Test Tracking for ' . $testParcel->parcel_id . '</a>';
                    echo '</div>';
                } else {
                    echo '<div class="status-card warning">';
                    echo '<h3>⚠️ No parcels found with Steadfast tracking</h3>';
                    echo '<p>Create a parcel with Steadfast Courier first to test tracking.</p>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="status-card error">';
                echo '<h3>❌ Error</h3>';
                echo '<div class="json-output">' . $e->getMessage() . '</div>';
                echo '</div>';
            }
            
        } elseif ($action === 'test' && isset($_GET['id'])) {
            $parcelId = $_GET['id'];
            echo '<div class="status-card info"><h3>🔍 Testing Tracking for Parcel ID: ' . $parcelId . '</h3></div>';
            
            try {
                $parcel = App\Models\Parcel::with('merchant', 'courier')->find($parcelId);
                
                if (!$parcel) {
                    echo '<div class="status-card error">';
                    echo '<h3>❌ Parcel Not Found</h3>';
                    echo '<div class="json-output">Parcel with ID ' . $parcelId . ' not found</div>';
                    echo '</div>';
                } else {
                    echo '<div class="status-card info">';
                    echo '<h3>📦 Parcel Information</h3>';
                    echo '<div class="json-output">';
                    echo "Parcel ID: " . $parcel->parcel_id . "\n";
                    echo "Customer: " . $parcel->customer_name . "\n";
                    echo "Courier: " . ($parcel->courier ? $parcel->courier->courier_name : 'None') . "\n";
                    echo "Tracking Number: " . ($parcel->courier_tracking_number ?: 'None') . "\n";
                    echo "Status: " . $parcel->status . "\n";
                    echo '</div>';
                    echo '</div>';
                    
                    // Test tracking
                    if ($parcel->courier && $parcel->courier_tracking_number) {
                        echo '<div class="status-card info"><h3>🔗 Testing Steadfast Tracking...</h3></div>';
                        
                        $courierApiService = new App\Services\CourierApiService();
                        $result = $courierApiService->getTrackingInfo($parcel);
                        
                        echo '<div class="status-card ' . ($result['success'] ? 'success' : 'error') . '">';
                        echo '<h3>' . ($result['success'] ? '✅' : '❌') . ' Tracking Result</h3>';
                        echo '<div class="json-output">';
                        echo json_encode($result, JSON_PRETTY_PRINT);
                        echo '</div>';
                        echo '</div>';
                    } else {
                        echo '<div class="status-card warning">';
                        echo '<h3>⚠️ Cannot Test Tracking</h3>';
                        echo '<p>Parcel does not have a courier or tracking number assigned.</p>';
                        echo '</div>';
                    }
                }
            } catch (Exception $e) {
                echo '<div class="status-card error">';
                echo '<h3>❌ Test Failed</h3>';
                echo '<div class="json-output">' . $e->getMessage() . '</div>';
                echo '</div>';
            }
        }
        ?>
        
        <div style="text-align: center; margin: 20px 0;">
            <a href="?" class="btn">🔄 Refresh</a>
        </div>
    </div>
</body>
</html>

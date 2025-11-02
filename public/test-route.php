<?php
// Test route accessibility
// Access: https://wisedynamic.in/labels/test-route.php

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
    <title>Route Test</title>
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
        <h1>🔍 Route Test</h1>
        
        <?php
        try {
            // Get a parcel with Steadfast tracking
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
                echo '</div>';
            } else {
                echo '<div class="status-card info">';
                echo '<h3>📦 Testing with Parcel: ' . $parcel->parcel_id . ' (ID: ' . $parcel->id . ')</h3>';
                echo '<div class="json-output">';
                echo "Parcel ID: " . $parcel->parcel_id . "\n";
                echo "Database ID: " . $parcel->id . "\n";
                echo "Tracking Number: " . $parcel->courier_tracking_number . "\n";
                echo '</div>';
                echo '</div>';
                
                // Test different URL formats
                $urls = [
                    'https://wisedynamic.in/labels/api/live-tracking/' . $parcel->id,
                    'https://wisedynamic.in/api/live-tracking/' . $parcel->id,
                    'https://wisedynamic.in/labels/public/api/live-tracking/' . $parcel->id,
                ];
                
                foreach ($urls as $url) {
                    echo '<div class="status-card info">';
                    echo '<h3>🌐 Testing URL: ' . $url . '</h3>';
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Accept: application/json',
                        'Content-Type: application/json'
                    ]);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $error = curl_error($ch);
                    curl_close($ch);
                    
                    echo '<div class="status-card ' . ($httpCode === 200 ? 'success' : 'error') . '">';
                    echo '<h3>' . ($httpCode === 200 ? '✅' : '❌') . ' HTTP ' . $httpCode . '</h3>';
                    echo '<div class="json-output">';
                    if ($error) {
                        echo "cURL Error: " . $error . "\n\n";
                    }
                    echo "Response: " . $response;
                    echo '</div>';
                    echo '</div>';
                }
            }
            
        } catch (Exception $e) {
            echo '<div class="status-card error">';
            echo '<h3>❌ Error</h3>';
            echo '<div class="json-output">' . $e->getMessage() . '</div>';
            echo '</div>';
        }
        ?>
        
        <div style="text-align: center; margin: 20px 0;">
            <a href="?" class="btn">🔄 Refresh</a>
        </div>
    </div>
</body>
</html>



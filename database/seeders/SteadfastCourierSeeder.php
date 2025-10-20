<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Courier;
use App\Models\Merchant;

class SteadfastCourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Steadfast Courier
        $courier = Courier::updateOrCreate(
            ['courier_name' => 'Steadfast Courier'],
            [
                'phone' => '01700000000',
                'email' => 'info@steadfast.com',
                'vehicle_type' => 'van',
                'status' => 'active',
                'rating' => 4.5,
                'total_deliveries' => 0,
                'api_endpoint' => 'https://portal.packzy.com/api/v1',
                'api_key' => 'w21i6x8sjwygmg6rz2on4omniflrd5rb',
                'api_secret' => 'g84orvx9hiywjtm7wy3w4h5e',
                'has_tracking' => true,
                'tracking_url_template' => 'https://portal.packzy.com/track/{tracking_number}',
                'api_config' => [
                    'base_url' => 'https://portal.packzy.com/api/v1',
                    'timeout' => 30,
                    'retry_attempts' => 3,
                    'status_mapping' => [
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
                    ]
                ]
            ]
        );

        echo "Steadfast Courier created/updated with ID: " . $courier->id . "\n";

        // Find merchant "kolkata2 dhaka" (try different variations)
        $merchant = Merchant::where('shop_name', 'like', '%kolkata2%')
            ->orWhere('shop_name', 'like', '%dhaka%')
            ->orWhere('shop_name', 'like', '%kolkata%')
            ->first();

        if (!$merchant) {
            // Try to find by email or other fields
            $merchant = Merchant::where('email', 'like', '%kolkata%')
                ->orWhere('email', 'like', '%dhaka%')
                ->first();
        }

        if ($merchant) {
            // Check if courier is already assigned
            $existingAssignment = $merchant->couriers()->where('courier_id', $courier->id)->first();
            
            if (!$existingAssignment) {
                // Attach courier to merchant
                $merchant->couriers()->attach($courier->id, [
                    'merchant_custom_id' => 'STEADFAST001',
                    'status' => 'active',
                    'merchant_api_key' => 'w21i6x8sjwygmg6rz2on4omniflrd5rb',
                    'merchant_api_secret' => 'g84orvx9hiywjtm7wy3w4h5e',
                    'is_primary' => true
                ]);
                
                echo "Steadfast courier assigned to merchant: " . $merchant->shop_name . " (ID: " . $merchant->id . ")\n";
            } else {
                echo "Steadfast courier already assigned to merchant: " . $merchant->shop_name . "\n";
            }
        } else {
            echo "Merchant 'kolkata2 dhaka' not found. Available merchants:\n";
            Merchant::all(['id', 'shop_name', 'email'])->each(function($m) {
                echo "- ID: {$m->id}, Name: {$m->shop_name}, Email: {$m->email}\n";
            });
            
            echo "\nPlease run this seeder after creating the merchant, or manually assign the courier.\n";
        }
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Merchant;
use App\Models\Courier;
use App\Services\SteadfastApiService;

class TestMerchantApiCredentials extends Command
{
    protected $signature = 'test:merchant-api {merchant_id}';
    protected $description = 'Test merchant API credentials directly from merchant table';

    public function handle()
    {
        $merchantId = $this->argument('merchant_id');
        
        $merchant = Merchant::find($merchantId);
        if (!$merchant) {
            $this->error("Merchant with ID {$merchantId} not found.");
            return 1;
        }
        
        $this->info("🔍 Testing API credentials for: {$merchant->shop_name}");
        $this->line('');
        
        // Check merchant's direct API credentials
        $this->info("📋 Merchant Direct API Credentials:");
        $this->line("   API Key: " . ($merchant->api_key ? 'Set' : 'Not Set'));
        $this->line("   API Secret: " . ($merchant->api_secret ? 'Set' : 'Not Set'));
        $this->line('');
        
        // Find Steadfast courier
        $steadfastCourier = Courier::where('courier_name', 'Steadfast Courier')->first();
        if (!$steadfastCourier) {
            $this->error("Steadfast Courier not found.");
            return 1;
        }
        
        $this->info("📋 Steadfast Courier Default Credentials:");
        $this->line("   API Key: " . ($steadfastCourier->api_key ? 'Set' : 'Not Set'));
        $this->line("   API Secret: " . ($steadfastCourier->api_secret ? 'Set' : 'Not Set'));
        $this->line('');
        
        // Test the logic used in parcel creation
        $this->info("🔑 Testing Parcel Creation Logic:");
        $apiKey = $merchant->api_key ?: $steadfastCourier->api_key;
        $apiSecret = $merchant->api_secret ?: $steadfastCourier->api_secret;
        
        $this->line("   Final API Key: " . ($apiKey ? 'Set' : 'Not Set'));
        $this->line("   Final API Secret: " . ($apiSecret ? 'Set' : 'Not Set'));
        $this->line('');
        
        if (!empty($apiKey) && !empty($apiSecret)) {
            $this->info("✅ Testing API Connection:");
            $service = new SteadfastApiService($apiKey, $apiSecret);
            $result = $service->testConnection();
            
            if ($result['success']) {
                $this->info("   ✅ API Connection Successful!");
                $this->line("   Balance: " . ($result['data']['current_balance'] ?? 'N/A'));
                $this->line("   Response Time: " . ($result['data']['response_time'] ?? 'N/A') . 's');
            } else {
                $this->error("   ❌ API Connection Failed: " . $result['message']);
            }
        } else {
            $this->error("❌ No API credentials available for testing.");
        }
        
        return 0;
    }
}
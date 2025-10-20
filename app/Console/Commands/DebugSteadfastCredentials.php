<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Merchant;
use App\Models\Courier;
use App\Services\SteadfastApiService;

class DebugSteadfastCredentials extends Command
{
    protected $signature = 'steadfast:debug-credentials {merchant_id}';
    protected $description = 'Debug Steadfast API credentials for a merchant';

    public function handle()
    {
        $merchantId = $this->argument('merchant_id');
        
        $merchant = Merchant::find($merchantId);
        if (!$merchant) {
            $this->error("Merchant with ID {$merchantId} not found.");
            return 1;
        }
        
        $this->info("🔍 Debugging Steadfast credentials for: {$merchant->shop_name}");
        $this->line('');
        
        // Find Steadfast courier
        $steadfastCourier = Courier::where('courier_name', 'Steadfast Courier')->first();
        if (!$steadfastCourier) {
            $this->error("Steadfast Courier not found.");
            return 1;
        }
        
        $this->info("📋 Steadfast Courier Details:");
        $this->line("   ID: {$steadfastCourier->id}");
        $this->line("   Name: {$steadfastCourier->courier_name}");
        $this->line("   API Endpoint: {$steadfastCourier->api_endpoint}");
        $this->line("   Has API Integration: " . ($steadfastCourier->hasApiIntegration() ? 'Yes' : 'No'));
        $this->line("   Supports Tracking: " . ($steadfastCourier->supportsTracking() ? 'Yes' : 'No'));
        $this->line("   Default API Key: " . ($steadfastCourier->api_key ? 'Set' : 'Not Set'));
        $this->line("   Default API Secret: " . ($steadfastCourier->api_secret ? 'Set' : 'Not Set'));
        $this->line('');
        
        // Check merchant-courier relationship
        $merchantCourier = $merchant->couriers()->where('courier_id', $steadfastCourier->id)->first();
        if (!$merchantCourier) {
            $this->error("❌ Merchant is not assigned to Steadfast Courier.");
            return 1;
        }
        
        $this->info("🔗 Merchant-Courier Relationship:");
        $this->line("   Merchant ID: {$merchant->id}");
        $this->line("   Courier ID: {$steadfastCourier->id}");
        $this->line("   Custom ID: {$merchantCourier->pivot->merchant_custom_id}");
        $this->line("   Status: {$merchantCourier->pivot->status}");
        $this->line("   Is Primary: " . ($merchantCourier->pivot->is_primary ? 'Yes' : 'No'));
        $this->line("   Merchant API Key: " . ($merchantCourier->pivot->merchant_api_key ? 'Set' : 'Not Set'));
        $this->line("   Merchant API Secret: " . ($merchantCourier->pivot->merchant_api_secret ? 'Set' : 'Not Set'));
        $this->line('');
        
        // Test credential retrieval
        $this->info("🔑 Testing Credential Retrieval:");
        $credentials = $steadfastCourier->getMerchantApiCredentials($merchant->id);
        $this->line("   Retrieved API Key: " . ($credentials['api_key'] ? 'Set' : 'Not Set'));
        $this->line("   Retrieved API Secret: " . ($credentials['api_secret'] ? 'Set' : 'Not Set'));
        $this->line('');
        
        if (!empty($credentials['api_key']) && !empty($credentials['api_secret'])) {
            $this->info("✅ Testing API Connection:");
            $service = new SteadfastApiService($credentials['api_key'], $credentials['api_secret']);
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
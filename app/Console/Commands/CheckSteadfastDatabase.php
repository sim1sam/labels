<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Courier;
use Illuminate\Support\Facades\DB;

class CheckSteadfastDatabase extends Command
{
    protected $signature = 'steadfast:check-db';
    protected $description = 'Check Steadfast courier data in database';

    public function handle()
    {
        $this->info('🔍 Checking Steadfast Courier Database Records:');
        $this->line('');
        
        // Check courier table
        $courier = Courier::where('courier_name', 'Steadfast Courier')->first();
        if ($courier) {
            $this->info('📋 Steadfast Courier Record:');
            $this->line("   ID: {$courier->id}");
            $this->line("   Name: {$courier->courier_name}");
            $this->line("   API Endpoint: " . ($courier->api_endpoint ?: 'NOT SET'));
            $this->line("   API Key: " . ($courier->api_key ?: 'NOT SET'));
            $this->line("   API Secret: " . ($courier->api_secret ?: 'NOT SET'));
            $this->line("   Has Tracking: " . ($courier->has_tracking ? 'Yes' : 'No'));
            $this->line("   Tracking URL: " . ($courier->tracking_url_template ?: 'NOT SET'));
            $this->line('');
        } else {
            $this->error('❌ Steadfast Courier not found in database!');
            return 1;
        }
        
        // Check merchant_courier table
        $this->info('🔗 Merchant-Courier Assignments:');
        $assignments = DB::table('merchant_courier')
            ->join('merchants', 'merchant_courier.merchant_id', '=', 'merchants.id')
            ->where('merchant_courier.courier_id', $courier->id)
            ->select('merchants.shop_name', 'merchant_courier.*')
            ->get();
            
        if ($assignments->count() > 0) {
            foreach ($assignments as $assignment) {
                $this->line("   Merchant: {$assignment->shop_name}");
                $this->line("   Custom ID: {$assignment->merchant_custom_id}");
                $this->line("   Status: {$assignment->status}");
                $this->line("   Is Primary: " . ($assignment->is_primary ? 'Yes' : 'No'));
                $this->line("   Merchant API Key: " . ($assignment->merchant_api_key ?: 'NOT SET'));
                $this->line("   Merchant API Secret: " . ($assignment->merchant_api_secret ?: 'NOT SET'));
                $this->line('');
            }
        } else {
            $this->warn('⚠️  No merchant assignments found for Steadfast Courier.');
        }
        
        // Check if API fields exist in couriers table
        $this->info('🗄️  Database Schema Check:');
        $columns = DB::select("SHOW COLUMNS FROM couriers LIKE 'api_%'");
        $this->line("   API-related columns in couriers table:");
        foreach ($columns as $column) {
            $this->line("   - {$column->Field} ({$column->Type})");
        }
        
        $pivotColumns = DB::select("SHOW COLUMNS FROM merchant_courier LIKE 'merchant_api_%'");
        $this->line("   Merchant API-related columns in merchant_courier table:");
        foreach ($pivotColumns as $column) {
            $this->line("   - {$column->Field} ({$column->Type})");
        }
        
        return 0;
    }
}
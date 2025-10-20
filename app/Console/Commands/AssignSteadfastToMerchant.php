<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Merchant;
use App\Models\Courier;
use Illuminate\Support\Facades\DB;

class AssignSteadfastToMerchant extends Command
{
    protected $signature = 'steadfast:assign-merchant {merchant_id} {custom_id}';
    protected $description = 'Assign Steadfast courier to merchant with custom ID';

    public function handle()
    {
        $merchantId = $this->argument('merchant_id');
        $customId = $this->argument('custom_id');
        
        // Find merchant
        $merchant = Merchant::find($merchantId);
        if (!$merchant) {
            $this->error("Merchant with ID {$merchantId} not found.");
            return 1;
        }
        
        // Find Steadfast courier
        $steadfastCourier = Courier::where('courier_name', 'Steadfast Courier')->first();
        if (!$steadfastCourier) {
            $this->error("Steadfast Courier not found. Please run the seeder first.");
            return 1;
        }
        
        $this->info("Assigning Steadfast Courier to merchant: {$merchant->shop_name}");
        
        // Check if already assigned
        $existingAssignment = DB::table('merchant_courier')
            ->where('merchant_id', $merchantId)
            ->where('courier_id', $steadfastCourier->id)
            ->first();
            
        if ($existingAssignment) {
            // Update existing assignment
            DB::table('merchant_courier')
                ->where('merchant_id', $merchantId)
                ->where('courier_id', $steadfastCourier->id)
                ->update([
                    'merchant_custom_id' => $customId,
                    'status' => 'active',
                    'merchant_api_key' => 'w21i6x8sjwygmg6rz2on4omniflrd5rb',
                    'merchant_api_secret' => 'g84orvx9hiywjtm7wy3w4h5e',
                    'is_primary' => true,
                    'updated_at' => now()
                ]);
                
            $this->info("✅ Updated existing assignment with custom ID: {$customId}");
        } else {
            // Create new assignment
            DB::table('merchant_courier')->insert([
                'merchant_id' => $merchantId,
                'courier_id' => $steadfastCourier->id,
                'merchant_custom_id' => $customId,
                'status' => 'active',
                'merchant_api_key' => 'w21i6x8sjwygmg6rz2on4omniflrd5rb',
                'merchant_api_secret' => 'g84orvx9hiywjtm7wy3w4h5e',
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->info("✅ Created new assignment with custom ID: {$customId}");
        }
        
        // Show current assignments
        $this->line("\nCurrent assignments for this merchant:");
        $assignments = DB::table('merchant_courier')
            ->join('couriers', 'merchant_courier.courier_id', '=', 'couriers.id')
            ->where('merchant_courier.merchant_id', $merchantId)
            ->select('couriers.courier_name', 'merchant_courier.merchant_custom_id', 'merchant_courier.status', 'merchant_courier.is_primary')
            ->get();
            
        foreach ($assignments as $assignment) {
            $primary = $assignment->is_primary ? ' (PRIMARY)' : '';
            $this->line("- {$assignment->courier_name}: {$assignment->merchant_custom_id} [{$assignment->status}]{$primary}");
        }
        
        return 0;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowMerchantCourierSetup extends Command
{
    protected $signature = 'steadfast:show-setup';
    protected $description = 'Show current merchant-courier setup with custom IDs';

    public function handle()
    {
        $this->info('📋 Current Merchant-Courier Setup:');
        $this->line('');
        
        $assignments = DB::table('merchant_courier')
            ->join('merchants', 'merchant_courier.merchant_id', '=', 'merchants.id')
            ->join('couriers', 'merchant_courier.courier_id', '=', 'couriers.id')
            ->select(
                'merchants.shop_name',
                'merchants.email',
                'couriers.courier_name',
                'merchant_courier.merchant_custom_id',
                'merchant_courier.status',
                'merchant_courier.is_primary'
            )
            ->get();
            
        if ($assignments->count() > 0) {
            $this->table(
                ['Merchant', 'Email', 'Courier', 'Custom ID', 'Status', 'Primary'],
                $assignments->map(function($a) {
                    return [
                        $a->shop_name,
                        $a->email,
                        $a->courier_name,
                        $a->merchant_custom_id,
                        $a->status,
                        $a->is_primary ? 'Yes' : 'No'
                    ];
                })
            );
            
            $this->line('');
            $this->info('💡 Custom ID Explanation:');
            $this->line('   • Custom ID = Merchant\'s unique identifier in Steadfast system');
            $this->line('   • When creating orders, Steadfast uses this ID to identify the merchant');
            $this->line('   • Each merchant has their own unique Custom ID');
            $this->line('   • This ID is sent to Steadfast API with every order');
            
            $this->line('');
            $this->info('🔧 How It Works:');
            $this->line('   1. Merchant creates parcel in your system');
            $this->line('   2. System uses merchant\'s Custom ID');
            $this->line('   3. Order sent to Steadfast with Custom ID');
            $this->line('   4. Steadfast knows which merchant placed the order');
            
        } else {
            $this->warn('No merchant-courier assignments found.');
        }
        
        return 0;
    }
}
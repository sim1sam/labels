<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantCourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchant = \App\Models\Merchant::first();
        $couriers = \App\Models\Courier::take(2)->get();
        
        if ($merchant && $couriers->count() > 0) {
            foreach ($couriers as $index => $courier) {
                $merchant->couriers()->attach($courier->id, [
                    'merchant_custom_id' => 'CUST' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'status' => 'active'
                ]);
            }
            echo "Assigned " . $couriers->count() . " couriers to merchant " . $merchant->shop_name . "\n";
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParcelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchant = \App\Models\Merchant::first();
        
        if ($merchant) {
            \App\Models\Parcel::create([
                'parcel_id' => \App\Models\Parcel::generateParcelId(),
                'merchant_id' => $merchant->id,
                'customer_name' => 'John Doe',
                'mobile_number' => '+1234567890',
                'delivery_address' => '123 Main Street, City, State 12345',
                'cod_amount' => 25.50,
                'status' => 'pending'
            ]);
            
            \App\Models\Parcel::create([
                'parcel_id' => \App\Models\Parcel::generateParcelId(),
                'merchant_id' => $merchant->id,
                'customer_name' => 'Jane Smith',
                'mobile_number' => '+0987654321',
                'delivery_address' => '456 Oak Avenue, Town, State 67890',
                'cod_amount' => 45.75,
                'status' => 'assigned'
            ]);
            
            \App\Models\Parcel::create([
                'parcel_id' => \App\Models\Parcel::generateParcelId(),
                'merchant_id' => $merchant->id,
                'customer_name' => 'Bob Johnson',
                'mobile_number' => '+1122334455',
                'delivery_address' => '789 Pine Road, Village, State 54321',
                'cod_amount' => 12.00,
                'status' => 'delivered'
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Courier;
use Illuminate\Support\Facades\Hash;

class SampleMerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample merchant user
        $merchantUser = User::create([
            'name' => 'Kolkata 2 Dhaka',
            'email' => 'hello@kolkata2dhaka.com',
            'password' => Hash::make('password'),
            'user_type' => User::TYPE_MERCHANT,
            'email_verified_at' => now(),
        ]);

        // Create merchant
        $merchant = Merchant::create([
            'merchant_id' => Merchant::generateMerchantId(),
            'shop_name' => 'Kolkata 2 Dhaka',
            'email' => 'hello@kolkata2dhaka.com',
            'phone' => '01700000000',
            'address' => 'Dhaka, Bangladesh',
            'user_id' => $merchantUser->id,
        ]);

        // Find Steadfast courier
        $steadfastCourier = Courier::where('courier_name', 'Steadfast Courier')->first();
        
        if ($steadfastCourier) {
            // Attach Steadfast courier to merchant
            $merchant->couriers()->attach($steadfastCourier->id, [
                'merchant_custom_id' => '96991',
                'status' => 'active',
                'merchant_api_key' => 'w21i6x8sjwygmg6rz2on4omniflrd5rb',
                'merchant_api_secret' => 'g84orvx9hiywjtm7wy3w4h5e',
                'is_primary' => true
            ]);
            
            echo "✅ Sample merchant created with Steadfast integration!\n";
        } else {
            echo "⚠️  Steadfast courier not found. Please run SteadfastCourierSeeder first.\n";
        }

        echo "Merchant Details:\n";
        echo "Name: Kolkata 2 Dhaka\n";
        echo "Email: hello@kolkata2dhaka.com\n";
        echo "Password: password\n";
        echo "Merchant ID: " . $merchant->id . "\n";
        echo "Custom ID: 96991\n";
    }
}
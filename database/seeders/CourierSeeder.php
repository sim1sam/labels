<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $couriers = [
            [
                'courier_name' => 'Ahmed Hassan',
                'phone' => '+1234567890',
                'email' => 'ahmed@example.com',
                'vehicle_type' => 'motorcycle',
                'status' => 'active',
                'rating' => 4.5,
                'total_deliveries' => 150,
            ],
            [
                'courier_name' => 'Mohammed Ali',
                'phone' => '+1234567891',
                'email' => 'mohammed@example.com',
                'vehicle_type' => 'van',
                'status' => 'active',
                'rating' => 4.8,
                'total_deliveries' => 200,
            ],
            [
                'courier_name' => 'Omar Khalil',
                'phone' => '+1234567892',
                'email' => 'omar@example.com',
                'vehicle_type' => 'bike',
                'status' => 'active',
                'rating' => 4.2,
                'total_deliveries' => 100,
            ],
            [
                'courier_name' => 'Youssef Ibrahim',
                'phone' => '+1234567893',
                'email' => 'youssef@example.com',
                'vehicle_type' => 'motorcycle',
                'status' => 'active',
                'rating' => 4.7,
                'total_deliveries' => 180,
            ],
            [
                'courier_name' => 'Hassan Mahmoud',
                'phone' => '+1234567894',
                'email' => 'hassan@example.com',
                'vehicle_type' => 'van',
                'status' => 'active',
                'rating' => 4.3,
                'total_deliveries' => 120,
            ],
        ];

        foreach ($couriers as $courier) {
            \App\Models\Courier::create($courier);
        }
    }
}

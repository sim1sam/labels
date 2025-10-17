<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => User::TYPE_ADMIN,
            'email_verified_at' => now(),
        ]);

        // Create merchant user
        User::create([
            'name' => 'Merchant User',
            'email' => 'merchant@example.com',
            'password' => Hash::make('password'),
            'user_type' => User::TYPE_MERCHANT,
            'email_verified_at' => now(),
        ]);
    }
}

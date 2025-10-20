<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('merchant_courier', function (Blueprint $table) {
            $table->string('merchant_api_key')->nullable()->after('merchant_custom_id');
            $table->string('merchant_api_secret')->nullable()->after('merchant_api_key');
            $table->json('merchant_api_config')->nullable()->after('merchant_api_secret');
            $table->boolean('is_primary')->default(false)->after('merchant_api_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchant_courier', function (Blueprint $table) {
            $table->dropColumn([
                'merchant_api_key',
                'merchant_api_secret', 
                'merchant_api_config',
                'is_primary'
            ]);
        });
    }
};
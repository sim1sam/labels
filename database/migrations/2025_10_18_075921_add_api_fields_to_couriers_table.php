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
        Schema::table('couriers', function (Blueprint $table) {
            $table->string('api_endpoint')->nullable()->after('total_deliveries');
            $table->string('api_key')->nullable()->after('api_endpoint');
            $table->string('api_secret')->nullable()->after('api_key');
            $table->json('api_config')->nullable()->after('api_secret'); // For additional API configuration
            $table->boolean('has_tracking')->default(false)->after('api_config');
            $table->string('tracking_url_template')->nullable()->after('has_tracking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->dropColumn([
                'api_endpoint',
                'api_key', 
                'api_secret',
                'api_config',
                'has_tracking',
                'tracking_url_template'
            ]);
        });
    }
};
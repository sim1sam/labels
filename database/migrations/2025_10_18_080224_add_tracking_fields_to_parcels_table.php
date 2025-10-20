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
        Schema::table('parcels', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('parcel_id');
            $table->string('courier_tracking_number')->nullable()->after('tracking_number');
            $table->json('tracking_history')->nullable()->after('courier_tracking_number');
            $table->timestamp('last_tracking_update')->nullable()->after('tracking_history');
            $table->string('sender_name')->nullable()->after('customer_name');
            $table->string('sender_phone')->nullable()->after('sender_name');
            $table->string('sender_address')->nullable()->after('sender_phone');
            $table->string('receiver_name')->nullable()->after('mobile_number');
            $table->string('receiver_phone')->nullable()->after('receiver_name');
            $table->string('receiver_address')->nullable()->after('receiver_phone');
            $table->decimal('weight', 8, 2)->nullable()->after('cod_amount');
            $table->text('description')->nullable()->after('weight');
            $table->decimal('declared_value', 10, 2)->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_number',
                'courier_tracking_number',
                'tracking_history',
                'last_tracking_update',
                'sender_name',
                'sender_phone',
                'sender_address',
                'receiver_name',
                'receiver_phone',
                'receiver_address',
                'weight',
                'description',
                'declared_value'
            ]);
        });
    }
};
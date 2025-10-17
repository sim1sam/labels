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
        Schema::create('merchant_courier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
            $table->foreignId('courier_id')->constrained()->onDelete('cascade');
            $table->string('merchant_custom_id'); // Custom merchant ID for this relationship
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            // Ensure unique combination of merchant and courier
            $table->unique(['merchant_id', 'courier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_courier');
    }
};

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
    Schema::create('order_db', function (Blueprint $table) {
        $table->id();
        $table->string('order_slip')->unique(); // e.g., 03092025-01
        $table->foreignId('client_id')->constrained('client_db')->onDelete('cascade');
        $table->enum('price_code', ['unit', 'pickup', 'spu']);
        $table->decimal('discount', 10, 2)->default(0);
        $table->enum('discount_type', ['fixed', 'percent'])->default('fixed'); // ✅ added
        $table->string('purchase_order')->nullable();
        $table->string('wwrs')->nullable();
        $table->string('truck')->nullable();
        $table->text('details')->nullable();
        $table->text('delivery_details')->nullable();
        $table->decimal('total', 12, 2)->default(0);
        $table->boolean('locked')->default(false);
        $table->boolean('paid')->default(false);
        $table->enum('payment_status', ['Unpaid', 'Partial', 'Paid','Cancel']);
        $table->timestamps();
    });

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('order_db');
    }
};

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
        Schema::create('product_db', function (Blueprint $table) {
            $table->id();
            $table->string('product_type');
            $table->string('product_name');
            $table->integer('price');
            $table->integer('pickup');
            $table->integer('spu');
            $table->integer('available');
            $table->integer('sold')->nullable();
            $table->integer('returned')->nullable();
            $table->longText('history')->nullable(); 
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_db');
    }
};

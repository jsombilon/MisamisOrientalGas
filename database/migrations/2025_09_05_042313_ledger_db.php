<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ledger_db', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('client_db')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('order_db')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('payments_db')->onDelete('cascade');

            $table->date('entry_date')->default(DB::raw('CURRENT_DATE'));
            $table->enum('entry_type', ['Order', 'Payment']); 
             $table->enum('category', ['Content', 'Sold'])->nullable(); 

            $table->decimal('debit', 12, 2)->default(0); 
            $table->decimal('credit', 12, 2)->default(0);  
            $table->decimal('balance', 12, 2)->default(0);
            $table->text('remarks')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

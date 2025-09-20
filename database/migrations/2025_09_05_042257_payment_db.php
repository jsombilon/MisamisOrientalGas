<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments_db', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('order_db')
                ->nullOnDelete(); // link to order
            $table->foreignId('client_id')->constrained('client_db')->onDelete('cascade'); // redundancy for quick lookup
            
            $table->decimal('amount_paid', 12, 2);       // payment amount
            $table->enum('payment_type', ['Cash', 'Charge', 'On Date Check', 'Post Date Check']); 
            $table->date('payment_date')->default(DB::raw('CURRENT_DATE'));  // when payment is made
            $table->string('reference_no')->nullable();  // check #, OR number, etc.
            $table->text('remarks')->nullable();
            $table->date('check_date')->nullable(); // for post dated checks
            $table->enum('check_status', ['Unpaid', 'Paid', 'Bounced'])->nullable();
            $table->enum('payment_status', ['Unpaid', 'Partial', 'Paid','Cancel','Installment','Cleared'])->default('Unpaid');
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

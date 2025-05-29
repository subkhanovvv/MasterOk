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
        Schema::create('product_activities', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['consume', 'loan', 'return', 'intake', 'intake_loan', 'intake_return']);

            // LOAN INFO
            $table->enum('loan_direction', ['given', 'taken'])->nullable(); // 'given' = you gave product to client; 'taken' = you took from individual
            $table->string('client_name')->nullable();      // For loan to/from individuals
            $table->string('client_phone')->nullable();     // Optional contact info
            $table->enum('status', ['complete', 'incomplete'])->nullable();
            $table->decimal('loan_amount', 12, 2)->nullable();
            $table->date('loan_due_to')->nullable(); // Remaining to be paid

            // PAYMENT
            $table->enum('payment_type', ['cash', 'card','bank_transfer'])->default('cash');
            $table->decimal('total_price', 12, 2)->default(0);
            // OTHER
            $table->string('return_reason')->nullable();
            $table->string('qr_code')->nullable();
            $table->text('note')->nullable();

            // RELATION
            $table->unsignedBigInteger('supplier_id')->nullable(); 
            $table->unsignedBigInteger('brand_id')->nullable();
            // only used for intake
            $table->timestamps();

            // Indexes
            $table->index('type');
            $table->index('client_phone');
            $table->index('client_name');
            $table->index('qr_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_activities');
    }
};

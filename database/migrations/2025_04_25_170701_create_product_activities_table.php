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
            $table->string('client_phone')->nullable();
            $table->string('client_name')->nullable();
            $table->enum('payment_type', ['cash', 'card'])->default('cash');
            $table->decimal('paid_amount', 12, 2)->nullable();
            $table->decimal('total_price', 12, 2)->default(0);
            $table->string('return_reason')->nullable();
            $table->string('qr_code')->nullable();
            $table->enum('loan_status', ['complete', 'incomplete'])->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('client_phone');
            $table->integer('supplier_id')->nullable();
            $table->index('type');
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

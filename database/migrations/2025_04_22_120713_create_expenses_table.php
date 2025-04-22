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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('qty');
            $table->bigInteger('cost_uzs');
            $table->decimal('usd_exchange_rate', 10, 2)->default(0);

            $table->enum('payment_status', ['loan', 'partly_paid', 'paid'])->default('loan');
            $table->bigInteger('total_cost');

            $table->enum('type', ['sold', 'bought', 'returned'])->default('bought');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

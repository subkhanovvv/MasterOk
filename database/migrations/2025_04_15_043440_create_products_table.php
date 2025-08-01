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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('qty')->default(0);
            $table->string('photo')->nullable();
            $table->string('unit')->default('шт');
            $table->decimal('price_uzs', 15, 2)->nullable();
            $table->string('short_description')->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->enum('status', ['normal', 'low', 'out_of_stock'])->default('out_of_stock');
            $table->foreignId('category_id')->nullable();
            $table->foreignId('brand_id')->nullable();
            $table->string('barcode')->nullable();
            $table->string('barcode_value')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

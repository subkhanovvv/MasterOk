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
            $table->string('unit');
            $table->decimal('price_uzs', 15, 2);
            $table->decimal('price_usd', 15, 2);
            $table->string('short_description')->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->enum('status', ['normal', 'low', 'out_of_stock'])->default('out_of_stock');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->decimal('tax')->default(0);
            $table->foreignId('brand_id')->constrained()->onDelete('restrict');
            $table->string('barcode')->nullable();
            $table->integer('barcode_value')->nullable();
            $table->timestamps();
            $table->index('status');
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

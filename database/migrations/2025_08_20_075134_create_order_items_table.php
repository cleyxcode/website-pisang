<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // Product info saat order (snapshot)
            $table->string('product_name');
            $table->string('product_sku');
            $table->decimal('product_price', 12, 2); // Harga saat order
            $table->string('product_image')->nullable(); // Gambar utama saat order
            
            // Quantity & total
            $table->integer('quantity');
            $table->decimal('total_price', 12, 2); // quantity * product_price
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
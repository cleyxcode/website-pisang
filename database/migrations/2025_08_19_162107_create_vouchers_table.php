<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode voucher (contoh: DISKON10)
            $table->string('name'); // Nama voucher untuk admin
            $table->text('description')->nullable();
            
            // Tipe diskon
            $table->enum('discount_type', ['percentage', 'fixed', 'free_shipping']); 
            $table->decimal('discount_value', 12, 2)->nullable(); // Nilai diskon (% atau nominal)
            
            // Batasan penggunaan
            $table->decimal('minimum_amount', 12, 2)->nullable(); // Minimum belanja
            $table->decimal('maximum_discount', 12, 2)->nullable(); // Max discount untuk percentage
            $table->integer('usage_limit')->nullable(); // Limit total penggunaan
            $table->integer('usage_limit_per_user')->default(1); // Limit per user
            $table->integer('used_count')->default(0); // Jumlah sudah digunakan
            
            // Waktu berlaku
            $table->datetime('starts_at')->nullable(); // Mulai berlaku
            $table->datetime('expires_at')->nullable(); // Berakhir
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
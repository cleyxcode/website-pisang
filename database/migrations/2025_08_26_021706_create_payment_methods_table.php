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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama metode pembayaran (BCA, Dana, etc)
            $table->enum('type', ['bank', 'ewallet']); // Jenis pembayaran
            $table->string('account_number'); // Nomor rekening atau nomor HP
            $table->string('account_name'); // Nama pemilik rekening/akun
            $table->string('icon')->nullable(); // Path icon/logo
            $table->text('instructions')->nullable(); // Instruksi pembayaran
            $table->boolean('is_active')->default(true); // Status aktif
            $table->integer('sort_order')->default(0); // Urutan tampil
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
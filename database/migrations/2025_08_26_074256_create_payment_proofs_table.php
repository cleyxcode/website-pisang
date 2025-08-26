<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->decimal('transfer_amount', 15, 2);
            $table->datetime('transfer_date');
            $table->string('sender_name');
            $table->string('sender_account')->nullable();
            $table->string('proof_image'); // Path ke gambar bukti transfer
            $table->text('notes')->nullable(); // Catatan dari customer
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->datetime('verified_at')->nullable();
            $table->string('verified_by')->nullable(); // Admin yang verifikasi
            $table->text('admin_notes')->nullable(); // Catatan admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
    }
};



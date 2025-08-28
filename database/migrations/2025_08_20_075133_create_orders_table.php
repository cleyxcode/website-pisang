<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // ORD-20241201-001
            
            // Customer info
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('customer_address');
            
            // Order details
            $table->decimal('subtotal', 12, 2); // Total sebelum diskon & ongkir
            $table->decimal('discount_amount', 12, 2)->default(0); // Dari voucher
            $table->decimal('shipping_cost', 12, 2)->default(0); // Ongkos kirim
            $table->decimal('total_amount', 12, 2); // Final total
            
            // Voucher info
            $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('voucher_code')->nullable();
            $table->decimal('voucher_discount', 12, 2)->default(0);
            
            // Payment info
            $table->enum('payment_method', ['midtrans', 'manual']);
            $table->foreignId('payment_method_id')->nullable()->constrained()->onDelete('set null'); // HAPUS ->after()
            $table->boolean('has_payment_proof')->default(false); // HAPUS ->after()
            
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'expired'])
                  ->default('pending');
            
            // Midtrans integration
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->enum('midtrans_status', ['pending', 'settlement', 'expire', 'cancel', 'deny'])->nullable();
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable(); // Catatan customer
            $table->text('admin_notes')->nullable(); // Catatan admin
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
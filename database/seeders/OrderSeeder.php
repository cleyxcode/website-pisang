<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Voucher;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    // public function run(): void
    // {
    //     $products = Product::all();
    //     $vouchers = Voucher::all();

    //     if ($products->isEmpty()) {
    //         $this->command->warn('Tidak ada produk! Buat produk dulu sebelum menjalankan OrderSeeder.');
    //         return;
    //     }

    //     // Generate orders for last 7 days
    //     for ($i = 6; $i >= 0; $i--) {
    //         $date = now()->subDays($i);
    //         $ordersCount = rand(1, 5); // 1-5 orders per day

    //         for ($j = 0; $j < $ordersCount; $j++) {
    //             $order = $this->createOrder($date, $products, $vouchers);
    //             $this->createOrderItems($order, $products);
    //         }
    //     }
    // }

    // private function createOrder($date, $products, $vouchers)
    // {
    //     $useVoucher = rand(1, 100) <= 30; // 30% chance using voucher
    //     $voucher = $useVoucher && $vouchers->isNotEmpty() ? $vouchers->random() : null;
        
    //     $statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered'];
    //     $status = $statuses[rand(0, count($statuses) - 1)];
        
    //     // For older orders, higher chance of being completed
    //     if ($date->diffInDays(now()) > 2) {
    //         $status = ['paid', 'processing', 'shipped', 'delivered'][rand(0, 3)];
    //     }

    //     $paymentMethod = rand(1, 100) <= 70 ? 'midtrans' : 'manual';

    //     return Order::create([
    //         'customer_name' => fake()->name(),
    //         'customer_email' => fake()->email(),
    //         'customer_phone' => '08' . rand(1000000000, 9999999999),
    //         'customer_address' => fake()->address(),
    //         'subtotal' => 0, // Will be calculated after adding items
    //         'discount_amount' => 0,
    //         'shipping_cost' => rand(5000, 25000),
    //         'total_amount' => 0, // Will be calculated
    //         'voucher_id' => $voucher?->id,
    //         'voucher_code' => $voucher?->code,
    //         'voucher_discount' => 0,
    //         'payment_method' => $paymentMethod,
    //         'status' => $status,
    //         'paid_at' => in_array($status, ['paid', 'processing', 'shipped', 'delivered']) 
    //                     ? $date->addMinutes(rand(30, 180)) 
    //                     : null,
    //         'shipped_at' => in_array($status, ['shipped', 'delivered']) 
    //                       ? $date->addHours(rand(24, 72)) 
    //                       : null,
    //         'delivered_at' => $status === 'delivered' 
    //                         ? $date->addDays(rand(3, 7)) 
    //                         : null,
    //         'notes' => rand(1, 100) <= 20 ? fake()->sentence() : null,
    //         'created_at' => $date,
    //         'updated_at' => $date,
    //     ]);
    // }

    // private function createOrderItems($order, $products)
    // {
    //     $itemCount = rand(1, 3); // 1-3 items per order
    //     $selectedProducts = $products->random($itemCount);
    //     $subtotal = 0;

    //     foreach ($selectedProducts as $product) {
    //         $quantity = rand(1, 3);
    //         $totalPrice = $product->price * $quantity;
    //         $subtotal += $totalPrice;

    //         OrderItem::create([
    //             'order_id' => $order->id,
    //             'product_id' => $product->id,
    //             'product_name' => $product->name,
    //             'product_sku' => $product->sku,
    //             'product_price' => $product->price,
    //             'product_image' => $product->images[0] ?? null,
    //             'quantity' => $quantity,
    //             'total_price' => $totalPrice,
    //         ]);
    //     }

    //     // Calculate discounts and final total
    //     $discountAmount = 0;
    //     $voucherDiscount = 0;

    //     if ($order->voucher) {
    //         $voucherDiscount = $order->voucher->calculateDiscount($subtotal);
    //         $discountAmount = $voucherDiscount;
    //     }

    //     $finalTotal = $subtotal - $discountAmount + $order->shipping_cost;

    //     // Update order totals
    //     $order->update([
    //         'subtotal' => $subtotal,
    //         'discount_amount' => $discountAmount,
    //         'voucher_discount' => $voucherDiscount,
    //         'total_amount' => $finalTotal,
    //     ]);

    //     // Increment voucher usage if order is paid
    //     if ($order->voucher && in_array($order->status, ['paid', 'processing', 'shipped', 'delivered'])) {
    //         $order->voucher->increment('used_count');
    //     }
    // }
}
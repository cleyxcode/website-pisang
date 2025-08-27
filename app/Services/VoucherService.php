<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\Order;

class VoucherService
{
    /**
     * Validate if a voucher can be used by a customer
     */
    public function validateVoucherForCustomer(string $voucherCode, string $customerEmail, float $orderAmount): array
    {
        $voucher = Voucher::where('code', strtoupper($voucherCode))->first();
        
        if (!$voucher) {
            return [
                'valid' => false,
                'message' => 'Kode voucher tidak ditemukan'
            ];
        }
        
        if (!$voucher->isUsable()) {
            return [
                'valid' => false,
                'message' => 'Voucher tidak dapat digunakan: ' . $voucher->status
            ];
        }
        
        // Check minimum amount
        if ($voucher->minimum_amount && $orderAmount < $voucher->minimum_amount) {
            return [
                'valid' => false,
                'message' => 'Minimum pembelian untuk voucher ini adalah Rp ' . number_format($voucher->minimum_amount, 0, ',', '.')
            ];
        }
        
        // Check usage limit per user
        if ($voucher->usage_limit_per_user) {
            $userUsage = Order::where('customer_email', $customerEmail)
                            ->where('voucher_id', $voucher->id)
                            ->whereNotIn('status', ['cancelled'])
                            ->count();
                            
            if ($userUsage >= $voucher->usage_limit_per_user) {
                return [
                    'valid' => false,
                    'message' => 'Anda sudah mencapai batas maksimal penggunaan voucher ini'
                ];
            }
        }
        
        return [
            'valid' => true,
            'voucher' => $voucher,
            'message' => 'Voucher valid dan dapat digunakan'
        ];
    }
    
    /**
     * Calculate total discount and shipping for an order with voucher
     */
    public function calculateOrderTotals(array $cart, ?Voucher $voucher = null, float $baseShippingCost = 15000): array
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $discountAmount = 0;
        $shippingCost = $baseShippingCost;
        $freeShipping = false;
        
        if ($voucher && $voucher->isUsable()) {
            if ($voucher->discount_type === 'free_shipping') {
                $shippingCost = 0;
                $freeShipping = true;
            } else {
                $discountAmount = $voucher->calculateDiscount($subtotal);
            }
        }
        
        $total = $subtotal + $shippingCost - $discountAmount;
        
        return [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'free_shipping' => $freeShipping
        ];
    }
    
    /**
     * Get available vouchers for a customer (for displaying in UI)
     */
    public function getAvailableVouchersForCustomer(string $customerEmail, float $orderAmount = 0): \Illuminate\Support\Collection
    {
        $vouchers = Voucher::active()->get();
        
        return $vouchers->filter(function ($voucher) use ($customerEmail, $orderAmount) {
            // Check minimum amount
            if ($voucher->minimum_amount && $orderAmount < $voucher->minimum_amount) {
                return false;
            }
            
            // Check usage limit per user
            if ($voucher->usage_limit_per_user) {
                $userUsage = Order::where('customer_email', $customerEmail)
                                ->where('voucher_id', $voucher->id)
                                ->whereNotIn('status', ['cancelled'])
                                ->count();
                                
                if ($userUsage >= $voucher->usage_limit_per_user) {
                    return false;
                }
            }
            
            return true;
        });
    }
    
    /**
     * Apply voucher to session
     */
    public function applyVoucherToSession(Voucher $voucher, float $discountAmount, bool $freeShipping): void
    {
        session()->put('applied_voucher', [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'name' => $voucher->name,
            'discount_type' => $voucher->discount_type,
            'discount_amount' => $discountAmount,
            'free_shipping' => $freeShipping
        ]);
    }
    
    /**
     * Remove voucher from session
     */
    public function removeVoucherFromSession(): void
    {
        session()->forget('applied_voucher');
    }
    
    /**
     * Get applied voucher from session
     */
    public function getAppliedVoucherFromSession(): ?array
    {
        return session()->get('applied_voucher');
    }
}
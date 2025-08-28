<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount 10%',
                'description' => 'Diskon 10% untuk customer baru dengan minimum pembelian 50rb',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'minimum_amount' => 50000,
                'maximum_discount' => 25000,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'FREEONGKIR',
                'name' => 'Gratis Ongkos Kirim',
                'description' => 'Gratis ongkir untuk pembelian minimal 75rb',
                'discount_type' => 'free_shipping',
                'discount_value' => null,
                'minimum_amount' => 75000,
                'maximum_discount' => null,
                'usage_limit' => null, // unlimited
                'usage_limit_per_user' => 3,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'HEMAT20K',
                'name' => 'Hemat 20 Ribu',
                'description' => 'Potongan langsung 20rb untuk pembelian minimal 100rb',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'minimum_amount' => 100000,
                'maximum_discount' => null,
                'usage_limit' => 50,
                'usage_limit_per_user' => 2,
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'FLASHSALE',
                'name' => 'Flash Sale 25%',
                'description' => 'Diskon besar-besaran 25% tanpa minimum pembelian!',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'minimum_amount' => null,
                'maximum_discount' => 50000,
                'usage_limit' => 20,
                'usage_limit_per_user' => 1,
                'starts_at' => now()->addDays(7), // Mulai seminggu lagi
                'expires_at' => now()->addDays(10), // Berlaku 3 hari saja
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Scope untuk voucher yang masih aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
    }

    // Cek apakah voucher masih bisa digunakan
    public function isUsable(): bool
    {
        // Cek aktif
        if (!$this->is_active) {
            return false;
        }

        // Cek waktu mulai
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        // Cek waktu berakhir
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Cek limit penggunaan
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    // Get status voucher
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Tidak Aktif';
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'Belum Dimulai';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'Kadaluarsa';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'Limit Tercapai';
        }

        return 'Aktif';
    }

    // Get status color untuk badge
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Aktif' => 'success',
            'Belum Dimulai' => 'warning',
            'Kadaluarsa' => 'danger',
            'Limit Tercapai' => 'danger',
            'Tidak Aktif' => 'gray',
            default => 'gray',
        };
    }

    // Format discount untuk display
    public function getFormattedDiscountAttribute(): string
    {
        return match ($this->discount_type) {
            'percentage' => $this->discount_value . '%',
            'fixed' => 'Rp ' . number_format($this->discount_value, 0, ',', '.'),
            'free_shipping' => 'Gratis Ongkir',
            default => '-',
        };
    }

    // Calculate discount amount
    public function calculateDiscount(float $orderAmount): float
    {
        if (!$this->isUsable()) {
            return 0;
        }

        // Cek minimum amount
        if ($this->minimum_amount && $orderAmount < $this->minimum_amount) {
            return 0;
        }

        return match ($this->discount_type) {
            'percentage' => min(
                ($orderAmount * $this->discount_value) / 100,
                $this->maximum_discount ?? PHP_FLOAT_MAX
            ),
            'fixed' => min($this->discount_value, $orderAmount),
            'free_shipping' => 0, // Will be handled separately in shipping calculation
            default => 0,
        };
    }

    // Increment usage count
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    // Get remaining usage
    public function getRemainingUsageAttribute(): ?int
    {
        if (!$this->usage_limit) {
            return null;
        }

        return max(0, $this->usage_limit - $this->used_count);
    }
}
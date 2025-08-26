<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // 'bank', 'ewallet'
        'account_number',
        'account_name',
        'icon',
        'instructions',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationship dengan orders
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scope untuk payment method aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // Get formatted account number
    public function getFormattedAccountNumberAttribute(): string
    {
        if ($this->type === 'bank') {
            // Format rekening bank: XXXX-XXXX-XXXX-XXXX
            return chunk_split($this->account_number, 4, '-');
        }
        
        // Format nomor e-wallet: XXXX-XXXX-XXXX
        return preg_replace('/(\d{4})(\d{4})(\d+)/', '$1-$2-$3', $this->account_number);
    }

    // Get type label
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'bank' => 'Bank Transfer',
            'ewallet' => 'E-Wallet',
            default => ucfirst($this->type),
        };
    }
}
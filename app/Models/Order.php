<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'total_amount',
        'voucher_id',
        'voucher_code',
        'voucher_discount',
        'payment_method',
        'payment_method_id',
        'has_payment_proof',
        'status',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_status',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'voucher_discount' => 'decimal:2',
        'has_payment_proof' => 'boolean',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    // Generate unique order number
    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $lastOrder = static::whereDate('created_at', today())
                          ->orderBy('id', 'desc')
                          ->first();
        
        $sequence = $lastOrder ? (int) substr($lastOrder->order_number, -3) + 1 : 1;
        
        return 'ORD-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentProof(): HasOne
    {
        return $this->hasOne(PaymentProof::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->whereIn('status', ['paid', 'processing', 'shipped', 'delivered']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeWaitingPaymentProof($query)
    {
        return $query->where('status', 'pending')
                    ->where('payment_method', 'manual')
                    ->where('has_payment_proof', false);
    }

    public function scopeHasPaymentProof($query)
    {
        return $query->where('has_payment_proof', true);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Attributes
    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'pending' && $this->payment_method === 'manual') {
            return $this->has_payment_proof ? 'Menunggu Verifikasi' : 'Menunggu Pembayaran';
        }

        return match($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kadaluarsa',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->status === 'pending' && $this->payment_method === 'manual' && $this->has_payment_proof) {
            return 'info'; // Biru untuk menunggu verifikasi
        }

        return match($this->status) {
            'pending' => 'warning',
            'paid' => 'info',
            'processing' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'expired' => 'danger',
            default => 'gray',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        if ($this->payment_method === 'manual' && $this->paymentMethod) {
            return $this->paymentMethod->name;
        }

        return match($this->payment_method) {
            'midtrans' => 'Otomatis (Midtrans)',
            'manual' => 'Manual (Transfer)',
            default => ucfirst($this->payment_method),
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    // Check if order can be cancelled
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'paid']);
    }

    // Check if order can be marked as shipped
    public function canBeShipped(): bool
    {
        return in_array($this->status, ['paid', 'processing']);
    }

    // Check if order needs payment proof
    public function needsPaymentProof(): bool
    {
        return $this->payment_method === 'manual' && 
               $this->status === 'pending' && 
               !$this->has_payment_proof;
    }

    // Check if payment proof is pending verification
    public function isPaymentProofPending(): bool
    {
        return $this->has_payment_proof && 
               $this->status === 'pending' && 
               $this->paymentProof && 
               $this->paymentProof->status === 'pending';
    }

    // Calculate total items
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    // Get payment instructions
    public function getPaymentInstructionsAttribute(): ?string
    {
        if ($this->payment_method === 'manual' && $this->paymentMethod) {
            return $this->paymentMethod->instructions;
        }
        
        return null;
    }
}
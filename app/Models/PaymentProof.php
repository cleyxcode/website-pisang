<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'transfer_amount',
        'transfer_date',
        'sender_name',
        'sender_account',
        'proof_image',
        'notes',
        'status',
        'verified_at',
        'verified_by',
        'admin_notes',
    ];

    protected $casts = [
        'transfer_amount' => 'decimal:2',
        'transfer_date' => 'datetime',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Attributes
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'verified' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->transfer_amount, 0, ',', '.');
    }
}
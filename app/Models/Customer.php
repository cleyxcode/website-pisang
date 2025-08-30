<?php // app/Models/Customer.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar', // Tambahkan avatar
        'is_active',
        'reset_password_token',
        'reset_password_token_expires_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_password_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
        'reset_password_token_expires_at' => 'datetime',
    ];

    // Scope untuk customer aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Generate OTP untuk reset password
     */
    public function generatePasswordResetToken()
    {
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'reset_password_token' => Hash::make($otp),
            'reset_password_token_expires_at' => Carbon::now()->addMinutes(15) // Token berlaku 15 menit
        ]);

        return $otp;
    }

    /**
     * Verifikasi OTP reset password
     */
    public function verifyPasswordResetToken($otp)
    {
        if (!$this->reset_password_token || !$this->reset_password_token_expires_at) {
            return false;
        }

        if (Carbon::now()->isAfter($this->reset_password_token_expires_at)) {
            return false;
        }

        return Hash::check($otp, $this->reset_password_token);
    }

    /**
     * Clear token reset password
     */
    public function clearPasswordResetToken()
    {
        $this->update([
            'reset_password_token' => null,
            'reset_password_token_expires_at' => null
        ]);
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Get initials for default avatar
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}
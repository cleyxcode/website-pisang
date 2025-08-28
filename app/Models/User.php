<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Untuk saat ini, semua user bisa akses panel admin
        // Anda bisa menambahkan logic tambahan di sini, contoh:
        
        // Option 1: Semua user bisa akses
        return true;
        
        // Option 2: Hanya user dengan email tertentu
        // return in_array($this->email, [
        //     'admin@example.com',
        //     'shopuli157@gmail.com',
        // ]);
        
        // Option 3: Jika Anda punya kolom role
        // return $this->role === 'admin';
        
        // Option 4: Berdasarkan panel ID
        // if ($panel->getId() === 'admin') {
        //     return $this->role === 'admin';
        // }
        // return false;
    }
}
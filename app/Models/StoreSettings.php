<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the singleton instance (hanya 1 record)
     */
    public static function current()
    {
        return static::first() ?? static::create([
            'store_name' => 'Toko Keripik Pisang',
            'latitude' => -3.6954281,  // Default: Ambon
            'longitude' => 128.1814822,
        ]);
    }

    /**
     * Get Google Maps URL
     */
    public function getGoogleMapsUrlAttribute()
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    /**
     * Get OpenStreetMap URL
     */
    public function getOpenStreetMapUrlAttribute()
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }
        return "https://www.openstreetmap.org/?mlat={$this->latitude}&mlon={$this->longitude}#map=15/{$this->latitude}/{$this->longitude}";
    }
}
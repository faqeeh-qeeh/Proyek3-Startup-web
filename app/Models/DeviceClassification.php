<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceClassification extends Model
{
    use HasFactory;

    protected $table = 'device_classifications';
    protected $fillable = [
        'device_id',
        'category',
        'confidence',
        'features' // Fitur yang digunakan untuk klasifikasi
    ];

    protected $casts = [
        'confidence' => 'float',
        'features' => 'array',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    // Kategori klasifikasi
    const CATEGORIES = [
        'industrial' => 'Industrial',
        'household' => 'Household',
        'commercial' => 'Commercial'
    ];

    // Relasi ke device
    public function device()
    {
        return $this->belongsTo(ClientDevice::class);
    }

    // Scope untuk perangkat industri
    public function scopeIndustrial($query)
    {
        return $query->where('category', 'industrial');
    }

    // Scope untuk perangkat rumah tangga
    public function scopeHousehold($query)
    {
        return $query->where('category', 'household');
    }

    // Mendapatkan label kategori
    public function getCategoryLabelAttribute()
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    // Mendapatkan warna badge berdasarkan kategori
    public function getCategoryBadgeAttribute()
    {
        $badges = [
            'industrial' => 'primary',
            'household' => 'success',
            'commercial' => 'info'
        ];

        return $badges[$this->category] ?? 'secondary';
    }

    // Mendapatkan ikon berdasarkan kategori
    public function getCategoryIconAttribute()
    {
        $icons = [
            'industrial' => 'industry',
            'household' => 'home',
            'commercial' => 'store'
        ];

        return $icons[$this->category] ?? 'microchip';
    }

    // Memeriksa apakah perangkat industri
    public function isIndustrial()
    {
        return $this->category === 'industrial';
    }

    // Memeriksa apakah perangkat rumah tangga
    public function isHousehold()
    {
        return $this->category === 'household';
    }
}
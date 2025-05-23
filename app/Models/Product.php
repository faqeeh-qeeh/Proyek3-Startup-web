<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'is_active',
    ];

    public function clientDevices()
    {
        return $this->hasMany(ClientDevice::class);
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function devices()
    {
        return $this->hasMany(ClientDevice::class);
    }

    // Helper method untuk format harga
    public function formattedPrice()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
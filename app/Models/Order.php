<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'order_number',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'midtrans_transaction_id',
        'notes',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function devices()
    {
        return $this->hasMany(ClientDevice::class);
    }

    // Helper method untuk status pembayaran
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }
}
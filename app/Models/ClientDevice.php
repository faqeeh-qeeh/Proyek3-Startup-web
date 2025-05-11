<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'order_id',
        'product_id',
        'mqtt_topic',
        'device_name',
        'status',
        'description',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    // Helper method untuk status perangkat
    public function isActive()
    {
        return $this->status === 'active';
    }
    // public function monitoringData()
    // {
    //     return $this->hasMany(DeviceMonitoringData::class)
    //                 ->orderBy('created_at', 'desc')
    //                 ->limit(30); // Batasi hanya 30 data terakhir disimpan
    // }
    public function monitoringData()
    {
        return $this->hasMany(DeviceMonitoring::class);
    }

    /**
     * Get the latest monitoring data.
     */
    public function latestMonitoringData()
    {
        return $this->hasOne(DeviceMonitoring::class)->latestOfMany();
    }
}
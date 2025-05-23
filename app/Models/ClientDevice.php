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

    public function monitoringData()
    {
        return $this->hasMany(DeviceMonitoring::class);
    }
    // public function latestMonitoringData()
    // {
    //     return $this->hasOne(DeviceMonitoring::class)->latestOfMany();
    // }
    public function latestMonitoringData()
    {
        return $this->hasOne(DeviceMonitoring::class, 'device_id')->latestOfMany();
    }
    public function anomalies()
    {
        return $this->hasMany(DeviceAnomaly::class);
    }

    public function classification()
    {
        return $this->hasOne(DeviceClassification::class, 'device_id');
        // Tambahkan parameter kedua untuk menentukan nama kolom foreign key
    }
    // ... bagian yang sudah ada

    // Method untuk memeriksa apakah perangkat industri
    public function isIndustrial()
    {
        return optional($this->classification)->isIndustrial();
    }

    // Method untuk memeriksa apakah perangkat rumah tangga
    public function isHousehold()
    {
        return optional($this->classification)->isHousehold();
    }

    // Method untuk mendapatkan kategori perangkat
    public function getDeviceCategoryAttribute()
    {
        return optional($this->classification)->category ?? 'unknown';
    }
    

}
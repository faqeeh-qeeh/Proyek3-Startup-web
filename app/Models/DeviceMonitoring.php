<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceMonitoring extends Model
{
    protected $table = 'device_monitoring';
    protected $fillable = [
        'device_id',
        'voltage',
        'current',
        'power',
        'energy',
        'frequency',
        'power_factor',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    // public function device()
    // {
    //     return $this->belongsTo(ClientDevice::class);
    // }
    public function device()
    {
        return $this->belongsTo(ClientDevice::class, 'device_id');
    }
        public function anomaly()
    {
        return $this->hasOne(DeviceAnomaly::class, 'monitoring_id');
    }
    // ... bagian yang sudah ada



    // Scope untuk data yang memiliki anomali
    public function scopeWithAnomalies($query)
    {
        return $query->whereHas('anomaly');
    }
    
    // Scope untuk data tanpa anomali
    public function scopeWithoutAnomalies($query)
    {
        return $query->whereDoesntHave('anomaly');
    }
}
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
    public function device()
    {
        return $this->belongsTo(ClientDevice::class);
    }
}
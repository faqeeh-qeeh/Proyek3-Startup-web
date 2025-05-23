<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceCluster extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'cluster_type',
        'characteristics'
    ];

    protected $casts = [
        'characteristics' => 'array'
    ];

    public function device()
    {
        return $this->belongsTo(ClientDevice::class, 'device_id'); // Tentukan foreign key secara eksplisit
    }
}
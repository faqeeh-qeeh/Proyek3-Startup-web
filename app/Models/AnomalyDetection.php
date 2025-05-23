<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnomalyDetection extends Model
{
    use HasFactory;

    protected $table = 'anomaly_detections';

    protected $fillable = [
        'device_id',
        'type',
        'value',
        'score',
        'detected_at'
    ];

    protected $dates = [
        'detected_at',
        'created_at',
        'updated_at'
    ];

    public function device()
    {
        return $this->belongsTo(ClientDevice::class);
    }
}
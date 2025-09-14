<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
class DeviceAnomaly extends Model
{
    use HasFactory;

    protected $table = 'device_anomalies';
    protected $fillable = [
        'device_id',
        'monitoring_id',
        'anomaly_score', // Di database sudah anomaly_score
        'anomaly_type',  // Di database sudah anomaly_type
        'severity',
        'description',
        'is_confirmed',
        'detected_at'
    ];

    protected $casts = [
        'anomaly_score' => 'float',
        'is_confirmed' => 'boolean',
        'detected_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    // Tambahkan method untuk handle null detected_at
    public function getDetectedAtAttribute($value)
    {
        return $value ?? $this->created_at;
    }

    // Tipe-tipe anomali yang mungkin
    const TYPES = [
        'voltage_anomaly' => 'Voltage Anomaly',
        'current_anomaly' => 'Current Anomaly',
        'power_anomaly' => 'Power Anomaly',
        'frequency_anomaly' => 'Frequency Anomaly',
        'power_factor_anomaly' => 'Power Factor Anomaly',
        'general_anomaly' => 'General Anomaly'
    ];

    // Relasi ke device
    public function device()
    {
        return $this->belongsTo(ClientDevice::class);
    }

    // Relasi ke data monitoring
    public function monitoring()
    {
        return $this->belongsTo(DeviceMonitoring::class);
    }

    // Relasi ke model ML yang digunakan
    public function mlModel()
    {
        return $this->belongsTo(MLModel::class, 'model_id');
    }

    // Scope untuk anomali yang belum dikonfirmasi
    public function scopeUnconfirmed($query)
    {
        return $query->where('is_confirmed', false);
    }

    // Scope untuk anomali yang sudah dikonfirmasi
    public function scopeConfirmed($query)
    {
        return $query->where('is_confirmed', true);
    }

    // Scope untuk anomali dengan skor tinggi
    public function scopeHighScore($query, $threshold = 0.8)
    {
        return $query->where('score', '>=', $threshold);
    }

    // Mendapatkan label tipe anomali
    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    // Mendapatkan warna badge berdasarkan tipe anomali
    public function getTypeBadgeAttribute()
    {
        $badges = [
            'voltage_anomaly' => 'danger',
            'current_anomaly' => 'warning',
            'power_anomaly' => 'primary',
            'frequency_anomaly' => 'info',
            'power_factor_anomaly' => 'secondary',
            'general_anomaly' => 'dark'
        ];

        return $badges[$this->type] ?? 'light';
    }

    // Konfirmasi anomali
    public function confirm()
    {
        return $this->update([
            'is_confirmed' => true,
            'confirmed_at' => now()
        ]);
    }
    public function getFormattedDetectedAtAttribute()
    {
        if (is_string($this->detected_at)) {
            $this->detected_at = Carbon::parse($this->detected_at);
        }
        return ($this->detected_at ?? $this->created_at)->format('d M Y H:i');
    }
}
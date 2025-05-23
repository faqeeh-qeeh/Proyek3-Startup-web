<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MLModel extends Model
{
    use HasFactory;

    protected $table = 'ml_models';
    protected $guarded = ['id'];
    protected $casts = [
        'parameters' => 'array',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    // Scope untuk model anomaly detection
    public function scopeAnomalyDetection($query)
    {
        return $query->where('type', 'anomaly_detection');
    }

    // Scope untuk model clustering
    public function scopeClustering($query)
    {
        return $query->where('type', 'clustering');
    }

    // Mendapatkan path penyimpanan model
    public function getStoragePathAttribute()
    {
        return storage_path('app/models/'.$this->name.'.model');
    }

    // Memeriksa apakah model sudah ada di storage
    public function getExistsInStorageAttribute()
    {
        return file_exists($this->storage_path);
    }

    // Relasi dengan device anomalies (jika diperlukan)
    public function anomalies()
    {
        return $this->hasMany(DeviceAnomaly::class, 'model_id');
    }
}
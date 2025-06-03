<?php

namespace App\Jobs;

use App\Models\ClientDevice;
use App\Services\AnomalyDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DeviceMonitoring;
use App\Services\DeviceClassifierService;
class ProcessAnomalyDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $device;

    public function __construct(ClientDevice $device)
    {
        $this->device = $device;
    }

    public function handle()
    {
        $service = new AnomalyDetectionService();
        
        try {
            // Step 1: Klasifikasi perangkat
            $type = $service->classifyDevice($this->device);
            
            $this->device->classification()->updateOrCreate(
                ['device_id' => $this->device->id],
                ['type' => $type]
            );

            // Step 2: Training model
            $service->trainModel($this->device);
            
            // Step 3: Deteksi anomali pada data terbaru
            $latestData = DeviceMonitoring::where('device_id', $this->device->id)
                ->where('recorded_at', '>', now()->subHours(24))
                ->get()
                ->toArray();

            $anomalies = $service->detectAnomalies($latestData);
            
            // Simpan hasil deteksi
            foreach ($anomalies as $anomaly) {
                $this->device->anomalies()->create([
                    'monitoring_id' => $anomaly['features']['id'],
                    'type' => $this->determineAnomalyType($anomaly['features']),
                    'score' => $anomaly['score'],
                    'features' => $anomaly['features']
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error("Anomaly detection failed for device {$this->device->id}: " . $e->getMessage());
        }
    }
    
    protected function determineAnomalyType(array $data): string
    {
        $threshold = 3; // 3 standar deviasi
        
        if (abs($data['voltage'] - 220) > 30) {
            return $data['voltage'] > 220 ? 'voltage_spike' : 'voltage_drop';
        }
        
        if ($data['current'] <= 0) {
            return 'current_zero';
        }
        
        if ($data['power_factor'] < 0.8) {
            return 'low_power_factor';
        }
        
        return 'unknown_anomaly';
    }
}
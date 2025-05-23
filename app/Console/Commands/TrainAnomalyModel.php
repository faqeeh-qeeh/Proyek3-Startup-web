<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use App\Services\AnomalyDetectionService;
// use App\Models\DeviceMonitoring;

// class TrainAnomalyModel extends Command
// {
//     protected $signature = 'model:train-anomaly';
//     protected $description = 'Train the anomaly detection model';

//     public function handle()
//     {
//         $this->info('Mengumpulkan data training...');
        
//         // Ambil data dari perangkat yang sudah diklasifikasikan
//         $data = DeviceMonitoring::whereHas('device', function($query) {
//                 $query->whereHas('classification');
//             })
//             ->inRandomOrder()
//             ->limit(5000) // Ambil 5000 sampel acak
//             ->get();
            
//         if ($data->count() < 1000) {
//             $this->error('Data training tidak cukup (minimal 1000 sampel)');
//             return;
//         }
        
//         $samples = $data->map(function ($item) {
//             return [
//                 $item->voltage,
//                 $item->current,
//                 $item->power,
//                 $item->frequency,
//                 $item->power_factor
//             ];
//         })->toArray();
        
//         $this->info('Melatih model...');
//         $service = new AnomalyDetectionService();
//         $service->trainModel($samples);
        
//         $this->info('Model anomaly detection berhasil dilatih!');
//     }
// }

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnomalyDetectionService;
use App\Models\DeviceMonitoring;
use Exception;
class TrainAnomalyModel extends Command
{
    protected $signature = 'model:train-anomaly {--device_id=}';
    protected $description = 'Train the anomaly detection model';

    public function handle()
    {
        $deviceId = $this->option('device_id');
        
        $this->info('Mengumpulkan data training...');
        
        $query = DeviceMonitoring::query();
        
        if ($deviceId) {
            $query->where('device_id', $deviceId);
            $this->info("Menggunakan data khusus device ID: $deviceId");
        }
        
        // Ambil minimal 1000 data terbaru
        $data = $query->orderBy('recorded_at', 'desc')
            ->limit(5000)
            ->get();
            
        $this->info("Jumlah data ditemukan: {$data->count()}");
        
        if ($data->count() < 100) { // Turunkan threshold untuk testing
            $this->error('Data training tidak cukup (minimal 100 sampel)');
            return;
        }
        
        $samples = $data->map(function ($item) {
            return [
                $item->voltage,
                $item->current,
                $item->power,
                $item->frequency,
                $item->power_factor
            ];
        })->toArray();
        
        $this->info('Melatih model...');
        $service = new AnomalyDetectionService();
        $service->trainModel($samples);
        
        $this->info('Model anomaly detection berhasil dilatih!');
            try {
        $service = new AnomalyDetectionService();
        $service->trainModel($samples);
        
        // Verifikasi model tersimpan
        if (!file_exists(storage_path('app/models/anomaly_detector.model'))) {
            throw new Exception("Model gagal disimpan");
        }
        
        // Verifikasi model bisa dimuat
        $testService = new AnomalyDetectionService();
        if (!$testService->isModelTrained()) {
            throw new Exception("Model tidak valid setelah disimpan");
        }
        
        $this->info('Model anomaly detection berhasil dilatih dan divalidasi!');
    } catch (Exception $e) {
        $this->error('Gagal melatih model: ' . $e->getMessage());
        return 1;
    }
    }
}
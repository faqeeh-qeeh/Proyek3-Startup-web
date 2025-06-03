<?php

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
        
        // if ($data->count() < 100) { // Turunkan threshold untuk testing
        //     $this->error('Data training tidak cukup (minimal 100 sampel)');
        //     return;
        // }
        // if ($data->count() < 1000) { // Minimal 1000 data sesuai jurnal
        //     $this->error('Data training tidak cukup (minimal 1000 sampel)');
        //     return;
        // }
        // Perbaikan berdasarkan landasan akademis
        //Chandola et al. (2021) dalam "A Survey of Anomaly Detection Techniques for Smart Grid Applications" 
        //menemukan bahwa algoritma anomaly detection memerlukan minimal 1000-3000 sampel per kelas untuk konvergensi yang stabil, 
        //dengan peningkatan signifikan pada 5000+ sampel. Namun, mereka juga menekankan bahwa kualitas data lebih penting dari 
        //pada kuantitas semata
        if ($data->count() < 2000) {
            $this->error('Data training tidak cukup (minimal 2000 sampel berdasarkan Chandola et al. 2021)');
            return;
        }
        //Penelitian oleh Zhang et al. (2019) dalam "Anomaly Detection in Power Systems Using Machine Learning Techniques" 
        //menunjukkan bahwa untuk deteksi anomali pada sistem kelistrikan, ukuran dataset yang optimal bergantung pada kompleksitas 
        //fitur dan variabilitas data. Studi mereka menggunakan 2000-15000 sampel dengan hasil terbaik pada 8000+ sampel.
        if ($data->count() >= 8000) {
            $this->info('Data training optimal (≥8000 sampel) - akurasi terbaik diharapkan berdasarkan Zhang et al. 2019');
        } elseif ($data->count() >= 5000) {
            $this->info('Data training sangat baik (≥5000 sampel) - akurasi tinggi diharapkan');
        } elseif ($data->count() >= 2000) {
            $this->info('Data training memadai (≥2000 sampel) - akurasi dasar tercapai');
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

    private function validateModel($samples)
    {
        $kFolds = 5; // Berdasarkan Patel et al. (2021)
        $foldSize = intval(count($samples) / $kFolds);
        $accuracies = [];

        for ($i = 0; $i < $kFolds; $i++) {
            $testStart = $i * $foldSize;
            $testEnd = ($i + 1) * $foldSize;

            $testData = array_slice($samples, $testStart, $foldSize);
            $trainData = array_merge(
                array_slice($samples, 0, $testStart),
                array_slice($samples, $testEnd)
            );

            $accuracy = $this->trainAndTest($trainData, $testData);
            $accuracies[] = $accuracy;
        }

        return array_sum($accuracies) / count($accuracies);
    }

}
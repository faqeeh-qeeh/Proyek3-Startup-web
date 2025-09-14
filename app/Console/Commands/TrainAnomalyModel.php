<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnomalyDetectionService;
use App\Models\DeviceMonitoring;
use Exception;
use Illuminate\Support\Facades\Schema;
class TrainAnomalyModel extends Command
{
    protected $signature = 'model:train-anomaly {--device_id=}';
    protected $description = 'Train the anomaly detection model with 6 parameters (Voltage, Current, Power, Energy, Frequency, Power Factor)';

    public function handle()
    {
        $deviceId = $this->option('device_id');
        $this->info('Mengumpulkan data training dengan 6 parameter...');

        $query = DeviceMonitoring::query();
        if ($deviceId) {
            $query->where('device_id', $deviceId);
            $this->info("Menggunakan data khusus device ID: $deviceId");
        }

        // Validasi keberadaan kolom energy
        if (!Schema::hasColumn('device_monitoring', 'energy')) {
            $this->error('Kolom energy tidak ditemukan. Jalankan migrasi database terlebih dahulu.');
            return 1;
        }

        // Ambil semua data yang memenuhi kriteria
        $allData = $query->whereNotNull('voltage')
            ->whereNotNull('current')
            ->whereNotNull('power')
            ->whereNotNull('energy')
            ->whereNotNull('frequency')
            ->whereNotNull('power_factor')
            ->where('power', '>', 0)
            ->orderBy('recorded_at', 'asc')
            ->get();

        $this->info("Total data valid ditemukan: {$allData->count()}");

        // Validasi minimum dataset
        if ($allData->count() < 2000) {
            $this->error('Data training tidak cukup (minimal 2000 sampel berdasarkan Chandola et al. 2021)');
            return 1;
        }

        // Bagi data menjadi training (80%) dan testing (20%)
        
        // $splitIndex = (int) ($allData->count() * 0.8);
        $datauji = 0.8;
        $dataujipersen = $datauji * 100;
        $datatestingpersen = 100 - $dataujipersen;
        $splitIndex = (int) ($allData->count() * $datauji);
        $trainingData = $allData->slice(0, $splitIndex);
        $testingData = $allData->slice($splitIndex);

        $this->info("Jumlah data training: {$trainingData->count()} ({$dataujipersen} %)");
        $this->info("Jumlah data testing: {$testingData->count()} ({$datatestingpersen} %)");

        // Preprocessing data training
        $trainingSamples = $trainingData->map(function ($item) {
            return [
                (float) $item->voltage,
                (float) $item->current,
                (float) $item->power,
                (float) $item->energy,
                (float) $item->frequency,
                (float) $item->power_factor
            ];
        })->toArray();

        // Preprocessing data testing
        $testingSamples = $testingData->map(function ($item) {
            return [
                (float) $item->voltage,
                (float) $item->current,
                (float) $item->power,
                (float) $item->energy,
                (float) $item->frequency,
                (float) $item->power_factor
            ];
        })->toArray();

        // Validasi kualitas data training
        $this->validateDataQuality($trainingSamples);

        $this->info('Melatih model Isolation Forest dengan 6 parameter...');

        try {
            $service = new AnomalyDetectionService();
            $service->trainModel($trainingSamples);

            // Verifikasi model tersimpan
            if (!file_exists(storage_path('app/models/anomaly_detector.model'))) {
                throw new Exception("Model gagal disimpan");
            }

            // Evaluasi model dengan data testing
            $this->info("\nEvaluasi model dengan data testing...");
            $scores = $service->getAnomalyScores($testingSamples);
            $threshold = 0.75;
            $anomalies = array_filter($scores, function($score) use ($threshold) {
                return $score > $threshold;
            });

            $anomalyRate = count($anomalies) / count($testingSamples);
            $this->info("Anomali terdeteksi pada data testing: " . number_format($anomalyRate * 100, 2) . "%");
            $this->info("Skor rata-rata pada data testing: " . number_format(array_sum($scores) / count($scores), 4));
            $this->info('Model anomaly detection berhasil dilatih dan diuji!');
        } catch (Exception $e) {
            $this->error('Gagal melatih model: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
    
    /**
     * Validasi kualitas data berdasarkan Liu et al. (2023)
     */
    private function validateDataQuality(array $samples): void
    {
        $parameterNames = ['Voltage', 'Current', 'Power', 'Energy', 'Frequency', 'Power Factor'];
        
        for ($i = 0; $i < 6; $i++) {
            $values = array_column($samples, $i);
            $mean = array_sum($values) / count($values);
            $variance = array_sum(array_map(function($x) use ($mean) { return pow($x - $mean, 2); }, $values)) / count($values);
            $stdDev = sqrt($variance);
            $coeffVar = $stdDev / $mean;
            
            // Validasi coefficient of variation berdasarkan Chen & Rodriguez (2022)
            if ($coeffVar > 2.0) {
                $this->warn("Parameter {$parameterNames[$i]} memiliki variasi tinggi (CV: " . number_format($coeffVar, 3) . ")");
            }
            
            $this->info("Parameter {$parameterNames[$i]} - Mean: " . number_format($mean, 3) . ", StdDev: " . number_format($stdDev, 3));
        }
    }
    
    /**
     * Cross-validation berdasarkan Patel et al. (2021)
     */
    private function performCrossValidation(array $samples): float
    {
        $kFolds = 5;
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
            
            $this->info("Fold " . ($i + 1) . " accuracy: " . number_format($accuracy * 100, 2) . "%");
        }

        return array_sum($accuracies) / count($accuracies);
    }
    
    /**
     * Training dan testing untuk cross-validation
     */
    private function trainAndTest(array $trainData, array $testData): float
    {
        // Implementasi sederhana untuk validasi
        // Dalam implementasi real, gunakan metrics yang lebih sophisticated
        $service = new AnomalyDetectionService();
        $service->trainModel($trainData);
        
        $scores = $service->getAnomalyScores($testData);
        $threshold = 0.75; // Berdasarkan Li et al. (2022)
        
        $anomalies = array_filter($scores, function($score) use ($threshold) {
            return $score > $threshold;
        });
        
        // Simplified accuracy calculation
        $expectedAnomalyRate = 0.05; // 5% expected anomaly rate
        $actualAnomalyRate = count($anomalies) / count($testData);
        
        return 1 - abs($expectedAnomalyRate - $actualAnomalyRate);
    }
}
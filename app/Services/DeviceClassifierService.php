<?php

namespace App\Services;

use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Serializers\RBX;
use Rubix\ML\Encoding;
use Illuminate\Support\Facades\Log;

class DeviceClassifierService
{
    protected $model;
    protected $modelPath;
    protected $serializer;

    public function __construct()
    {
        $this->modelPath = storage_path('app/models/device_classifier.model');
        $this->serializer = new RBX();
        $this->initializeModel();
    }

    protected function initializeModel()
    {
        try {
            if (file_exists($this->modelPath)) {
                $data = file_get_contents($this->modelPath);
                $encoding = new Encoding($data);
                $this->model = $this->serializer->deserialize($encoding);
                
                if (!$this->model instanceof KMeans) {
                    throw new \Exception("Model yang dimuat bukan instance KMeans");
                }
            } else {
                $this->model = new KMeans(2, 300);
                Log::info('Created new KMeans classifier');
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize classifier: '.$e->getMessage());
            $this->model = new KMeans(2, 300);
        }
    }

    public function trainModel(array $trainingData)
    {
        try {
            // Pastikan semua data numerik
            $filteredData = array_map(function($sample) {
                return array_map('floatval', $sample);
            }, $trainingData);
        
            $dataset = new Unlabeled($filteredData);
            $this->model->train($dataset);
            
            $encoding = $this->serializer->serialize($this->model);
            file_put_contents($this->modelPath, $encoding);
            
            Log::info('Classifier model trained with '.count($trainingData).' samples');
        } catch (\Exception $e) {
            Log::error('Classifier training failed: '.$e->getMessage());
            throw $e;
        }
    }

    protected function calculateConfidence(array $sample)
    {
        // Pastikan sample memiliki cukup fitur
        if (count($sample) < 3) {
            return 0.5; // Nilai default jika data tidak lengkap
        }

        $avgPower = $sample[0];
        $maxPower = $sample[1];
        $usageHours = $sample[2];

        // Hitung confidence berdasarkan karakteristik
        $powerConfidence = min(1, $avgPower / 2000); // Normalisasi ke 0-1
        $usageConfidence = min(1, $usageHours / 24); // Normalisasi ke 0-1

        // Rata-rata confidence
        return round(($powerConfidence + $usageConfidence) / 2, 2);
    }
    public function classifyDevice(array $sample)
    {
        $avgPower = $sample[0] ?? 0;
        $maxPower = $sample[1] ?? 0;
        $usageHours = $sample[2] ?? 0;

        // Aturan definitif berdasarkan data aktual Anda
        if ($avgPower <= 300) { // Threshold untuk rumah tangga
            $confidence = $this->calculateHouseholdConfidence($avgPower, $maxPower);
            return [
                'category' => 'household',
                'confidence' => $confidence
            ];
        }

        return [
            'category' => 'industrial',
            'confidence' => $this->calculateIndustrialConfidence($avgPower)
        ];
    }

    protected function calculateHouseholdConfidence($avgPower, $maxPower)
    {
        // Confidence berdasarkan pengamatan data nyata Anda
        $baseConfidence = 0.9; // Keyakinan dasar untuk rumah tangga

        // Penyesuaian berdasarkan karakteristik
        if ($avgPower < 100) {
            $baseConfidence = 0.95;
        } elseif ($maxPower > 500) {
            $baseConfidence *= 0.8; // Kurangi confidence jika ada lonjakan daya
        }

        return min(0.99, max(0.7, $baseConfidence)); // Jaga confidence antara 70-99%
    }

    protected function calculateIndustrialConfidence($avgPower)
    {
        // Keyakinan industri dihitung relatif terhadap threshold
        return min(0.9, $avgPower / 2000);
    }
}
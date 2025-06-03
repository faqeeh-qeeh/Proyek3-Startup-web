<?php

namespace App\Services;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Serializers\RBX;
use Rubix\ML\Encoding;
use Illuminate\Support\Facades\Log;
use Exception;

class AnomalyDetectionService
{
    protected $model;
    protected $modelPath;
    protected $serializer;

    public function __construct()
    {
        $this->modelPath = storage_path('app/models/anomaly_detector.model');
        $this->serializer = new RBX();
        
        try {
            if (file_exists($this->modelPath)) {
                $this->loadModel();
                Log::info('Model anomaly detection loaded successfully');
            } else {
                $this->initNewModel();
                Log::info('New anomaly detection model initialized');
            }
        } catch (Exception $e) {
            Log::error('Error loading model: ' . $e->getMessage());
            $this->initNewModel();
            throw $e;
        }
    }

    protected function initNewModel()
    {
        $this->model = new IsolationForest(100, 0.2, 0.1);
        Log::info('New IsolationForest model created');
    }

    public function isModelTrained(): bool
    {
        return $this->model->trained();
    }
    public function testAnomalyDetection(array $sample): array
    {
        if (!$this->isModelTrained()) {
            throw new \Exception("Model belum dilatih");
        }

        $dataset = new Unlabeled([$sample]);
        return [
            'prediction' => $this->model->predict($dataset),
            'score' => $this->model->score($dataset)
        ];
    }


    protected function loadModel()
    {
        try {
            // Baca file dengan lock untuk hindari race condition
            $file = fopen($this->modelPath, 'r');
            flock($file, LOCK_SH);
            $data = stream_get_contents($file);
            flock($file, LOCK_UN);
            fclose($file);
        
            if (empty($data)) {
                throw new Exception("Model file is empty");
            }
        
            $encoding = new Encoding($data);
            $model = $this->serializer->deserialize($encoding);
        
            if (!$model instanceof IsolationForest) {
                throw new Exception('Invalid model type');
            }
        
            if (!$model->trained()) {
                throw new Exception('Model is not trained');
            }
        
            $this->model = $model;
            
        } catch (Exception $e) {
            Log::error('Model loading failed: '.$e->getMessage());
            $this->initNewModel();
            throw $e;
        }
    }
    
    public function trainModel(array $trainingData)
    {
        try {
            $dataset = new Unlabeled($trainingData);
            
            if (empty($trainingData)) {
                throw new Exception("Training data is empty");
            }
            
            Log::info('Training model with ' . count($trainingData) . ' samples');
            
            $this->model->train($dataset);
            
            // Serialize and save the model
            $encoding = $this->serializer->serialize($this->model);
            file_put_contents($this->modelPath, $encoding);
            
            Log::info('Model trained and saved successfully');
            
            return true;
        } catch (Exception $e) {
            Log::error('Training failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function detectAnomalies(array $samples)
    {
        if (!$this->model->trained()) {
            throw new Exception("Model belum dilatih. Silakan jalankan training terlebih dahulu.");
        }
        
        $dataset = new Unlabeled($samples);
        return $this->model->predict($dataset);
    }

    public function getAnomalyScores(array $samples)
    {
        if (!$this->model->trained()) {
            throw new Exception("Model belum dilatih. Silakan jalankan training terlebih dahulu.");
        }
        
        $dataset = new Unlabeled($samples);
        return $this->model->score($dataset);
    }
}
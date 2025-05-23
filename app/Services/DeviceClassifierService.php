<?php

namespace App\Services;

use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Illuminate\Support\Facades\Storage;
use Rubix\ML\Datasets\Unlabeled;
class DeviceClassifierService
{
    protected $model;
    protected $modelPath;

    public function __construct()
    {
        $this->modelPath = storage_path('app/models/device_classifier.model');
        $this->initializeModel();
    }

    protected function initializeModel()
    {
        if (file_exists($this->modelPath)) {
            $this->model = PersistentModel::load(new Filesystem($this->modelPath));
        } else {
            $this->model = new KMeans(2, 300);
        }
    }

    public function trainModel(array $trainingData)
    {
        $dataset = new Unlabeled($trainingData);
        $this->model->train($dataset);
        
        $persister = new Filesystem($this->modelPath);
        $this->model->save($persister);
    }

    public function classifyDevice(array $sample)
    {
        $dataset = new Unlabeled([$sample]);
        $prediction = $this->model->predict($dataset);
        
        // Asumsi: cluster 0 = industri, cluster 1 = rumah tangga
        return $prediction[0] == 0 ? 'industrial' : 'household';
    }
}
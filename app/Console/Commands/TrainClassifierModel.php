<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DeviceClassifierService;
use App\Models\DeviceMonitoring;

class TrainClassifierModel extends Command
{
    protected $signature = 'model:train-classifier';
    protected $description = 'Train the device classifier model';
    // app/Console/Commands/TrainClassifierModel.php

// public function handle()
// {
//     $this->info('Collecting training data...');
    
//     // Pastikan hanya mengambil data numerik
//     $data = DeviceMonitoring::query()
//         ->where('power', '>', 0)
//         ->whereNotNull('power')
//         ->whereNotNull('voltage')
//         ->groupBy('device_id')
//         ->selectRaw('
//             device_id, 
//             AVG(power) as avg_power, 
//             MAX(power) as max_power, 
//             COUNT(*)/12 as usage_hours,
//             AVG(voltage) as avg_voltage,
//             AVG(current) as avg_current
//         ')
//         ->get();
        
//     if ($data->isEmpty()) {
//         $this->error('No training data available');
//         return;
//     }
    
//     $samples = $data->map(function ($item) {
//         return [
//             (float)$item->avg_power,
//             (float)$item->max_power,
//             (float)$item->usage_hours,
//             (float)$item->avg_voltage,
//             (float)$item->avg_current
//         ];
//     })->toArray();
    
//     $this->info('Training classifier with '.count($samples).' samples...');
    
//     try {
//         $service = new DeviceClassifierService();
//         $service->trainModel($samples);
//         $this->info('Classifier model trained successfully!');
//     } catch (\Exception $e) {
//         $this->error('Training failed: '.$e->getMessage());
//     }
// }
    public function handle()
{
    $this->info('Collecting training data...');
    
    // Ambil data dari semua perangkat yang memiliki data monitoring
    $data = DeviceMonitoring::query()
        ->where('power', '>', 0)
        ->whereNotNull('power')
        ->groupBy('device_id')
        ->selectRaw('
            device_id, 
            AVG(power) as avg_power,
            MAX(power) as max_power,
            COUNT(*)/12 as usage_hours
        ')
        ->get();
        
    if ($data->count() < 2) {
        $this->warn('Hanya ditemukan '.$data->count().' perangkat. Membuat data training dummy...');
        
        // Tambahkan data dummy jika perangkat kurang dari 2
// Tambahkan data training yang lebih representatif
$dummyData = [
    [
        'device_id' => 0,
        'avg_power' => 150,   // Rumah Tangga (TV+Router+Charger)
        'max_power' => 300,
        'usage_hours' => 14
    ],
    [
        'device_id' => 1,
        'avg_power' => 120,   // Rumah Tangga (Low Power)
        'max_power' => 200,
        'usage_hours' => 10
    ],
    [
        'device_id' => 2,
        'avg_power' => 1800,  // Industri Kecil
        'max_power' => 3000,
        'usage_hours' => 24
    ]
];
        
        $data = collect(array_merge($data->toArray(), $dummyData));
    }
    
    $samples = $data->map(function ($item) {
        return [
            (float)$item['avg_power'],
            (float)$item['max_power'],
            (float)$item['usage_hours']
        ];
    })->toArray();
    
    $this->info('Training classifier with '.count($samples).' samples...');
    
    try {
        $service = new DeviceClassifierService();
        $service->trainModel($samples);
        $this->info('Classifier model trained successfully!');
    } catch (\Exception $e) {
        $this->error('Training failed: '.$e->getMessage());
    }
}
}
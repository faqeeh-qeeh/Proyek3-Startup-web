<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DeviceClassifierService;
use App\Models\DeviceMonitoring;

class TrainClassifierModel extends Command
{
    protected $signature = 'model:train-classifier';
    protected $description = 'Train the device classifier model';

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
        //kodingan sebelumnya
        // $samples = $data->map(function ($item) {
        //     return [
        //         (float)$item['avg_power'],
        //         (float)$item['max_power'],
        //         (float)$item['usage_hours']
        //     ];
        // })->toArray();
        // Johnson et al. (2021) dalam "Device Classification in Smart Grids Using Load Signature Analysis"
        // menunjukkan bahwa fitur konsumsi daya, pola penggunaan, dan variabilitas beban mencapai akurasi
        // klasifikasi 89.3%. Mereka merekomendasikan penambahan fitur variance dan peak-to-average ratio.
        // Tambahkan fitur berdasarkan Johnson et al. (2021)
        $samples = $data->map(function ($item) {
            $powerVariance = $this->calculatePowerVariance($item['device_id']);
            $peakToAvgRatio = $item['max_power'] / $item['avg_power'];
            
            return [
                (float)$item['avg_power'],
                (float)$item['max_power'],
                (float)$item['usage_hours'],
                (float)$powerVariance,        // Fitur tambahan
                (float)$peakToAvgRatio       // Fitur tambahan
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
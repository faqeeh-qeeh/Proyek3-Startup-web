<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientDevice;
use App\Models\DeviceAnomaly;
use App\Services\AnomalyDetectionService;
use App\Services\DeviceClassifierService;
use Illuminate\Http\Request;
use App\Models\DeviceMonitoring;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AnomalyApiController extends Controller
{
    protected $anomalyService;
    protected $classifierService;

    public function __construct()
    {
        $this->anomalyService = new AnomalyDetectionService();
        $this->classifierService = new DeviceClassifierService();
    }

    public function show(ClientDevice $device)
    {
        if ($device->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        $device->load('classification');
        
        return response()->json([
            'success' => true,
            'data' => $device
        ]);
    }

    public function getRecentAnomalies(ClientDevice $device)
    {
        if ($device->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = DeviceMonitoring::where('device_id', $device->id)
            ->where('recorded_at', '>=', now()->subMinutes(5))
            ->whereNotNull('voltage')
            ->whereNotNull('current')
            ->whereNotNull('power')
            ->whereNotNull('energy')
            ->whereNotNull('frequency')
            ->whereNotNull('power_factor')
            ->where('power', '>', 0)
            ->orderBy('recorded_at', 'desc')
            ->get();

        $samples = $data->map(function ($item) {
            return [
                $item->voltage,
                $item->current,
                $item->power,
                $item->energy,
                $item->frequency,
                $item->power_factor
            ];
        })->toArray();

        $scores = $this->anomalyService->getAnomalyScores($samples);
        $quality = $this->calculateQuality($scores);

        return response()->json([
            'success' => true,
            'quality_score' => $quality['percentage'],
            'quality_level' => $quality['level'],
            'good_samples' => $quality['stats']['good'],
            'fair_samples' => $quality['stats']['fair'],
            'poor_samples' => $quality['stats']['poor'],
            'total_samples' => $quality['stats']['total'],
            'last_updated' => now()->toDateTimeString()
        ]);
    }

    public function index(ClientDevice $device)
    {
        if ($device->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $anomalies = DeviceAnomaly::where('device_id', $device->id)
            ->with('monitoring')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => [
                'anomalies' => $anomalies,
                'classification' => $device->classification,
            ]
        ]);
    }

    public function detectAnomalies(Request $request, ClientDevice $device)
    {
        if ($device->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // Verifikasi model
            if (!$this->anomalyService->isModelTrained()) {
                $this->trainModelForDevice($device);
                $this->anomalyService = new AnomalyDetectionService();
            }

            // Ambil data monitoring dengan 6 parameter
            $monthlyData = DeviceMonitoring::where('device_id', $device->id)
                ->where('recorded_at', '>=', now()->subDays(30))
                ->whereNotNull('voltage')
                ->whereNotNull('current')
                ->whereNotNull('power')
                ->whereNotNull('energy')
                ->whereNotNull('frequency')
                ->whereNotNull('power_factor')
                ->where('power', '>', 0)
                ->orderBy('recorded_at')
                ->get();

            if ($monthlyData->count() < 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient data for detection (min 100 samples)'
                ], 400);
            }

            // Deteksi anomali dengan 6 parameter
            $samples = $monthlyData->map(function ($item) {
                return [
                    $item->voltage,
                    $item->current,
                    $item->power,
                    $item->energy,
                    $item->frequency,
                    $item->power_factor
                ];
            })->toArray();

            $scores = $this->anomalyService->getAnomalyScores($samples);
            $quality = $this->calculateQuality($scores);

            // Simpan anomali yang terdeteksi
            $anomalies = [];
            foreach ($monthlyData as $index => $monitoring) {
                if ($scores[$index] > 0.72) { // Threshold baru 0.72
                    $anomalyType = $this->categorizeAnomaly($samples[$index], $device);
                    $severity = $this->calculateSeverity($scores[$index], 0.72);

                    $anomaly = DeviceAnomaly::updateOrCreate(
                        ['monitoring_id' => $monitoring->id],
                        [
                            'device_id' => $device->id,
                            'anomaly_score' => $scores[$index],
                            'anomaly_type' => $anomalyType,
                            'severity' => $severity,
                            'description' => $this->generateDescription($anomalyType, $severity, $samples[$index]),
                            'is_resolved' => false,
                            'detected_at' => $monitoring->recorded_at ?? now(),
                        ]
                    );
                    $anomalies[] = $anomaly;
                }
            }

            // Klasifikasi perangkat
            $this->classifyDevice($device);

            DB::commit();

            return response()->json([
                'success' => true,
                'anomalies_count' => count($anomalies),
                'quality_score' => $quality['percentage'],
                'quality_level' => $quality['level'],
                'quality_stats' => $quality['stats'],
                'classification' => $device->fresh()->classification,
                'anomalies' => $anomalies
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Anomaly detection failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Helper methods
    private function calculateQuality(array $scores): array
    {
        $total = count($scores);
        $good = count(array_filter($scores, fn($s) => $s < 0.5));
        $fair = count(array_filter($scores, fn($s) => $s >= 0.5 && $s < 0.72));
        $poor = $total - $good - $fair;

        $percentage = round(($good / $total) * 100);

        if ($percentage >= 80) $level = 'excellent';
        elseif ($percentage >= 60) $level = 'good';
        elseif ($percentage >= 40) $level = 'fair';
        else $level = 'poor';

        return [
            'percentage' => $percentage,
            'level' => $level,
            'stats' => [
                'good' => $good,
                'fair' => $fair,
                'poor' => $poor,
                'total' => $total
            ]
        ];
    }

    private function categorizeAnomaly(array $sample, ClientDevice $device): string
    {
        $voltage = $sample[0];
        $current = $sample[1];
        $power = $sample[2];
        $energy = $sample[3];
        $frequency = $sample[4];
        $powerFactor = $sample[5];

        if (abs($frequency - 50.0) > 1.0) {
            return 'frequency_deviation';
        }
        
        if ($voltage < 200 || $voltage > 250) {
            return 'voltage_anomaly';
        }
        
        if ($powerFactor < 0.7) {
            return 'power_factor_poor';
        }
        
        $theoreticalPower = $voltage * $current;
        $powerEfficiency = $power / $theoreticalPower;
        
        if ($powerEfficiency < 0.6 || $powerEfficiency > 1.1) {
            return 'power_efficiency_anomaly';
        }
        
        $expectedEnergy = $power * (1/60);
        if (abs($energy - $expectedEnergy) / $expectedEnergy > 0.3) {
            return 'energy_pattern_anomaly';
        }
        
        return 'general_anomaly';
    }

    private function calculateSeverity(float $score, float $threshold): string
    {
        $severityRatio = $score / $threshold;
        
        if ($severityRatio >= 2.0) return 'critical';
        elseif ($severityRatio >= 1.5) return 'high';
        elseif ($severityRatio >= 1.2) return 'medium';
        else return 'low';
    }

    private function generateDescription(string $type, string $severity, array $sample): string
    {
        $voltage = number_format($sample[0], 1);
        $current = number_format($sample[1], 2);
        $power = number_format($sample[2], 1);
        $energy = number_format($sample[3], 3);
        $frequency = number_format($sample[4], 1);
        $powerFactor = number_format($sample[5], 2);
        
        $descriptions = [
            'frequency_deviation' => "Deviasi frekuensi terdeteksi ({$frequency} Hz). Tegangan: {$voltage}V, Arus: {$current}A, Daya: {$power}W, Energi: {$energy}kWh, Faktor Daya: {$powerFactor}",
            'voltage_anomaly' => "Anomali tegangan terdeteksi ({$voltage}V). Arus: {$current}A, Daya: {$power}W, Energi: {$energy}kWh, Frekuensi: {$frequency}Hz, Faktor Daya: {$powerFactor}",
            'power_factor_poor' => "Faktor daya rendah terdeteksi ({$powerFactor}). Tegangan: {$voltage}V, Arus: {$current}A, Daya: {$power}W, Energi: {$energy}kWh, Frekuensi: {$frequency}Hz",
            'power_efficiency_anomaly' => "Anomali efisiensi daya terdeteksi. Tegangan: {$voltage}V, Arus: {$current}A, Daya: {$power}W, Energi: {$energy}kWh, Frekuensi: {$frequency}Hz, Faktor Daya: {$powerFactor}",
            'energy_pattern_anomaly' => "Pola konsumsi energi tidak normal ({$energy}kWh). Tegangan: {$voltage}V, Arus: {$current}A, Daya: {$power}W, Frekuensi: {$frequency}Hz, Faktor Daya: {$powerFactor}",
            'general_anomaly' => "Anomali umum terdeteksi. Tegangan: {$voltage}V, Arus: {$current}A, Daya: {$power}W, Energi: {$energy}kWh, Frekuensi: {$frequency}Hz, Faktor Daya: {$powerFactor}"
        ];
        
        $baseDescription = $descriptions[$type] ?? $descriptions['general_anomaly'];
        
        return "Tingkat: " . strtoupper($severity) . ". " . $baseDescription;
    }

    protected function classifyDevice(ClientDevice $device)
    {
        try {
            $avgData = DeviceMonitoring::where('device_id', $device->id)
                ->selectRaw('
                    AVG(voltage) as avg_voltage,
                    AVG(current) as avg_current,
                    AVG(power) as avg_power,
                    COUNT(*)/12 as usage_hours
                ')
                ->first();

            $sample = [
                (float)$avgData->avg_power,
                (float)$avgData->avg_power * 1.5,
                (float)$avgData->usage_hours
            ];

            $classification = $this->classifierService->classifyDevice($sample);

            $device->classification()->updateOrCreate(
                ['device_id' => $device->id],
                [
                    'category' => $classification['category'],
                    'confidence' => $classification['confidence'],
                    'features' => $sample
                ]
            );

        } catch (\Exception $e) {
            Log::error('Device classification failed', [
                'device_id' => $device->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function trainModelForDevice(ClientDevice $device): void
    {
        $trainingData = DeviceMonitoring::where('device_id', $device->id)
            ->whereNotNull('voltage')
            ->whereNotNull('current')
            ->whereNotNull('power')
            ->whereNotNull('energy')
            ->whereNotNull('frequency')
            ->whereNotNull('power_factor')
            ->where('power', '>', 0)
            ->orderBy('recorded_at', 'desc')
            ->limit(5000)
            ->get();
            
        if ($trainingData->count() < 100) {
            throw new \Exception("Data training tidak cukup untuk device ini (minimal 100 sampel dengan 6 parameter lengkap)");
        }
        
        $samples = $trainingData->map(function ($item) {
            return [
                (float) $item->voltage,
                (float) $item->current,
                (float) $item->power,
                (float) $item->energy,
                (float) $item->frequency,
                (float) $item->power_factor
            ];
        })->toArray();
        
        $this->anomalyService->trainModel($samples);
    }
}
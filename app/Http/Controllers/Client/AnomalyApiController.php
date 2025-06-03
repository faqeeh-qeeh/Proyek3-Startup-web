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

class AnomalyApiController extends Controller
{
    protected $anomalyService;
    protected $classifierService;

    // public function __construct()
    // {
    //     $this->anomalyService = new AnomalyDetectionService();
    // }

    // public function index(ClientDevice $device)
    // {
    //     if ($device->client_id !== auth()->id()) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $anomalies = DeviceAnomaly::where('device_id', $device->id)
    //         ->with('monitoring')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);

    //     return response()->json([
    //         'success' => true,
    //         'data' => $anomalies
    //     ]);
    // }
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
    // public function detectAnomalies(Request $request, ClientDevice $device)
    // {
    //     if ($device->client_id !== auth()->id()) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     try {
    //         // Ambil data monitoring
    //         $data = DeviceMonitoring::where('device_id', $device->id)
    //             ->where('recorded_at', '>=', now()->subDays(30))
    //             ->orderBy('recorded_at')
    //             ->get();

    //         if ($data->count() < 50) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Insufficient data for detection (min 50 samples)'
    //             ], 400);
    //         }

    //         // Deteksi anomali
    //         $samples = $data->map(function ($item) {
    //             return [
    //                 'voltage' => $item->voltage,
    //                 'current' => $item->current,
    //                 'power' => $item->power,
    //                 'frequency' => $item->frequency,
    //                 'power_factor' => $item->power_factor
    //             ];
    //         })->toArray();

    //         $scores = $this->anomalyService->getAnomalyScores($samples);
    //         $quality = $this->calculateQuality($scores);

    //         // Simpan anomali yang terdeteksi
    //         $anomalies = [];
    //         foreach ($data as $index => $monitoring) {
    //             if ($scores[$index] > 0.75) {
    //                 $anomaly = DeviceAnomaly::updateOrCreate(
    //                     ['monitoring_id' => $monitoring->id],
    //                     [
    //                         'device_id' => $device->id,
    //                         'score' => $scores[$index],
    //                         'type' => $this->determineAnomalyType($monitoring),
    //                         'description' => $this->generateDescription($monitoring, $scores[$index])
    //                     ]
    //                 );
    //                 $anomalies[] = $anomaly;
    //             }
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'anomalies_count' => count($anomalies),
    //             'quality_score' => $quality['percentage'],
    //             'quality_level' => $quality['level'],
    //             'anomalies' => $anomalies
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Anomaly detection failed',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getRecentAnomalies(ClientDevice $device)
    {
        if ($device->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = DeviceMonitoring::where('device_id', $device->id)
            ->where('recorded_at', '>=', now()->subMinutes(5))
            ->orderBy('recorded_at', 'desc')
            ->get();

        $samples = $data->map(function ($item) {
            return [
                'voltage' => $item->voltage,
                'current' => $item->current,
                'power' => $item->power,
                'frequency' => $item->frequency,
                'power_factor' => $item->power_factor
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

    // Helper methods
    private function calculateQuality(array $scores): array
    {
        $total = count($scores);
        $good = count(array_filter($scores, fn($s) => $s < 0.5));
        $fair = count(array_filter($scores, fn($s) => $s >= 0.5 && $s < 0.75));
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

    private function determineAnomalyType($monitoring): string
    {
        if ($monitoring->voltage > 250 || $monitoring->voltage < 180) return 'voltage';
        if ($monitoring->current <= 0 && $monitoring->power > 0) return 'current';
        if ($monitoring->power_factor < 0.5) return 'power_factor';
        return 'general';
    }

    private function generateDescription($monitoring, $score): string
    {
        return sprintf(
            "Anomaly detected (score: %.2f). Values: V=%.1f, I=%.3f, P=%.1f, PF=%.2f",
            $score,
            $monitoring->voltage,
            $monitoring->current,
            $monitoring->power,
            $monitoring->power_factor
        );
    }
    
    public function __construct()
    {
        $this->anomalyService = new AnomalyDetectionService();
        $this->classifierService = new DeviceClassifierService();
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

            // Ambil data monitoring
            $monthlyData = DeviceMonitoring::where('device_id', $device->id)
                ->where('recorded_at', '>=', now()->subDays(30))
                ->orderBy('recorded_at')
                ->get();

            if ($monthlyData->count() < 50) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient data for detection (min 50 samples)'
                ], 400);
            }

            // Deteksi anomali
            $samples = $monthlyData->map(function ($item) {
                return [
                    $item->voltage,
                    $item->current,
                    $item->power,
                    $item->frequency,
                    $item->power_factor
                ];
            })->toArray();

            $scores = $this->anomalyService->getAnomalyScores($samples);
            $quality = $this->calculateQuality($scores);

            // Simpan anomali yang terdeteksi
            $anomalies = [];
            foreach ($monthlyData as $index => $monitoring) {
                if ($scores[$index] > 0.75) {
                    $anomaly = DeviceAnomaly::updateOrCreate(
                        ['monitoring_id' => $monitoring->id],
                        [
                            'device_id' => $device->id,
                            'score' => $scores[$index],
                            'type' => $this->determineAnomalyType($monitoring),
                            'description' => $this->generateDescription($monitoring, $scores[$index])
                        ]
                    );
                    $anomalies[] = $anomaly;
                }
            }

            // Klasifikasi perangkat jika belum ada
            if (!$device->classification) {
                $this->classifyDevice($device);
            }

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

    protected function classifyDevice(ClientDevice $device)
    {
        $data = DeviceMonitoring::where('device_id', $device->id)
            ->where('recorded_at', '>=', now()->subDays(7))
            ->orderBy('recorded_at')
            ->get();

        if ($data->isEmpty()) {
            return;
        }

        $avgPower = $data->avg('power');
        $maxPower = $data->max('power');
        $usageHours = $data->count() / 12;

        $sample = [$avgPower, $maxPower, $usageHours];
        $result = $this->classifierService->classifyDevice($sample);

        $device->classification()->create([
            'category' => $result['category'],
            'confidence' => $result['confidence']
        ]);
    }
}
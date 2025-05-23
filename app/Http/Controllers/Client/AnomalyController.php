<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientDevice;
use App\Models\DeviceMonitoring;
use App\Models\DeviceAnomaly;
use App\Services\AnomalyDetectionService;
use App\Services\DeviceClassifierService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Datasets\CSV;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class AnomalyController extends Controller
{
    protected $anomalyService;
    protected $classifierService;

    public function __construct()
    {
        $this->middleware('auth:client');
        $this->anomalyService = new AnomalyDetectionService();
        $this->classifierService = new DeviceClassifierService();
    }

    public function index(ClientDevice $device)
    {
        if ($device->client_id !== auth('client')->id()) {
            abort(403);
        }

        $anomalies = DeviceAnomaly::where('device_id', $device->id)
            ->with('monitoring')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.devices.anomalies', compact('device', 'anomalies'));
    }
public function detectAnomalies(ClientDevice $device)
{
    // Validasi kepemilikan perangkat
    if ($device->client_id !== auth('client')->id()) {
        \Log::warning('Unauthorized anomaly detection attempt', [
            'client_id' => auth('client')->id(),
            'device_id' => $device->id
        ]);
        abort(403);
    }

    try {
        \Log::info('Starting anomaly detection for device: ' . $device->id, [
            'device_name' => $device->device_name,
            'client' => auth('client')->user()->username
        ]);

        // ==================== [1] VERIFIKASI MODEL ====================
        if (!$this->anomalyService->isModelTrained()) {
            \Log::warning('Model not trained, attempting to train...');
            
            try {
                $this->trainModelForDevice($device);
                \Log::info('Model training completed successfully');
                
                // Buat instance baru service setelah training
                $this->anomalyService = new AnomalyDetectionService();
                
                if (!$this->anomalyService->isModelTrained()) {
                    throw new \Exception("Model still not trained after training process");
                }
            } catch (\Exception $trainingException) {
                \Log::error('Model training failed', [
                    'error' => $trainingException->getMessage(),
                    'trace' => $trainingException->getTraceAsString()
                ]);
                throw new \Exception("Gagal melatih model: " . $trainingException->getMessage());
            }
        }

        // ==================== [2] PENGUMPULAN DATA ====================
        $data = DeviceMonitoring::where('device_id', $device->id)
            ->where('recorded_at', '>=', now()->subDays(30))
            ->orderBy('recorded_at')
            ->get();

        \Log::debug('Data collected for detection', [
            'count' => $data->count(),
            'date_range' => [
                'start' => $data->first()->recorded_at ?? null,
                'end' => $data->last()->recorded_at ?? null
            ]
        ]);

        if ($data->count() < 50) {
            \Log::warning('Insufficient data for detection', ['count' => $data->count()]);
            return back()->with('error', 'Data tidak cukup (minimal 50 sampel, ditemukan '.$data->count().')');
        }

        // ==================== [3] PREPROCESSING DATA ====================
        $samples = $data->map(function ($item) {
            return [
                'voltage' => $item->voltage,
                'current' => $item->current,
                'power' => $item->power,
                'frequency' => $item->frequency,
                'power_factor' => $item->power_factor
            ];
        })->toArray();

        \Log::debug('Sample data preview', [
            'first_sample' => $samples[0] ?? null,
            'last_sample' => $samples[count($samples)-1] ?? null
        ]);

        // ==================== [4] DETEKSI ANOMALI ====================
        try {
            $scores = $this->anomalyService->getAnomalyScores($samples);
            \Log::info('Anomaly scores generated', [
                'score_stats' => [
                    'min' => min($scores),
                    'max' => max($scores),
                    'avg' => array_sum($scores) / count($scores)
                ],
                'first_5_scores' => array_slice($scores, 0, 5)
            ]);
        } catch (\Exception $detectionException) {
            \Log::error('Anomaly detection failed', [
                'error' => $detectionException->getMessage(),
                'trace' => $detectionException->getTraceAsString(),
                'sample_count' => count($samples)
            ]);
            throw new \Exception("Gagal memproses deteksi: " . $detectionException->getMessage());
        }

        // ==================== [5] PENYIMPANAN HASIL ====================
        $anomaliesDetected = 0;
        $now = now();

        DB::beginTransaction();
        try {
            foreach ($data as $index => $monitoring) {
                if ($scores[$index] > 0.75) { // Threshold anomali
                    DeviceAnomaly::updateOrCreate(
                        ['monitoring_id' => $monitoring->id],
                        [
                            'device_id' => $device->id,
                            'score' => $scores[$index],
                            'type' => $this->determineAnomalyType($monitoring),
                            'description' => $this->generateAnomalyDescription($monitoring, $scores[$index]),
                            'created_at' => $now,
                            'updated_at' => $now
                        ]
                    );
                    $anomaliesDetected++;
                }
            }
            DB::commit();
            \Log::info('Anomaly results saved', ['count' => $anomaliesDetected]);
        } catch (\Exception $dbException) {
            DB::rollBack();
            \Log::error('Failed to save anomalies', [
                'error' => $dbException->getMessage(),
                'trace' => $dbException->getTraceAsString()
            ]);
            throw new \Exception("Gagal menyimpan hasil deteksi");
        }

        // ==================== [6] KLASIFIKASI PERANGKAT ====================
        if (!$device->classification) {
            try {
                $this->classifyDevice($device);
                \Log::info('Device classification completed', [
                    'category' => $device->classification->category
                ]);
            } catch (\Exception $classificationException) {
                \Log::error('Device classification failed', [
                    'error' => $classificationException->getMessage(),
                    'trace' => $classificationException->getTraceAsString()
                ]);
                // Tidak throw exception karena klasifikasi bukan critical
            }
        }

        // ==================== [7] RETURN HASIL ====================
        $message = "Deteksi selesai. Ditemukan {$anomaliesDetected} anomali.";
        \Log::info('Anomaly detection completed successfully', [
            'anomalies_detected' => $anomaliesDetected,
            'device_id' => $device->id
        ]);

        return back()->with('success', $message);

    } catch (\Exception $e) {
        \Log::error('Anomaly detection process failed', [
            'device_id' => $device->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->with('error', 'Gagal mendeteksi anomali: ' . $e->getMessage())
            ->with('debug_info', [
                'model_trained' => $this->anomalyService->isModelTrained(),
                'exception' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);
    }
}

    protected function determineAnomalyType($monitoring)
    {
        // Logika untuk menentukan jenis anomali
        if ($monitoring->voltage > 250 || $monitoring->voltage < 180) {
            return 'voltage_anomaly';
        } elseif ($monitoring->current <= 0 && $monitoring->power > 0) {
            return 'current_anomaly';
        } elseif ($monitoring->power_factor < 0.5) {
            return 'power_factor_anomaly';
        } else {
            return 'general_anomaly';
        }
    }

    protected function generateAnomalyDescription($monitoring, $score)
    {
        $desc = "Anomali terdeteksi (skor: ".number_format($score, 2)."). ";
        $desc .= "Nilai: Voltage={$monitoring->voltage}V, ";
        $desc .= "Current={$monitoring->current}A, ";
        $desc .= "Power={$monitoring->power}W, ";
        $desc .= "PF={$monitoring->power_factor}";
        
        return $desc;
    }
protected function classifyDevice(ClientDevice $device)
{
    try {
        // Ambil data untuk klasifikasi
        $data = DeviceMonitoring::where('device_id', $device->id)
            ->where('recorded_at', '>=', now()->subDays(7))
            ->orderBy('recorded_at')
            ->get();

        if ($data->isEmpty()) {
            \Log::warning('No data available for classification');
            return;
        }

        // Hitung fitur untuk klasifikasi
        $avgPower = $data->avg('power');
        $maxPower = $data->max('power');
        $usageHours = $data->count() / 12; // Asumsi data tiap 5 menit

        $sample = [$avgPower, $maxPower, $usageHours];

        // Dapatkan hasil klasifikasi
        $result = $this->classifierService->classifyDevice($sample);

        // Simpan hasil klasifikasi
        $device->classification()->create([
            'category' => $result['category'],
            'confidence' => $result['confidence']
        ]);

        \Log::info('Device classified', [
            'device_id' => $device->id,
            'category' => $result['category'],
            'confidence' => $result['confidence']
        ]);

    } catch (\Exception $e) {
        \Log::error('Device classification failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
    protected function trainModelForDevice(ClientDevice $device)
    {
        $data = DeviceMonitoring::where('device_id', $device->id)
            ->orderBy('recorded_at', 'desc')
            ->limit(1000)
            ->get();

        if ($data->count() < 50) {
            throw new \Exception("Data training tidak cukup (minimal 50 sampel)");
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

        $this->anomalyService->trainModel($samples);
    }
}
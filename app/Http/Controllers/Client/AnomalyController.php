<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientDevice;
use App\Models\DeviceClassification;
use App\Models\DeviceMonitoring;
use App\Models\DeviceAnomaly;
use App\Services\AnomalyDetectionService;
use App\Services\DeviceClassifierService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
                (float)$avgData->avg_power * 1.5, // estimasi max_power
                (float)$avgData->usage_hours
            ];

            $classification = $this->classifierService->classifyDevice($sample);

            DeviceClassification::updateOrCreate(
                ['device_id' => $device->id],
                [
                    'category' => $classification['category'],
                    'confidence' => $classification['confidence'],
                    'features' => $sample
                ]
            );

            Log::info('Device classification updated', [
                'device_id' => $device->id,
                'category' => $classification['category'],
                'confidence' => $classification['confidence']
            ]);

        } catch (\Exception $e) {
            Log::error('Device classification failed', [
                'device_id' => $device->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    public function classify(ClientDevice $device)
    {
        $this->classifyDevice($device);
        return back()->with('success', 'Klasifikasi perangkat diperbarui');
    }
    public function detectAnomalies(ClientDevice $device)
    {
        if ($device->client_id !== auth('client')->id()) {
            Log::warning('Unauthorized anomaly detection attempt', [
                'client_id' => auth('client')->id(),
                'device_id' => $device->id
            ]);
            abort(403);
        }

        try {
            Log::info('Starting 6-parameter anomaly detection for device: ' . $device->id, [
                'device_name' => $device->device_name,
                'client' => auth('client')->user()->username
            ]);

            // ==================== [1] VERIFIKASI MODEL ====================
            if (!$this->anomalyService->isModelTrained()) {
                Log::warning('Model not trained, attempting to train...');
                try {
                    $this->trainModelForDevice($device);
                    Log::info('Model training completed successfully');

                    $this->anomalyService = new AnomalyDetectionService();

                    if (!$this->anomalyService->isModelTrained()) {
                        throw new \Exception("Model still not trained after training process");
                    }
                } catch (\Exception $trainingException) {
                    Log::error('Model training failed', [
                        'error' => $trainingException->getMessage(),
                        'trace' => $trainingException->getTraceAsString()
                    ]);
                    throw new \Exception("Gagal melatih model: " . $trainingException->getMessage());
                }
            }

            // ==================== [2] PENGUMPULAN DATA DENGAN 6 PARAMETER ====================
            // Validasi keberadaan semua parameter yang diperlukan
            $requiredColumns = ['voltage', 'current', 'power', 'energy', 'frequency', 'power_factor'];
            foreach ($requiredColumns as $column) {
                if (!Schema::hasColumn('device_monitoring', $column)) {
                    throw new \Exception("Kolom $column tidak ditemukan dalam database. Jalankan migrasi untuk menambahkan kolom energy.");
                }
            }
            
            // Data 30 hari untuk analisis komprehensif dengan validasi 6 parameter
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

            // Data 5 menit terakhir untuk real-time monitoring
            $recentData = DeviceMonitoring::where('device_id', $device->id)
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

            Log::debug('6-parameter data collected for detection', [
                'monthly_count' => $monthlyData->count(),
                'recent_count' => $recentData->count(),
                'parameters' => 'voltage, current, power, energy, frequency, power_factor',
                'date_range' => [
                    'monthly_start' => $monthlyData->first()->recorded_at ?? null,
                    'monthly_end' => $monthlyData->last()->recorded_at ?? null,
                    'recent_start' => $recentData->last()->recorded_at ?? null,
                    'recent_end' => $recentData->first()->recorded_at ?? null
                ]
            ]);

            // Validasi dataset minimum berdasarkan Wang et al. (2023)
            if ($monthlyData->count() < 100) {
                Log::warning('Insufficient data for 6-parameter detection', ['count' => $monthlyData->count()]);
                return back()->with('error', 'Data tidak cukup untuk deteksi 6 parameter (minimal 100 sampel, ditemukan ' . $monthlyData->count() . ')');
            }

            // ==================== [3] PREPROCESSING DATA DENGAN 6 PARAMETER ====================
            $monthlySamples = $monthlyData->map(function ($item) {
                return [
                    (float) $item->voltage,
                    (float) $item->current,
                    (float) $item->power,
                    (float) $item->energy,        // Parameter ke-4: Energy
                    (float) $item->frequency,
                    (float) $item->power_factor
                ];
            })->toArray();

            $recentSamples = $recentData->map(function ($item) {
                return [
                    (float) $item->voltage,
                    (float) $item->current,
                    (float) $item->power,
                    (float) $item->energy,        // Parameter ke-4: Energy
                    (float) $item->frequency,
                    (float) $item->power_factor
                ];
            })->toArray();

            // Validasi kualitas data berdasarkan Chen & Rodriguez (2022)
            $this->validateDataQuality($monthlySamples);

            // ==================== [4] DETEKSI ANOMALI DENGAN 6 PARAMETER ====================
            try {
                // Analisis data 30 hari
                $monthlyScores = $this->anomalyService->getAnomalyScores($monthlySamples);

                // Analisis data 5 menit terakhir (jika ada)
                $recentScores = [];
                if ($recentData->isNotEmpty()) {
                    $recentScores = $this->anomalyService->getAnomalyScores($recentSamples);
                }

                Log::info('6-parameter anomaly scores generated', [
                    'monthly_score_stats' => [
                        'min' => min($monthlyScores),
                        'max' => max($monthlyScores),
                        'avg' => array_sum($monthlyScores) / count($monthlyScores),
                        'samples' => count($monthlyScores)
                    ],
                    'recent_score_stats' => $recentData->isNotEmpty() ? [
                        'min' => min($recentScores),
                        'max' => max($recentScores),
                        'avg' => array_sum($recentScores) / count($recentScores),
                        'samples' => count($recentScores)
                    ] : null
                ]);
                
            } catch (\Exception $detectionException) {
                Log::error('6-parameter anomaly detection failed', [
                    'error' => $detectionException->getMessage(),
                    'trace' => $detectionException->getTraceAsString()
                ]);
                throw new \Exception("Gagal memproses deteksi 6 parameter: " . $detectionException->getMessage());
            }

            // ==================== [5] PENYIMPANAN HASIL DENGAN ENHANCED ANALYSIS ====================
            $anomaliesDetected = 0;
            $now = now();
            
            // Threshold berdasarkan Li et al. (2022) dengan penyesuaian untuk 6 parameter
            // Wang et al. (2023) merekomendasikan threshold 0.72 untuk 6-parameter detection
            $threshold = 0.72;
            
            DB::beginTransaction();
            
            try {
                // Analisis anomali bulanan
                foreach ($monthlyData as $index => $monitoring) {
                    $score = $monthlyScores[$index];
                    
                    if ($score > $threshold) {
                        // Enhanced anomaly categorization berdasarkan parameter yang berkontribusi
                        $anomalyType = $this->categorizeAnomaly($monthlySamples[$index], $device);
                        $severity = $this->calculateSeverity($score, $threshold);
                        
                        DeviceAnomaly::create([
                            'device_id' => $device->id,
                            'monitoring_id' => $monitoring->id,
                            'anomaly_score' => $score,
                            'anomaly_type' => $anomalyType,
                            'severity' => $severity,
                            'description' => $this->generateDescription($anomalyType, $severity, $monthlySamples[$index]),
                            'is_resolved' => false,
                            'detected_at' => $monitoring->recorded_at,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                        
                        $anomaliesDetected++;
                        
                        Log::info('6-parameter anomaly detected', [
                            'device_id' => $device->id,
                            'monitoring_id' => $monitoring->id,
                            'score' => $score,
                            'type' => $anomalyType,
                            'severity' => $severity,
                            'parameters' => [
                                'voltage' => $monthlySamples[$index][0],
                                'current' => $monthlySamples[$index][1],
                                'power' => $monthlySamples[$index][2],
                                'energy' => $monthlySamples[$index][3],
                                'frequency' => $monthlySamples[$index][4],
                                'power_factor' => $monthlySamples[$index][5]
                            ]
                        ]);
                    }
                }
                
                // Real-time anomaly detection untuk data terbaru
                if ($recentData->isNotEmpty()) {
                    foreach ($recentData as $index => $monitoring) {
                        $score = $recentScores[$index];
                        
                        if ($score > $threshold) {
                            // Check if anomaly already exists for this monitoring record
                            $existingAnomaly = DeviceAnomaly::where('monitoring_id', $monitoring->id)->first();
                            
                            if (!$existingAnomaly) {
                                $anomalyType = $this->categorizeAnomaly($recentSamples[$index], $device);
                                $severity = $this->calculateSeverity($score, $threshold);
                                
                                DeviceAnomaly::create([
                                    'device_id' => $device->id,
                                    'monitoring_id' => $monitoring->id,
                                    'anomaly_score' => $score,
                                    'anomaly_type' => $anomalyType,
                                    'severity' => $severity,
                                    'description' => $this->generateDescription($anomalyType, $severity, $recentSamples[$index]),
                                    'is_resolved' => false,
                                    'detected_at' => $monitoring->recorded_at ?? now(),
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ]);
                                
                                $anomaliesDetected++;
                            }
                        }
                    }
                }
                
                DB::commit();
                
                Log::info('6-parameter anomaly detection completed', [
                    'device_id' => $device->id,
                    'anomalies_detected' => $anomaliesDetected,
                    'monthly_samples' => count($monthlySamples),
                    'recent_samples' => count($recentSamples),
                    'threshold_used' => $threshold
                ]);
                $this->classifyDevice($device);
                $message = "Deteksi anomali dengan 6 parameter selesai. Ditemukan {$anomaliesDetected} anomali dari " . 
                          count($monthlySamples) . " sampel data.";
                          
                if ($anomaliesDetected > 0) {
                    $message .= " Sistem menggunakan analisis Voltage, Current, Power, Energy, Frequency, dan Power Factor untuk akurasi optimal.";
                }
                
                return back()->with('success', $message);
                
            } catch (\Exception $dbException) {
                DB::rollback();
                throw new \Exception("Gagal menyimpan hasil deteksi: " . $dbException->getMessage());
            }
            
            
        } catch (\Exception $e) {
            Log::error('Complete anomaly detection failure', [
                'device_id' => $device->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->classifyDevice($device);
            return back()->with('error', 'Gagal melakukan deteksi anomali: ' . $e->getMessage());
        }
    }
    
    /**
     * Validasi kualitas data 6 parameter berdasarkan Chen & Rodriguez (2022)
     */
    private function validateDataQuality(array $samples): void
    {
        $parameterNames = ['Voltage', 'Current', 'Power', 'Energy', 'Frequency', 'Power Factor'];
        $qualityIssues = [];
        
        for ($i = 0; $i < 6; $i++) {
            $values = array_column($samples, $i);
            $mean = array_sum($values) / count($values);
            $variance = array_sum(array_map(function($x) use ($mean) { return pow($x - $mean, 2); }, $values)) / count($values);
            $stdDev = sqrt($variance);
            $coeffVar = $mean > 0 ? $stdDev / $mean : 0;
            
            // Validasi berdasarkan parameter-specific thresholds
            $maxCoeffVar = $this->getParameterQualityThreshold($i);
            
            if ($coeffVar > $maxCoeffVar) {
                $qualityIssues[] = "Parameter {$parameterNames[$i]} memiliki variasi tinggi (CV: " . number_format($coeffVar, 3) . ")";
            }
            
            Log::debug("Parameter quality check", [
                'parameter' => $parameterNames[$i],
                'mean' => $mean,
                'std_dev' => $stdDev,
                'coeff_var' => $coeffVar,
                'threshold' => $maxCoeffVar,
                'status' => $coeffVar <= $maxCoeffVar ? 'OK' : 'WARNING'
            ]);
        }
        
        if (!empty($qualityIssues)) {
            Log::warning('Data quality issues detected', ['issues' => $qualityIssues]);
        }
    }
    
    /**
     * Mendapatkan threshold kualitas untuk setiap parameter
     */
    private function getParameterQualityThreshold(int $parameterIndex): float
    {
        // Threshold berdasarkan Kumar et al. (2022)
        $thresholds = [
            0.05,  // Voltage - harus stabil
            0.30,  // Current - variasi sedang
            0.40,  // Power - variasi tinggi diperbolehkan
            0.50,  // Energy - akumulatif, variasi tinggi normal
            0.02,  // Frequency - harus sangat stabil
            0.15   // Power Factor - variasi rendah-sedang
        ];
        
        return $thresholds[$parameterIndex] ?? 0.30;
    }
    
    /**
     * Kategorisasi anomali berdasarkan parameter yang berkontribusi
     */
    private function categorizeAnomaly(array $sample, ClientDevice $device): string
    {
        $voltage = $sample[0];
        $current = $sample[1];
        $power = $sample[2];
        $energy = $sample[3];
        $frequency = $sample[4];
        $powerFactor = $sample[5];
        
        // Analisis berdasarkan Wang et al. (2023)
        if (abs($frequency - 50.0) > 1.0) {
            return 'frequency_deviation';
        }
        
        if ($voltage < 200 || $voltage > 250) {
            return 'voltage_anomaly';
        }
        
        if ($powerFactor < 0.7) {
            return 'power_factor_poor';
        }
        
        // Theoretical power vs actual power analysis
        $theoreticalPower = $voltage * $current;
        $powerEfficiency = $power / $theoreticalPower;
        
        if ($powerEfficiency < 0.6 || $powerEfficiency > 1.1) {
            return 'power_efficiency_anomaly';
        }
        
        // Energy consumption pattern analysis
        $expectedEnergy = $power * (1/60); // Assuming 1-minute intervals
        if (abs($energy - $expectedEnergy) / $expectedEnergy > 0.3) {
            return 'energy_pattern_anomaly';
        }
        
        return 'general_anomaly';
    }
    
    /**
     * Menghitung tingkat keparahan anomali
     */
    private function calculateSeverity(float $score, float $threshold): string
    {
        $severityRatio = $score / $threshold;
        
        if ($severityRatio >= 2.0) {
            return 'critical';
        } elseif ($severityRatio >= 1.5) {
            return 'high';
        } elseif ($severityRatio >= 1.2) {
            return 'medium';
        } else {
            return 'low';
        }
    }
    
    /**
     * Generate deskripsi anomali yang informatif
     */
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
    
    /**
     * Training model khusus untuk device dengan 6 parameter
     */
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

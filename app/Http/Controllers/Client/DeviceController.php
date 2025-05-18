<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientDevice;
use App\Models\DeviceMonitoring;
use Illuminate\Http\Request;
use App\Services\MqttService;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    public function index()
    {
        $devices = auth('client')->user()->devices()
            ->with(['product', 'order'])
            ->latest()
            ->paginate(10);
            
        return view('client.devices.index', compact('devices'));
    }

    public function show(ClientDevice $device)
    {
        if ($device->client_id !== auth('client')->id()) {
            abort(403);
        }

        $device->load(['product', 'order']);
        
        // Ambil data terakhir untuk preview awal
        $latestData = DeviceMonitoring::where('device_id', $device->id)
            ->orderBy('recorded_at', 'desc')
            ->first();
            
        return view('client.devices.show', compact('device', 'latestData'));
    }

    public function controlDevice(Request $request, ClientDevice $device)
    {
        if ($device->client_id !== auth('client')->id()) {
            abort(403);
        }
    
        $request->validate([
            'command' => 'required|in:on,off',
            'channel' => 'required|integer|min:1|max:4',
        ]);
    
        $mqttService = new MqttService();
        
        $message = json_encode([
            'command' => $request->command,
            'channel' => $request->channel,
            'timestamp' => now()->toDateTimeString(),
        ]);
    
        try {
            $mqttService->publish($device->mqtt_topic . '/control', $message);
            return back()->with('success', 'Command sent successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send command: ' . $e->getMessage());
        }
    }
// app/Http/Controllers/Client/DeviceController.php

    public function getLatestData(ClientDevice $device)
    {
        if ($device->client_id !== auth('client')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Ambil 1 data terbaru dari database
        $data = DeviceMonitoring::where('device_id', $device->id)
            ->orderBy('recorded_at', 'desc')
            ->first();
    
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data belum tersedia'
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => [
                'voltage' => $data->voltage,
                'current' => $data->current,
                'power' => $data->power,
                'energy' => $data->energy,
                'frequency' => $data->frequency,
                'power_factor' => $data->power_factor,
                'timestamp' => $data->recorded_at->getTimestamp()
            ],
            'last_updated' => $data->recorded_at->toIso8601String()
        ]);
    }
    public function getRelayStatus(ClientDevice $device)
    {
        if ($device->client_id !== auth('client')->id()) {
            abort(403);
        }

        // Ini contoh - dalam implementasi nyata bisa ambil dari database atau MQTT
        return response()->json([
            'relays' => [
                ['channel' => 1, 'status' => 'off'],
                ['channel' => 2, 'status' => 'off'],
                ['channel' => 3, 'status' => 'off'],
                ['channel' => 4, 'status' => 'off']
            ],
            'last_updated' => now()->toDateTimeString()
        ]);
    }
    public function getMonitoringData(ClientDevice $device)
    {
        if ($device->client_id !== auth('client')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ambil 10 data terbaru untuk chart
        $monitoringData = DeviceMonitoring::where('device_id', $device->id)
            ->latest('recorded_at')
            ->take(10)
            ->get()
            ->reverse();

        $labels = [];
        $voltage = [];
        $current = [];
        $power = [];
        $energy = [];
        $frequency = [];
        $powerFactor = [];

        foreach ($monitoringData as $data) {
            $labels[] = Carbon::parse($data->recorded_at)->format('H:i:s');
            $voltage[] = $data->voltage;
            $current[] = $data->current;
            $power[] = $data->power;
            $energy[] = $data->energy;
            $frequency[] = $data->frequency;
            $powerFactor[] = $data->power_factor;
        }

        $latestData = $monitoringData->last();

        return response()->json([
            'success' => true,
            'chart' => [
                'labels' => $labels,
                'voltage' => $voltage,
                'current' => $current,
                'power' => $power,
                'energy' => $energy,
                'frequency' => $frequency,
                'power_factor' => $powerFactor
            ],
            'latest' => [
                'voltage' => $latestData->voltage ?? 0,
                'current' => $latestData->current ?? 0,
                'power' => $latestData->power ?? 0,
                'energy' => $latestData->energy ?? 0,
                'frequency' => $latestData->frequency ?? 0,
                'power_factor' => $latestData->power_factor ?? 0,
                'timestamp' => $latestData->recorded_at ?? now()
            ]
        ]);
    }
}
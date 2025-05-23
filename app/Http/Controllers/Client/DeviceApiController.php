<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientDevice;
use App\Models\DeviceMonitoring;
use Illuminate\Http\Request;
use App\Services\MqttService;

class DeviceApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function index()
    {
        $devices = auth()->user()->devices()
            ->with(['product', 'order', 'latestMonitoringData'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $devices->map(function ($device) {
                return [
                    'id' => $device->id,
                    'client_id' => $device->client_id,
                    'order_id' => $device->order_id,
                    'product_id' => $device->product_id,
                    'mqtt_topic' => $device->mqtt_topic,
                    'device_name' => $device->device_name,
                    'status' => $device->status,
                    'description' => $device->description,
                    'product' => $device->product,
                    'order' => $device->order,
                    'latest_monitoring_data' => $device->latestMonitoringData,
                ];
            })
        ]);
    }

    public function show(ClientDevice $device)
    {
        if ($device->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $device->load(['product', 'order', 'latestMonitoringData']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $device->id,
                'client_id' => $device->client_id,
                'order_id' => $device->order_id,
                'product_id' => $device->product_id,
                'mqtt_topic' => $device->mqtt_topic,
                'device_name' => $device->device_name,
                'status' => $device->status,
                'description' => $device->description,
                'product' => $device->product,
                'order' => $device->order,
                'latest_monitoring_data' => $device->latestMonitoringData,
            ]
        ]);
    }
    public function getLatestData(ClientDevice $device)
    {
        if ($device->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = DeviceMonitoring::where('device_id', $device->id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'No data available'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getMonitoringData(ClientDevice $device)
    {
        if ($device->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = DeviceMonitoring::where('device_id', $device->id)
            ->orderBy('recorded_at', 'desc')
            ->take(10)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getRelayStatus(ClientDevice $device)
    {
        if ($device->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ini contoh - dalam implementasi nyata bisa ambil dari database atau MQTT
        return response()->json([
            'relays' => [
                ['channel' => 1, 'status' => 'off'],
                ['channel' => 2, 'status' => 'off'],
                ['channel' => 3, 'status' => 'off'],
                ['channel' => 4, 'status' => 'off']
            ]
        ]);
    }

    public function controlDevice(Request $request, ClientDevice $device)
    {
        if ($device->client_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send command'
            ], 500);
        }
    }
    
}
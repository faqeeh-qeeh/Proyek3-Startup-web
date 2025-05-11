<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientDevice;
use App\Models\DeviceData;
use Illuminate\Http\Request;

class DeviceHistoryController extends Controller
{
    public function index(ClientDevice $device)
    {
        if ($device->client_id !== auth('client')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = DeviceData::where('device_id', $device->id)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json($data);
    }

    public function store(Request $request, ClientDevice $device)
    {
        // Endpoint ini akan dipanggil oleh ESP32 untuk menyimpan data
        $validated = $request->validate([
            'voltage' => 'nullable|numeric',
            'current' => 'nullable|numeric',
            'power' => 'nullable|numeric',
            'energy' => 'nullable|numeric',
            'frequency' => 'nullable|numeric',
            'pf' => 'nullable|numeric',
        ]);

        $device->data()->create($validated);

        return response()->json(['message' => 'Data saved']);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientDevice;
use App\Models\DeviceMonitoring;
use Illuminate\Http\Request;

class DeviceMonitoringController extends Controller
{
    public function getLatestData($deviceId)
    {
        $device = ClientDevice::findOrFail($deviceId);
        
        // Pastikan device milik user yang login
        if ($device->client_id !== auth('client')->id()) {
            abort(403);
        }
        
        $data = DeviceMonitoring::where('device_id', $deviceId)
            ->orderBy('recorded_at', 'desc')
            ->first();
            
        return response()->json([
            'data' => $data,
            'last_updated' => $device->last_data_received
        ]);
    }
    
    public function getHistoricalData($deviceId, Request $request)
    {
        $device = ClientDevice::findOrFail($deviceId);
        
        if ($device->client_id !== auth('client')->id()) {
            abort(403);
        }
        
        $limit = $request->get('limit', 100);
        
        return DeviceMonitoring::where('device_id', $deviceId)
            ->orderBy('recorded_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
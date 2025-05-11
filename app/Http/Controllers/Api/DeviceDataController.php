<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceDataController extends Controller
{
    public function getData(ClientDevice $device)
    {
        if ($device->client_id !== Auth::guard('client')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = cache()->get('device-data-' . $device->id, [
            'voltage' => 0,
            'current' => 0,
            'power' => 0,
            'energy' => 0,
            'frequency' => 0,
            'pf' => 0,
            'timestamp' => now()->toDateTimeString()
        ]);

        return response()->json($data);
    }
}
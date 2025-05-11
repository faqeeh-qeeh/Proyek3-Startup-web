<?php

use App\Models\ClientDevice;
use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('device-data.{deviceId}', function ($user, $deviceId) {
//     $device = ClientDevice::findOrFail($deviceId);
//     return $user->id === $device->client_id;
// });

Broadcast::channel('device.{deviceId}', function ($user, $deviceId) {
    return (int) $user->id === (int) ClientDevice::findOrFail($deviceId)->client_id;
});
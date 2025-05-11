<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceDataUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $deviceId;
    public $data;

public function __construct($deviceId, $data)
{
    \Log::info("DeviceDataUpdated event constructed", [
        'device_id' => $deviceId,
        'data' => $data
    ]);

    $this->deviceId = $deviceId;
    $this->data = $data;
}


    public function broadcastOn()
    {
        \Log::info("Broadcasting to channel: device.{$this->deviceId}");
        return new Channel('device.' . $this->deviceId);
    }

    public function broadcastAs()
    {
        return 'device.data.updated';
    }
    public function broadcastWith()
    {
        return [
            'device_id' => $this->deviceId,
            'data' => $this->data,
            'server_time' => now()->toDateTimeString()
        ];
    }
}
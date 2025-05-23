<?php

// namespace App\Events;

// use Illuminate\Broadcasting\Channel;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;

// class DeviceDataUpdated implements ShouldBroadcast
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public $deviceId;
//     public $data;

//     /**
//      * Create a new event instance.
//      */
//     public function __construct($deviceId, array $data)
//     {
//         $this->deviceId = $deviceId;
//         $this->data = $data;
//     }

//     /**
//      * Get the channels the event should broadcast on.
//      */
//     public function broadcastOn(): Channel
//     {
//         return new Channel('device.' . $this->deviceId);
//     }

//     /**
//      * The event's broadcast name.
//      */
//     public function broadcastAs(): string
//     {
//         return 'device.data.updated';
//     }

//     /**
//      * Get the data to broadcast.
//      */
//     public function broadcastWith(): array
//     {
//         return [
//             'device_id' => $this->deviceId,
//             'data' => $this->data,
//             'timestamp' => now()->toDateTimeString()
//         ];
//     }
// }
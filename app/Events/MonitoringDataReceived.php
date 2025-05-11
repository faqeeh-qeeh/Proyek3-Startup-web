<?php

// namespace App\Events;

// use Illuminate\Broadcasting\Channel;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Log;


// class MonitoringDataReceived implements ShouldBroadcast
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public $data;
//     public $deviceId;

//     public function __construct($deviceId, $data)
//     {
//         \Log::info("Creating MonitoringDataReceived event", [
//             'deviceId' => $deviceId,
//             'data' => $data
//         ]);
        
//         $this->deviceId = $deviceId;
//         $this->data = array_merge([
//             'voltage' => 0,
//             'current' => 0,
//             'power' => 0,
//             'energy' => 0,
//             'frequency' => 0,
//             'pf' => 0,
//         ], $data);
        
//         \Log::debug("Event data prepared", $this->data);
//     }

//     public function broadcastOn()
//     {
//         return new Channel(str_replace('/', '.', $this->deviceId).'.monitoring');
//     }

//     public function broadcastAs()
//     {
//         return 'monitoring.data';
//     }
// }
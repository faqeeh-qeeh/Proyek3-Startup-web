<?php

// namespace App\Services;

// use PhpMqtt\Client\MqttClient;
// use App\Events\DeviceDataUpdated;
// use App\Models\ClientDevice;

// class MqttListener
// {
//     public function listen()
//     {
//         $server = 'mqtt.my.id';
//         $port = 1883;
//         $clientId = 'laravel-listener-' . uniqid();
        
//         $mqtt = new MqttClient($server, $port, $clientId);
        
//         $mqtt->connect(null, true);
        
//         // Subscribe ke semua device monitoring topic
//         $devices = ClientDevice::where('status', 'active')->get();
        
//         foreach ($devices as $device) {
//             $topic = $device->mqtt_topic . '/monitoring';
//             $mqtt->subscribe($topic, function (string $topic, string $message) {
//                 $this->handleMessage($topic, $message);
//             }, 0);
//         }
        
//         $mqtt->loop(true);
//     }
    
//     protected function handleMessage(string $topic, string $message)
//     {
//         $data = json_decode($message, true);
        
//         // Extract device ID dari topic
//         $parts = explode('/', $topic);
//         $deviceIdentifier = $parts[count($parts)-2]; // ambil 'wahyu/device1'
        
//         $device = ClientDevice::where('mqtt_topic', 'like', '%'.$deviceIdentifier.'%')->first();
        
//         if ($device) {
//             // Simpan ke database
//             $device->monitoringData()->create([
//                 'voltage' => $data['voltage'],
//                 'current' => $data['current'],
//                 'power' => $data['power'],
//                 'energy' => $data['energy'],
//                 'frequency' => $data['Frequency'],
//                 'power_factor' => $data['pf']
//             ]);
            
//             // Broadcast ke frontend
//             event(new DeviceDataUpdated($device->id, $data));
//         }
//     }
// }
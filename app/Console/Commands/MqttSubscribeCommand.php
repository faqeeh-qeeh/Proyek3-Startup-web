<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use App\Services\MqttService;
// use App\Models\ClientDevice;

// class MqttSubscribeCommand extends Command
// {
//     protected $signature = 'mqtt:subscribe';
//     protected $description = 'Subscribe to MQTT topics';

//     public function handle()
//     {
//         $mqttService = new MqttService();
        
//         // Subscribe ke semua device yang aktif
//         $devices = ClientDevice::where('status', 'active')->get();
        
//         foreach ($devices as $device) {
//             $topic = $device->mqtt_topic . '/monitoring';
            
//             $mqttService->subscribe($topic, function ($topic, $message) {
//                 $data = json_decode($message, true);
//                 $this->processMonitoringData($topic, $data);
//             });
            
//             $this->info("Subscribed to: " . $topic);
//         }
        
//         $mqttService->loop();
//     }
    
//     protected function processMonitoringData($topic, $data)
//     {
//         // Simpan ke database atau broadcast ke frontend
//         $deviceTopic = str_replace('/monitoring', '', $topic);
//         $device = ClientDevice::where('mqtt_topic', $deviceTopic)->first();
        
//         if ($device) {
//             // Simpan data monitoring
//             $device->update([
//                 'last_voltage' => $data['voltage'],
//                 'last_current' => $data['current'],
//                 'last_power' => $data['power'],
//                 'last_energy' => $data['energy'],
//                 'last_frequency' => $data['Frequency'],
//                 'last_pf' => $data['pf'],
//                 'last_data_received' => now()
//             ]);
            
//             // Broadcast ke frontend menggunakan Pusher atau WebSocket
//             event(new \App\Events\DeviceDataUpdated($device, $data));
//         }
//     }
// }
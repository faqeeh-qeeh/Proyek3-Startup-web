<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use PhpMqtt\Client\MqttClient;
// use PhpMqtt\Client\ConnectionSettings;
// use App\Models\ClientDevice;
// use App\Models\DeviceMonitoringData;
// use Illuminate\Support\Facades\Log;

// class MqttSubscriberCommand extends Command
// {
//     protected $signature = 'mqtt:subscribe';
//     protected $description = 'Subscribe to MQTT topics and save to database';

//     public function handle()
//     {
//         try {
//             $server = 'mqtt.my.id';
//             $port = 1883;
//             $clientId = 'laravel-subscriber-' . uniqid();
            
//             $mqtt = new MqttClient($server, $port, $clientId);
            
//             $connectionSettings = (new ConnectionSettings)
//                 ->setUsername(null)
//                 ->setPassword(null);
                
//             $mqtt->connect($connectionSettings, true);
            
//             Log::info("MQTT Subscriber connected to broker.");
            
//             $mqtt->subscribe('/project/startup/client/#', function ($topic, $message) {
//                 if (str_ends_with($topic, '/monitoring')) {
//                     $data = json_decode($message, true);
                    
//                     Log::debug("Data received", ['topic' => $topic, 'data' => $data]);
                    
//                     if (json_last_error() !== JSON_ERROR_NONE) {
//                         Log::warning("Invalid JSON: " . $message);
//                         return;
//                     }
                    
//                     $device = ClientDevice::where('mqtt_topic', $topic)->first();
                    
//                     if (!$device) {
//                         Log::warning("Device not found for topic: " . $topic);
//                         return;
//                     }
                    
//                     try {
//                         $monitoringData = DeviceMonitoringData::create([
//                             'device_id' => $device->id,
//                             'voltage' => $data['voltage'] ?? 0,
//                             'current' => $data['current'] ?? 0,
//                             'power' => $data['power'] ?? 0,
//                             'energy' => $data['energy'] ?? 0,
//                             'frequency' => $data['frequency'] ?? 0,
//                             'pf' => $data['pf'] ?? 0
//                         ]);
                        
//                         Log::info("Data saved. ID: " . $monitoringData->id);
//                     } catch (\Exception $e) {
//                         Log::error("Database Error: " . $e->getMessage());
//                     }
//                 }
//             }, 1);
            
//             $mqtt->loop(true);
//         } catch (\Exception $e) {
//             Log::error("MQTT Subscriber crashed: " . $e->getMessage());
//             $this->error("Error: " . $e->getMessage());
//         }
//     }
// }
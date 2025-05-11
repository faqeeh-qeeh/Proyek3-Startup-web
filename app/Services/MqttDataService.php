<?php

// namespace App\Services;

// use PhpMqtt\Client\MqttClient;
// use App\Models\ClientDevice;
// use Illuminate\Support\Facades\Log;

// class MqttDataService
// {
//     protected $mqtt;
//     protected $broker = 'mqtt.my.id';
//     protected $port = 1883;

//     public function __construct()
//     {
//         $this->mqtt = new MqttClient($this->broker, $this->port, 'laravel-server-' . uniqid());
        
//         $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)
//             ->setKeepAliveInterval(60);
            
//         $this->mqtt->connect($connectionSettings, true);
//     }

//     public function subscribeToDevices()
//     {
//         // Subscribe ke semua topic monitoring perangkat
//         $devices = ClientDevice::all();
        
//         foreach ($devices as $device) {
//             $topic = $device->mqtt_topic . '/monitoring';
//             $this->mqtt->subscribe($topic, function ($topic, $message) use ($device) {
//                 $this->processDeviceData($device, $message);
//             }, 1);
//         }
        
//         $this->mqtt->loop(true);
//     }

//     protected function processDeviceData(ClientDevice $device, $message)
//     {
//         try {
//             $data = json_decode($message, true);
            
//             // Pastikan semua field ada
//             $fullData = array_merge([
//                 'voltage' => 0,
//                 'current' => 0,
//                 'power' => 0,
//                 'energy' => 0,
//                 'frequency' => 0,
//                 'pf' => 0,
//                 'timestamp' => now()->toDateTimeString(),
//             ], $data);
            
//             // Simpan data ke cache
//             cache()->put('device-data-' . $device->id, $fullData, now()->addMinutes(5));
            
//             // Broadcast event
//             event(new \App\Events\DeviceDataUpdated($device->id, $fullData));
            
//             Log::info("Data received from {$device->mqtt_topic}: " . json_encode($fullData));
//         } catch (\Exception $e) {
//             Log::error("Error processing MQTT data: " . $e->getMessage());
//         }
//     }
// }

// namespace App\Services;

// use PhpMqtt\Client\MqttClient;
// use App\Events\DeviceDataUpdated;
// use App\Models\ClientDevice;
// use Illuminate\Support\Facades\Log;

// class MqttDataService
// {
//     protected $mqtt;
//     protected $broker = 'mqtt.my.id';
//     protected $port = 1883;

//     public function __construct()
//     {
//         $this->mqtt = new MqttClient($this->broker, $this->port, 'laravel-server-' . uniqid());
        
//         $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)
//             ->setKeepAliveInterval(60);
            
//         $this->mqtt->connect($connectionSettings, true);
//     }

//     public function subscribeToDevices()
//     {
//         $devices = ClientDevice::all();
        
//         foreach ($devices as $device) {
//             $topic = $device->mqtt_topic . '/monitoring';
            
//             $this->mqtt->subscribe($topic, function ($topic, $message) use ($device) {
//                 try {
//                     $data = json_decode($message, true);
                    
//                     if (json_last_error() !== JSON_ERROR_NONE) {
//                         throw new \Exception("Invalid JSON format");
//                     }

//                     // Validasi data minimum
//                     if (!isset($data['voltage'])) {
//                         throw new \Exception("Missing required fields");
//                     }

//                     // Simpan ke cache
//                     cache()->put('device-data-' . $device->id, $data, now()->addMinutes(5));

//                     // Trigger event
//                     event(new DeviceDataUpdated($device->id, $data));
                    
//                     Log::debug("Data processed for device {$device->id}");

//                 } catch (\Exception $e) {
//                     Log::error("MQTT Processing Error: " . $e->getMessage());
//                 }
//             }, 1);
//         }
        
//         $this->mqtt->loop(true);
//     }
//     protected function processDeviceData(ClientDevice $device, $message)
//     {
//         try {
//             $data = json_decode($message, true);
            
//             // Normalisasi field names (ubah Frequency menjadi frequency)
//             if (isset($data['Frequency'])) {
//                 $data['frequency'] = $data['Frequency'];
//                 unset($data['Frequency']);
//             }
            
//             // Validasi data
//             $requiredFields = ['voltage', 'current', 'power', 'energy', 'frequency', 'pf'];
//             foreach ($requiredFields as $field) {
//                 if (!isset($data[$field])) {
//                     throw new \Exception("Missing field: {$field}");
//                 }
//                 $data[$field] = (float)$data[$field];
//             }
        
//             $data['timestamp'] = now()->toDateTimeString();
            
//             // Debug log
//             Log::debug("Processing data for device {$device->id}:", $data);
            
//             // Simpan ke cache
//             cache()->put('device-data-' . $device->id, $data, now()->addMinutes(5));
            
//             // Trigger event
//             event(new DeviceDataUpdated($device->id, $data));
            
//             return true;
//         } catch (\Exception $e) {
//             Log::error("Error processing MQTT data: " . $e->getMessage());
//             return false;
//         }
//     }
// }
<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use PhpMqtt\Client\MqttClient;
// use PhpMqtt\Client\ConnectionSettings;
// use App\Models\ClientDevice;
// use App\Events\MonitoringDataReceived;
// use Illuminate\Support\Facades\Log;

// class SubscribeMqtt extends Command
// {
//     /**
//      * The name and signature of the console command.
//      *
//      * @var string
//      */
//     protected $signature = 'mqtt:subscribe';

//     /**
//      * The console command description.
//      *
//      * @var string
//      */
//     protected $description = 'Subscribe to MQTT topics for IoT monitoring';

//     /**
//      * MQTT Client instance
//      *
//      * @var MqttClient
//      */
//     protected $mqtt;

//     /**
//      * Create a new command instance.
//      *
//      * @return void
//      */
//     public function __construct()
//     {
//         parent::__construct();
//     }

//     /**
//      * Execute the console command.
//      *
//      * @return int
//      */
//     public function handle()
//     {
//         $this->info('Starting MQTT Subscriber...');
//         $this->info('Server: ' . config('mqtt.host'));
//         $this->info('Port: ' . config('mqtt.port'));

//         try {
//             $this->initializeMqttClient();
//             $this->subscribeToTopics();
//             $this->mqtt->loop(true);
//         } catch (\Exception $e) {
//             $this->error('Error: ' . $e->getMessage());
//             Log::error('MQTT Subscriber Error: ' . $e->getMessage());
            
//             // Reconnect after 5 seconds if error occurs
//             sleep(5);
//             $this->handle();
//         }

//         return 0;
//     }

//     /**
//      * Initialize MQTT client connection
//      *
//      * @throws \Exception
//      */
//     protected function initializeMqttClient()
//     {
//         $server = config('mqtt.host');
//         $port = config('mqtt.port');
//         $clientId = 'laravel-subscriber-' . uniqid();
        
//         $this->mqtt = new MqttClient($server, $port, $clientId);
        
//         $connectionSettings = (new ConnectionSettings)
//             ->setUsername(config('mqtt.username'))
//             ->setPassword(config('mqtt.password'))
//             ->setKeepAliveInterval(60)
//             ->setLastWillTopic('project/startup/lastwill')
//             ->setLastWillMessage('Server disconnected')
//             ->setLastWillQualityOfService(1);
            
//         $this->mqtt->connect($connectionSettings, true);
        
//         $this->info('Connected to MQTT Broker');
//     }

//     /**
//      * Subscribe to required topics
//      */
//     protected function subscribeToTopics()
//     {
//         // Subscribe to monitoring topics for all devices
//         $this->mqtt->subscribe('/project/startup/client/+/monitoring', function ($topic, $message) {
//             $this->processMonitoringMessage($topic, $message);
//         }, 1);

//         // Subscribe to control response topics if needed
//         $this->mqtt->subscribe('/project/startup/client/+/control/response', function ($topic, $message) {
//             $this->processControlResponse($topic, $message);
//         }, 1);

//         $this->info('Subscribed to monitoring topics');
//     }

//     /**
//      * Process incoming monitoring messages
//      *
//      * @param string $topic
//      * @param string $message
//      */
//     protected function processMonitoringMessage($topic, $message)
//     {
//         try {
//             $topicParts = explode('/', $topic);
//             $deviceIdentifier = $topicParts[4]; // Extract device identifier from topic
            
//             Log::debug("Received monitoring data from topic: {$topic}", ['message' => $message]);
            
//             $data = json_decode($message, true);
            
//             if (json_last_error() !== JSON_ERROR_NONE) {
//                 throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
//             }
            
//             // Validate required fields
//             $requiredFields = ['voltage', 'current', 'power', 'energy', 'frequency', 'pf'];
//             foreach ($requiredFields as $field) {
//                 if (!array_key_exists($field, $data)) {
//                     throw new \Exception("Missing required field: {$field}");
//                 }
//             }
            
//             // Find device by MQTT topic
//             $device = ClientDevice::where('mqtt_topic', 'like', "%{$deviceIdentifier}%")->first();
            
//             if (!$device) {
//                 throw new \Exception("Device not found for topic: {$topic}");
//             }
            
//             // Create device log
//             $logData = [
//                 'voltage' => $data['voltage'] ?? null,
//                 'current' => $data['current'] ?? null,
//                 'power' => $data['power'] ?? null,
//                 'energy' => $data['energy'] ?? null,
//                 'frequency' => $data['frequency'] ?? null,
//                 'pf' => $data['pf'] ?? null, // Power factor
//             ];
            
//             $device->logs()->create(['data' => $logData]);
            
//             // Broadcast real-time update
//             event(new MonitoringDataReceived($device->id, $logData));
            
//             $this->info("Processed monitoring data for device: {$device->device_name}");
            
//         } catch (\Exception $e) {
//             Log::error('Error processing monitoring message: ' . $e->getMessage(), [
//                 'topic' => $topic,
//                 'message' => $message
//             ]);
//             $this->error('Error: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Process control responses from devices
//      *
//      * @param string $topic
//      * @param string $message
//      */
//     protected function processControlResponse($topic, $message)
//     {
//         try {
//             $data = json_decode($message, true);
            
//             if (json_last_error() !== JSON_ERROR_NONE) {
//                 throw new \Exception('Invalid JSON format in control response: ' . json_last_error_msg());
//             }
            
//             // Here you can process device responses to control commands
//             // For example, log the response or update device status
//             Log::info('Control response received', ['topic' => $topic, 'data' => $data]);
            
//         } catch (\Exception $e) {
//             Log::error('Error processing control response: ' . $e->getMessage(), [
//                 'topic' => $topic,
//                 'message' => $message
//             ]);
//         }
//     }

//     /**
//      * Handle graceful shutdown
//      */
//     public function __destruct()
//     {
//         if ($this->mqtt && $this->mqtt->isConnected()) {
//             $this->mqtt->disconnect();
//             $this->info('Disconnected from MQTT Broker');
//         }
//     }
// }
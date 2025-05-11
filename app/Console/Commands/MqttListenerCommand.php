<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use App\Services\MqttDataService;

// class MqttListenerCommand extends Command
// {
//     protected $signature = 'mqtt:listen';
//     protected $description = 'Listen to MQTT topics for device data';

//     public function handle()
//     {
//         $this->info('Starting MQTT listener...');
//         $this->info('Press Ctrl+C to stop');
        
//         try {
//             $mqttService = new MqttDataService();
//             $mqttService->subscribeToDevices();
//         } catch (\Exception $e) {
//             $this->error("Error: " . $e->getMessage());
//             $this->info('Restarting listener in 5 seconds...');
//             sleep(5);
//             $this->call('mqtt:listen');
//         }
//     }
// }
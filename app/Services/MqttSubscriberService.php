<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\DeviceMonitoring;
use App\Models\ClientDevice;
use App\Models\DeviceMonitoringData;
use App\Events\DeviceDataUpdated;
use Illuminate\Support\Facades\Log;
class MqttSubscriberService
{
    private $mqtt;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $server = 'broker.emqx.io';
        $port = 1883;
        $clientId = 'laravel-subscriber-' . uniqid();
        
        $this->mqtt = new MqttClient($server, $port, $clientId);
        
        $connectionSettings = (new ConnectionSettings)
            ->setKeepAliveInterval(60);
            
        $this->mqtt->connect($connectionSettings, true);
    }

    public function subscribeToDevices()
    {
        $devices = ClientDevice::where('status', 'active')->get();
        
        foreach ($devices as $device) {
            $topic = $device->mqtt_topic . '/monitoring';
            
            $this->mqtt->subscribe($topic, function (string $topic, string $message) {
                $this->processMessage($topic, $message);
            }, 0);
            
            echo "Subscribed to: " . $topic . PHP_EOL;
        }
        
        $this->mqtt->loop(true);
    }
    public function processMessage(string $topic, string $message)
    {
        try {
            $data = json_decode($message, true);
            
            $topicParts = explode('/', $topic);
            $deviceTopic = implode('/', array_slice($topicParts, 0, -1));
            
            $device = ClientDevice::where('mqtt_topic', $deviceTopic)->first();
            
            if ($device) {
                // Log sebelum proses
                \Log::channel('daily')->info('Processing MQTT message', [
                    'device_id' => $device->id,
                    'topic' => $topic,
                    'message' => $message
                ]);
    
                $monitoringData = DeviceMonitoring::create([
                    'device_id' => $device->id,
                    'voltage' => $data['voltage'],
                    'current' => $data['current'],
                    'power' => $data['power'],
                    'energy' => $data['energy'],
                    'frequency' => $data['Frequency'] ?? $data['frequency'],
                    'power_factor' => $data['pf'] ?? $data['power_factor'],
                    'recorded_at' => now()
                ]);
                
                echo "Data disimpan untuk device: " . $device->id . PHP_EOL;
                
                $device->update([
                    'last_voltage' => $data['voltage'],
                    'last_current' => $data['current'],
                    'last_power' => $data['power'],
                    'last_energy' => $data['energy'],
                    'last_frequency' => $data['Frequency'] ?? $data['frequency'],
                    'last_pf' => $data['pf'] ?? $data['power_factor'],
                    'last_data_received' => now()
                ]);
    
                // Dispatch event secara explicit
                \Log::channel('daily')->info('Dispatching DeviceDataUpdated event', [
                    'device_id' => $device->id
                ]);
    
                // Gunakan dispatch untuk memastikan event diproses
                \App\Events\DeviceDataUpdated::dispatch($device->id, [
                    'voltage' => $data['voltage'],
                    'current' => $data['current'],
                    'power' => $data['power'],
                    'energy' => $data['energy'],
                    'frequency' => $data['Frequency'] ?? $data['frequency'],
                    'power_factor' => $data['pf'] ?? $data['power_factor'],
                    'timestamp' => now()->toIso8601String()
                ]);
    
                echo "Data diperbaharui untuk device: " . $device->id . PHP_EOL;
            }
        } catch (\Exception $e) {
            \Log::channel('daily')->error('Error processing MQTT message', [
                'error' => $e->getMessage(),
                'topic' => $topic,
                'message' => $message
            ]);
            echo "Error processing message: " . $e->getMessage() . PHP_EOL;
        }
    }
    public function publish($topic, $message)
    {
        $this->mqtt->publish($topic, $message, 1);
    }
    
    public function disconnect()
    {
        $this->mqtt->disconnect();
    }
    
    public function __destruct()
    {
        $this->disconnect();
    }
}
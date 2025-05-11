<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\ClientDevice;
class MqttService
{
    protected $mqtt;

    public function __construct()
    {
        $server = 'broker.emqx.io';
        $port = 1883;
        $clientId = 'laravel-client-' . uniqid();
        
        $this->mqtt = new MqttClient($server, $port, $clientId);
        
        $connectionSettings = (new ConnectionSettings)
            ->setKeepAliveInterval(60)
            ->setLastWillTopic('project/startup/lastwill')
            ->setLastWillMessage('Client disconnected')
            ->setLastWillQualityOfService(1);
            
        $this->mqtt->connect($connectionSettings, true);
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
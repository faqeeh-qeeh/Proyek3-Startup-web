<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MqttSubscriberService;

class RunMqttSubscriber extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Run MQTT subscriber to save monitoring data';

    public function handle()
    {
        $this->info('Starting MQTT subscriber...');
        
        try {
            $subscriber = new MqttSubscriberService();
            $subscriber->subscribeToDevices();
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            sleep(5);
            $this->call('mqtt:subscribe'); // Restart on failure
        }
    }
}
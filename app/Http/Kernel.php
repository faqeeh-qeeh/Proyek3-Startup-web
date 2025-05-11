<?php  

namespace App\Http;  

use Illuminate\Foundation\Http\Kernel as HttpKernel;  
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends HttpKernel  
{  
    protected $middleware = [  
        // Middleware global  
    ];  

    protected $middlewareGroups = [  
        'web' => [  
            // Middleware web  
        ],  
        'api' => [  
            // Middleware api  
        ],  
    ];  

    protected $middlewareAliases = [  
        // Middleware aliases  
    ];  
    protected $commands = [
        \App\Console\Commands\MqttListenerCommand::class,
    ];
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('mqtt:subscribe')->everyMinute()->withoutOverlapping();
    }
}
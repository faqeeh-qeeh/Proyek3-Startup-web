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
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];  

    protected $middlewareAliases = [  
        \App\Http\Middleware\CorsMiddleware::class,
    ];  
    protected $commands = [
        // \App\Console\Commands\MqttListenerCommand::class,
    ];
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('mqtt:subscribe')->everyMinute()->withoutOverlapping();
    }
}
<?php

return [

    'default' => env('BROADCAST_DRIVER', 'null'),

    'connections' => [
        // 'pusher' => [
        //     'driver' => 'pusher',
        //     'key' => env('PUSHER_APP_KEY'),
        //     'secret' => env('PUSHER_APP_SECRET'),
        //     'app_id' => env('PUSHER_APP_ID'),
        //     'options' => [
        //         'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
        //         'encrypted' => true,
        //         'useTLS' => true,
        //         // 'host' => env('PUSHER_HOST', '127.0.0.1'), // Hapus jika menggunakan Pusher cloud
        //         // 'port' => env('PUSHER_PORT', 6001), // Hapus jika menggunakan Pusher cloud
        //         // 'scheme' => env('PUSHER_SCHEME', 'https'),
        //     ],
        // ],
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ],
        ],


        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
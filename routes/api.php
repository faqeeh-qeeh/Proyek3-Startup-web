<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\DeviceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::middleware('auth:client')->group(function () {
//     Route::get('/devices/{device}/monitoring-data', [\app\Http\Controllers\DeviceMonitoringController::class, 'getMonitoringData']);
// });

// Route::middleware('auth:client')->group(function () {
//     Route::get('/devices/{device}/monitoring', [\app\Http\Controllers\DeviceMonitoringController::class, 'getMonitoringData']);
// });

// Route::middleware('auth:client')->group(function () {
//     Route::get('/devices/{device}/history', [\App\Http\Controllers\Api\DeviceHistoryController::class, 'index']);
//     Route::post('/devices/{device}/data', [\App\Http\Controllers\Api\DeviceHistoryController::class, 'store']);
// });

// Route::middleware('auth:client')->group(function () {
//     Route::get('/devices/{device}/monitoring', [\App\Http\Controllers\Api\DeviceMonitoringController::class, 'getLatestData']);
//     Route::get('/devices/{device}/monitoring/history', [\App\Http\Controllers\Api\DeviceMonitoringController::class, 'getHistoricalData']);
// });
// Route::get('/devices/{device}/status', [\App\Http\Controllers\Client\DeviceController::class, 'getStatus'])->name('client.devices.status');

// Route::middleware(['auth:client'])->group(function () {
//     // Data monitoring
//     Route::get('/devices/{device}/monitoring', [DeviceController::class, 'getLatestData']);
    
//     // Kontrol relay
//     Route::post('/devices/{device}/control', [DeviceController::class, 'controlDevice']);
    
//     // Status relay
//     Route::get('/devices/{device}/relay-status', [DeviceController::class, 'getRelayStatus']);
    
//     // History data
//     Route::get('/devices/{device}/history', [DeviceController::class, 'getHistoricalData']);
// });

// routes/api.php

Route::middleware('auth:client')->group(function () {
    Route::get('/devices/{device}/monitoring', [DeviceController::class, 'getLatestData']);
});

Route::middleware(['auth:client', 'api'])->group(function() {
    Route::get('devices/{device}/monitoring', [DeviceController::class, 'getLatestData'])
        ->name('api.devices.monitoring');
    
    Route::get('devices/{device}/relay-status', [DeviceController::class, 'getRelayStatus'])
        ->name('api.devices.relay-status');
});

// api.php
// Route::get('devices/{device}/monitoring', [DeviceController::class, 'getLatestData'])
//     ->name('api.devices.monitoring');
// ->middleware(['auth:client', 'api']); // Komentari sementara
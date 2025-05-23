<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\DeviceController;
use App\Http\Controllers\Auth\ClientAuthController;

use App\Http\Controllers\Client\AnomalyController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware('auth:client')->group(function () {
    Route::get('/devices/{device}/monitoring', [DeviceController::class, 'getLatestData']);
});

Route::middleware(['auth:client', 'api'])->group(function() {
    Route::get('devices/{device}/monitoring', [DeviceController::class, 'getLatestData'])
        ->name('api.devices.monitoring');
    
    Route::get('devices/{device}/relay-status', [DeviceController::class, 'getRelayStatus'])
        ->name('api.devices.relay-status');
});
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

// // routes/api.php
// Route::prefix('client')->group(function () {
//     // Authentication Routes
//     Route::post('/register', [ClientAuthController::class, 'apiRegister']);
//     Route::post('/login', [ClientAuthController::class, 'apiLogin']);
    
//     // Protected Routes
//     Route::middleware('auth:client-api')->group(function () {
//         Route::post('/logout', [ClientAuthController::class, 'apiLogout']);
//         Route::get('/user', [ClientAuthController::class, 'getUser']);
//         Route::get('/products', [\App\Http\Controllers\Client\ProductController::class, 'apiIndex']);
//     });
// });




Route::get('/test-response', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'Test response',
        'data' => [
            ['id' => 1, 'name' => 'Test Product']
        ]
    ]);
});





use App\Http\Controllers\Auth\ClientAuthApiController;
use App\Http\Controllers\Client\ProductApiController;
use App\Http\Controllers\Client\OrderApiController;
use App\Http\Controllers\Client\PaymentApiController;
use App\Http\Controllers\Client\DeviceApiController;
Route::prefix('client')->group(function () {
    Route::post('/register', [ClientAuthApiController::class, 'register']);
    Route::post('/login', [ClientAuthApiController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ClientAuthApiController::class, 'logout']);
        Route::get('/products', [ProductApiController::class, 'index']);
                // Order routes
        Route::get('/orders', [OrderApiController::class, 'index']);
        Route::post('/orders', [OrderApiController::class, 'store']);
        Route::get('/orders/{order}', [OrderApiController::class, 'show']);
        
        // Payment routes
        Route::post('/orders/{order}/payment', [PaymentApiController::class, 'create']);
        Route::get('/orders/{order}/check-status', [PaymentApiController::class, 'checkStatus']);
        Route::get('/devices', [DeviceApiController::class, 'index']);
        Route::get('/devices/{device}', [DeviceApiController::class, 'show']);
        Route::get('/devices/{device}/latest-data', [DeviceApiController::class, 'getLatestData']);
        Route::get('/devices/{device}/monitoring-data', [DeviceApiController::class, 'getMonitoringData']);
        Route::get('/devices/{device}/relay-status', [DeviceApiController::class, 'getRelayStatus']);
        Route::post('/devices/{device}/control', [DeviceApiController::class, 'controlDevice']);
    });
});
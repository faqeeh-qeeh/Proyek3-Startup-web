<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ClientDeviceController;
use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [AdminAuthController::class, 'register']);
    
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    Route::middleware('auth:admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            // Products
        Route::resource('products', ProductController::class);
        
        // Orders
        Route::resource('orders', OrderController::class)->only(['index', 'show']);
        Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('orders/{order}/assign-mqtt', [OrderController::class, 'assignMqttTopic'])->name('orders.assign-mqtt');
        Route::get('orders/export/excel', [OrderController::class, 'exportExcel'])->name('orders.export.excel');
        Route::get('orders/export/pdf', [OrderController::class, 'exportPDF'])->name('orders.export.pdf');
        // Devices
        Route::resource('devices', ClientDeviceController::class)->except(['create', 'store']);
        // Route::get('/clients', [\App\Http\Controllers\Admin\ClientController::class, 'index'])->name('clients.index');
        // Route::get('/clients/{client}', [\App\Http\Controllers\Admin\ClientController::class, 'show'])->name('clients.show');
        // Route::put('/clients/{client}', [\App\Http\Controllers\Admin\ClientController::class, 'update'])->name('clients.update');
        // Route::get('/clients/export/excel', [\App\Http\Controllers\Admin\ClientController::class, 'exportExcel'])->name('clients.export.excel');
        // Route::get('/clients/export/pdf', [\App\Http\Controllers\Admin\ClientController::class, 'exportPdf'])->name('clients.export.pdf');
        Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class)->only(['index', 'show']);
    });
});

// Client Routes (akan dikembangkan nanti)
// Route::prefix('client')->group(function () {
//     Route::get('/register', [ClientAuthController::class, 'showRegisterForm'])->name('client.register');
//     Route::post('/register', [ClientAuthController::class, 'register']);
    
//     Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('client.login');
//     Route::post('/login', [ClientAuthController::class, 'login']);
    
//     Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');
// });

// // Client Routes
// Route::prefix('client')->group(function () {
//     Route::get('/register', [ClientAuthController::class, 'showRegisterForm'])->name('client.register');
//     Route::post('/register', [ClientAuthController::class, 'register']);
    
//     Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('client.login');
//     Route::post('/login', [ClientAuthController::class, 'login']);
    
//     Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');
    
//     // Route::middleware('auth:client')->group(function () {
//     //     Route::get('/products', [\App\Http\Controllers\Client\ProductController::class, 'index'])->name('client.products');
//     // });
    
//     // Authenticated Client Routes
//     Route::middleware('auth:client')->name('client.')->group(function () {
//         // Dashboard/Home
//         Route::get('/dashboard', function () {
//             return redirect()->route('client.orders.index');
//         })->name('dashboard');
//             // Products
//         Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    
//         // Orders
//         Route::resource('orders', OrderController::class)->except(['edit', 'update', 'destroy']);
    
//         // Payments
//         Route::prefix('payments')->group(function () {
//             Route::get('/{order}/create', [\App\Http\Controllers\Client\PaymentController::class, 'create'])->name('payments.create');
//             Route::post('/{order}/store', [\App\Http\Controllers\Client\PaymentController::class, 'store'])->name('payments.store');
//             Route::get('/{order}/success', [\App\Http\Controllers\Client\PaymentController::class, 'success'])->name('payments.success');
//             Route::get('/{order}/failure', [\App\Http\Controllers\Client\PaymentController::class, 'failure'])->name('payments.failure');
//         });
        
//         // Midtrans Notification
//         Route::post('/payments/notification', [\App\Http\Controllers\Client\PaymentController::class, 'handleNotification'])
//             ->withoutMiddleware(['web', 'auth:client'])
//             ->name('payments.notification');
        
//         // Devices
//         Route::resource('devices', \App\Http\Controllers\Client\DeviceController::class)->only(['index', 'show']);
//         Route::post('/devices/{device}/control', [\App\Http\Controllers\Client\DeviceController::class, 'controlDevice'])
//             ->name('devices.control');
//     });
// });

// Client Routes
Route::prefix('client')->group(function () {
    // Authentication Routes
    Route::middleware('guest:client')->group(function () {
        Route::get('/register', [ClientAuthController::class, 'showRegisterForm'])->name('client.register');
        Route::post('/register', [ClientAuthController::class, 'register'])->name('client.register.post');
        
        Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('client.login');
        Route::post('/login', [ClientAuthController::class, 'login'])->name('client.login.post');
    });
    
    // Logout Route
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');
    
    // Authenticated Client Routes
    Route::middleware('auth:client')->name('client.')->group(function () {
        // Dashboard/Home
        Route::get('/dashboard', function () {
            return redirect()->route('client.orders.index');
        })->name('dashboard');
        
        // Products
        Route::get('/products', [\App\Http\Controllers\Client\ProductController::class, 'index'])->name('products.index');
    
        // Orders
        Route::resource('orders', \App\Http\Controllers\Client\OrderController::class)->except(['edit', 'update', 'destroy']);
        
        // Order Confirmation (jika perlu)
        Route::post('/orders/{order}/confirm', [\App\Http\Controllers\Client\OrderController::class, 'confirm'])->name('orders.confirm');
    
        // Payments
        Route::prefix('payments')->group(function () {
            Route::get('/{order}/create', [\App\Http\Controllers\Client\PaymentController::class, 'create'])->name('payments.create');
            Route::post('/{order}/store', [\App\Http\Controllers\Client\PaymentController::class, 'store'])->name('payments.store');
            Route::get('/{order}/success', [\App\Http\Controllers\Client\PaymentController::class, 'success'])->name('payments.success');
            Route::get('/{order}/failure', [\App\Http\Controllers\Client\PaymentController::class, 'failure'])->name('payments.failure');
            
            // Manual Payment Confirmation (jika perlu)
            Route::get('/{order}/confirm', [\App\Http\Controllers\Client\PaymentController::class, 'showConfirmForm'])->name('payments.confirm');
            Route::post('/{order}/confirm', [\App\Http\Controllers\Client\PaymentController::class, 'confirmPayment'])->name('payments.confirm.post');
            Route::post('/{order}/retry', [\App\Http\Controllers\Client\PaymentController::class, 'retry'])->name('payments.retry');

        });
        Route::prefix('devices')->name('devices.')->group(function() {
            // Resource routes
            Route::resource('/', \App\Http\Controllers\Client\DeviceController::class)
                ->only(['index', 'show'])
                ->parameters(['' => 'device']);

            // Control routes
            Route::post('/{device}/control', [\App\Http\Controllers\Client\DeviceController::class, 'controlDevice'])
                ->name('control');

            // Data routes
            Route::prefix('/{device}')->group(function() {
                Route::get('/monitoring-data', [\App\Http\Controllers\Client\DeviceController::class, 'getMonitoringData'])
                    ->name('monitoring-data');
            
                Route::get('/monitoring', [\App\Http\Controllers\Client\DeviceController::class, 'getLatestData'])
                    ->name('monitoring');
            
                Route::get('/relay-status', [\App\Http\Controllers\Client\DeviceController::class, 'getRelayStatus'])
                    ->name('relay-status');
                        Route::post('/detect-anomalies', [\App\Http\Controllers\Client\AnomalyController::class, 'detect'])
                    ->name('detect-anomalies');
                        
                Route::post('/classify', [\App\Http\Controllers\Client\AnomalyController::class, 'classify'])
                    ->name('classify');
                        
                Route::get('/anomalies', [\App\Http\Controllers\Client\AnomalyController::class, 'showAnomalies'])
                    ->name('anomalies');
                Route::get('/export-monitoring', [\App\Http\Controllers\Client\DeviceController::class, 'exportMonitoringData'])
                    ->name('export-monitoring');
                    
            });
        });
        });
    Route::post('/payments/notification', [\App\Http\Controllers\Client\PaymentController::class, 'handleNotification'])
    ->withoutMiddleware(['web', 'verifyCsrfToken', 'auth:client'])
    ->name('client.payments.notification');
    //     // Tambahkan route
    // Route::post('/client/orders/{order}/check-status', [\App\Http\Controllers\Client\PaymentController::class, 'checkStatus'])
    //     ->name('orders.check-status');\
    Route::get('/payments/{order}/check-status', [\App\Http\Controllers\Client\PaymentController::class, 'checkStatus'])
    ->name('client.payments.check-status');
    Route::get('/devices/{device}/anomalies', [\App\Http\Controllers\Client\AnomalyController::class, 'index'])
         ->name('client.devices.anomalies');
         
    Route::post('/devices/{device}/detect-anomalies', [\App\Http\Controllers\Client\AnomalyController::class, 'detectAnomalies'])
         ->name('client.devices.detect-anomalies');
         
    Route::patch('/anomalies/{anomaly}/confirm', [\App\Http\Controllers\Client\AnomalyController::class, 'confirmAnomaly'])
         ->name('client.devices.confirm-anomaly');
});


// Route::get('/test-broadcast', function() {
//     event(new \App\Events\DeviceDataUpdated(1, [
//         'voltage' => 220,
//         'current' => 1.5,
//         'power' => 330,
//         'energy' => 5.6,
//         'frequency' => 50,
//         'pf' => 0.98
//     ]));
//     return "Event dispatched!";
// });

// Buat route test di routes/web.php
// Route::get('/test-pusher', function() {
//     $pusher = new \Pusher\Pusher(
//         config('broadcasting.connections.pusher.key'),
//         config('broadcasting.connections.pusher.secret'),
//         config('broadcasting.connections.pusher.app_id'),
//         config('broadcasting.connections.pusher.options')
//     );
    
//     try {
//         $pusher->trigger('test-channel', 'test-event', ['message' => 'Hello']);
//         return "Pusher connected successfully!";
//     } catch (\Exception $e) {
//         return "Pusher error: " . $e->getMessage();
//     }
// });

// routes/web.php

// use Pusher\Pusher;

// Route::get('/direct-pusher', function () {
//     $options = [
//         'cluster' => env('PUSHER_APP_CLUSTER'),
//         'useTLS' => true
//     ];

//     $pusher = new Pusher(
//         env('PUSHER_APP_KEY'),
//         env('PUSHER_APP_SECRET'),
//         env('PUSHER_APP_ID'),
//         $options
//     );

//     $pusher->trigger('device-data', 'DeviceDataUpdated', [
//         'device_id' => 1,
//         'data' => ['test' => 223]
//     ]);

//     return 'Direct Event sent!';
// });

// Route::get('/test-listen', function () {
//     return view('test-listen');
// });

Route::get('/test', function () {
    return view('test-listen'); // dari file test-listen.blade.php yang ada
});

Route::get('/test-model', function() {
    $service = new App\Services\AnomalyDetectionService();
    
    return response()->json([
        'model_exists' => file_exists(storage_path('app/models/anomaly_detector.model')),
        'model_size' => filesize(storage_path('app/models/anomaly_detector.model')),
        'is_trained' => $service->isModelTrained(),
        'test_prediction' => $service->testAnomalyDetection([220, 1.5, 330, 50, 0.9])
    ]);
});
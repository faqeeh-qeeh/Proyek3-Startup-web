<?php

// namespace App\Http\Controllers\Client;

// use App\Http\Controllers\Controller;
// use App\Models\ClientDevice;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class DeviceDataController extends Controller
// {
//     public function show(ClientDevice $device)
//     {
//         if ($device->client_id !== Auth::guard('client')->id()) {
//             abort(403);
//         }

//         return view('client.devices.show', compact('device'));
//     }

//     public function getHistoricalData(ClientDevice $device)
//     {
//         // Ini contoh, dalam implementasi nyata ambil dari database
//         $data = [
//             'labels' => [],
//             'power' => []
//         ];

//         // Generate dummy data
//         for ($i = 0; $i < 60; $i++) {
//             $data['labels'][] = now()->subMinutes($i)->format('H:i');
//             $data['power'][] = rand(100, 2000);
//         }

//         return response()->json($data);
//     }
// }
<?php

// namespace App\Http\Controllers;

// use App\Models\ClientDevice;
// use App\Models\DeviceLog;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class DeviceMonitoringController extends Controller
// {
//     public function getMonitoringData(ClientDevice $device)
//     {
//         // Verifikasi device milik client yang login
//         if ($device->client_id !== Auth::guard('client')->id()) {
//             return response()->json(['error' => 'Unauthorized'], 403);
//         }

//         // Ambil 15 data terakhir dari log
//         $logs = $device->logs()->latest()->take(15)->get()->reverse();

//         // Format data untuk chart
//         $chartData = [
//             'labels' => $logs->map(function ($log) {
//                 return $log->created_at->format('H:i:s');
//             }),
//             'voltage' => $logs->pluck('data.voltage'),
//             'current' => $logs->pluck('data.current'),
//             'power' => $logs->pluck('data.power'),
//             'energy' => $logs->pluck('data.energy'),
//             'frequency' => $logs->pluck('data.frequency'),
//             'power_factor' => $logs->pluck('data.pf'),
//         ];

//         // Data terbaru
//         $latestLog = $device->logs()->latest()->first();

//         return response()->json([
//             'chart' => $chartData,
//             'latest' => $latestLog ? $latestLog->data : null,
//             'costs' => $this->calculateCosts($device)
//         ]);
//     }

//     private function calculateCosts(ClientDevice $device)
//     {
//         // Hitung biaya berdasarkan tarif listrik (contoh: Rp 1.500 per kWh)
//         $ratePerKWh = 1500;
//         $latestLog = $device->logs()->latest()->first();
        
//         if (!$latestLog) {
//             return [
//                 'minute' => 0,
//                 'hour' => 0,
//                 'day' => 0
//             ];
//         }

//         $power = $latestLog->data['power'] ?? 0; // dalam Watt
//         $energy = $latestLog->data['energy'] ?? 0; // dalam kWh

//         return [
//             'minute' => ($power / 1000) * (1/60) * $ratePerKWh,
//             'hour' => ($power / 1000) * $ratePerKWh,
//             'day' => ($power / 1000) * 24 * $ratePerKWh
//         ];
//     }
// }
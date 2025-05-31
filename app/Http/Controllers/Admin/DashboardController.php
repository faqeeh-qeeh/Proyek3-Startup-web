<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ClientDevice;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        // Data statistik utama
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $activeDevices = ClientDevice::where('status', 'active')->count();
        
        // Data untuk grafik
        $orderTrends = $this->getOrderTrends();
        $orderStatusDistribution = $this->getOrderStatusDistribution();
        
        // Data terbaru
        $recentOrders = Order::with('client')
            ->latest()
            ->take(5)
            ->get();
            
        $recentDevices = ClientDevice::with('client')
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalProducts',
            'totalOrders',
            'pendingOrders',
            'activeDevices',
            'recentOrders',
            'recentDevices',
            'orderTrends',
            'orderStatusDistribution'
        ));
    }

    private function getOrderTrends()
    {
        // Data order 7 hari terakhir
        $dates = [];
        $completedData = [];
        $pendingData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates[] = $date->format('D');
            
            $completedData[] = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->count();
                
            $pendingData[] = Order::whereDate('created_at', $date)
                ->where('status', 'pending')
                ->count();
        }
        
        return [
            'labels' => $dates,
            'completed' => $completedData,
            'pending' => $pendingData
        ];
    }

    private function getOrderStatusDistribution()
    {
        $total = Order::count();
        
        if ($total === 0) {
            return [
                'completed' => 0,
                'pending' => 0,
                'cancelled' => 0
            ];
        }
        
        return [
            'completed' => round(Order::where('status', 'completed')->count() / $total * 100),
            'pending' => round(Order::where('status', 'pending')->count() / $total * 100),
            'cancelled' => round(Order::where('status', 'cancelled')->count() / $total * 100)
        ];
    }
}
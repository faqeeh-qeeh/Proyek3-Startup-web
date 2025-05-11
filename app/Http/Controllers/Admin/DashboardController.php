<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ClientDevice;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $activeDevices = ClientDevice::where('status', 'active')->count();
        
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
            'recentDevices'
        ));
    }
}
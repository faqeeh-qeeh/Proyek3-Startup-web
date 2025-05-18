<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $status = request()->query('status');
        
        $orders = Order::with(['client', 'items.product'])
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);
            
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['client', 'items.product', 'devices']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,failed',
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

// Di method assignMqttTopic
    public function assignMqttTopic(Request $request, Order $order)
    {
        $request->validate([
            'mqtt_topics' => 'required|array',
            'mqtt_topics.*' => 'required|string|unique:client_devices,mqtt_topic',
        ]);

        // Pastikan order sudah dibayar
        if ($order->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Order must be paid before assigning MQTT topics');
        }

        foreach ($order->items as $item) {
            if (isset($request->mqtt_topics[$item->id])) {
                $topic = '/project/startup/client/' . $request->mqtt_topics[$item->id];

                // Cek apakah device sudah ada
                $existingDevice = $order->devices()->where('product_id', $item->product_id)->first();

                if ($existingDevice) {
                    $existingDevice->update([
                        'mqtt_topic' => $topic,
                        'status' => 'active',
                    ]);
                } else {
                    $order->devices()->create([
                        'client_id' => $order->client_id,
                        'product_id' => $item->product_id,
                        'mqtt_topic' => $topic,
                        'device_name' => $item->product->name . ' Device',
                        'status' => 'active',
                    ]);
                }
            }
        }

        // Update status order jika belum completed
        if ($order->status !== 'completed') {
            $order->update(['status' => 'completed']);
        }

        return redirect()->back()->with('success', 'MQTT topics assigned successfully');
    }
    public function exportExcel()
    {
        return Excel::download(new OrdersExport, 'orders_'.date('Ymd_His').'.xlsx');
    }

    public function exportPDF()
    {
        $orders = Order::with(['client', 'items.product'])
            ->latest()
            ->get();

        $pdf = PDF::loadView('admin.orders.export_pdf', compact('orders'))
                  ->setPaper('a4', 'landscape')
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true
                  ]);

        return $pdf->download('orders_'.date('Ymd_His').'.pdf');
    }
}
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Snap;
use App\Models\ClientDevice;
use App\Services\MqttService;
class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    public function index()
    {
        $orders = auth('client')->user()->orders()
                    ->with(['items.product'])
                    ->latest()
                    ->paginate(10); // Ubah ini dari get() ke paginate()
        
        return view('client.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('client.orders.create', compact('products'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'quantity' => 'required|integer|min:1',
    //     ]);

    //     $product = Product::findOrFail($request->product_id);

    //     $order = auth('client')->user()->orders()->create([
    //         'order_number' => 'ORD-' . Str::upper(Str::random(10)),
    //         'total_amount' => $product->price * $request->quantity,
    //         'status' => 'pending',
    //         'payment_status' => 'unpaid',
    //     ]);

    //     $order->items()->create([
    //         'product_id' => $product->id,
    //         'quantity' => $request->quantity,
    //         'price' => $product->price,
    //     ]);

    //     return redirect()->route('client.payments.create', $order)
    //         ->with('success', 'Order created successfully. Please proceed to payment.');
    // }
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            // quantity validation dihapus karena sudah fixed 1
        ]);
    
        $product = Product::findOrFail($request->product_id);
    
        $order = auth('client')->user()->orders()->create([
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'total_amount' => $product->price, // Langsung harga produk, tidak dikali quantity
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
    
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1, // Fixed quantity 1
            'price' => $product->price,
        ]);
    
        return redirect()->route('client.payments.create', $order)
            ->with('success', 'Order created successfully. Please proceed to payment.');
    }
    public function show(Order $order)
    {
        if ($order->client_id !== auth('client')->id()) {
            abort(403);
        }

        $order->load(['items.product', 'devices']);
        return view('client.orders.show', compact('order'));
    }
    public function controlDevice(Request $request, ClientDevice $device)
    {
        if ($device->client_id !== auth('client')->id()) {
            abort(403);
        }

        $request->validate([
            'command' => 'required|in:on,off',
            'channel' => 'required|integer|min:1|max:4',
        ]);

        $mqttService = new MqttService();
        $topic = $device->mqtt_topic . '/control';
        $message = json_encode([
            'command' => $request->command,
            'channel' => $request->channel,
            'timestamp' => now()->toDateTimeString(),
        ]);

        $mqttService->publish($topic, $message);

        return back()->with('success', 'Command sent successfully');
    }
}
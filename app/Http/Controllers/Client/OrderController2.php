<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $midtransService;

    public function __construct()
    {
        $this->middleware('auth:client');
        $this->midtransService = new MidtransService();
    }

    public function index()
    {
        $orders = Auth::guard('client')->user()->orders()->latest()->get();
        return view('client.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('client.orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Buat order
        $order = Auth::guard('client')->user()->orders()->create([
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'total_amount' => $product->price * $request->quantity,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        // Tambahkan item ke order
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->price,
        ]);

        // Generate Snap Token
        $snapToken = $this->midtransService->createSnapToken($order);

        return redirect()->route('client.orders.show', $order->id)
            ->with('snap_token', $snapToken);
    }

    public function show(Order $order)
    {
        if ($order->client_id != Auth::guard('client')->id()) {
            abort(403);
        }

        $order->load(['items.product', 'devices']);
        
        $snapToken = session('snap_token') ?? null;

        return view('client.orders.show', compact('order', 'snapToken'));
    }
}
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $orders = auth()->user()->orders()
                    ->with(['items.product'])
                    ->latest()
                    ->paginate(10);
        
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
    
        $product = Product::findOrFail($request->product_id);
    
        $order = auth()->user()->orders()->create([
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'total_amount' => $product->price,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
    
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);
    
        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order->load('items.product')
        ], 201);
    }

    public function show(Order $order)
    {
        if ($order->client_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load(['items.product', 'devices']));
    }
}
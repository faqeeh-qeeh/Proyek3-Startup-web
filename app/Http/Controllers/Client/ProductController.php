<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;



class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    public function index()
    {
        $products = Product::where('is_active', true)
            ->withCount(['clientDevices as available_stock' => function($query) {
                $query->where('status', 'active');
            }])
            ->get();

        return view('client.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        // Hitung perangkat yang sudah dibeli oleh client ini
        $ownedDevices = auth('client')->user()
            ->devices()
            ->where('product_id', $product->id)
            ->count();

        // Rekomendasi untuk pembelian tambahan jika sudah memiliki perangkat
        $recommendation = null;
        if ($ownedDevices > 0) {
            $recommendation = [
                'message' => 'Anda sudah memiliki ' . $ownedDevices . ' perangkat ini.',
                'suggestion' => 'Pertimbangkan untuk membeli perangkat tambahan untuk monitoring ruangan/lokasi lain.'
            ];
        }

        return view('client.products.show', [
            'product' => $product,
            'ownedDevices' => $ownedDevices,
            'recommendation' => $recommendation
        ]);
    }

    // public function apiIndex()
    // {
    //     $products = Product::where('is_active', true)
    //         ->withCount(['clientDevices as available_stock' => function($query) {
    //             $query->where('status', 'active');
    //         }])
    //         ->get();
        
    //     return ProductResource::collection($products);
    // }
    // ... (di dalam class ProductController)

    public function indexApi()
    {
        $products = Product::where('is_active', true)
            ->withCount(['clientDevices as available_stock' => function($query) {
                $query->where('status', 'active');
            }])
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'formatted_price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                    'available_stock' => $product->available_stock,
                ];
            });

        return response()->json($products);
    }
}
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
}
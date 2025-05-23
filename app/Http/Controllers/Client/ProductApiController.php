<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $products = Product::where('is_active', true)
            ->withCount(['clientDevices as available_stock' => function($query) {
                $query->where('status', 'active');
            }])
            ->get();

        return response()->json($products);
    }
}
{{-- @extends('client.layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Daftar Produk IoT Monitoring</h2>
        </div>
    </div>

    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="text-center py-5 bg-light">
                    <i class="fas fa-image fa-5x text-muted"></i>
                </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="text-primary mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</h4>
                        <form action="{{ route('client.orders.create') }}" method="GET">
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-shopping-cart mr-2"></i> Beli
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($products->isEmpty())
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                Saat ini tidak ada produk yang tersedia.
            </div>
        </div>
    </div>
    @endif
</div>
@endsection --}}


@extends('client.layouts.app')

@section('title', 'Daftar Produk')

@push('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
        background-color: var(--card-bg);
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .product-img-container {
        height: 200px;
        background-color: var(--light-color);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .product-img {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    
    .product-placeholder {
        color: var(--text-muted);
        font-size: 3rem;
    }
    
    .product-title {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0.75rem;
    }
    
    .product-description {
        color: var(--text-muted);
        margin-bottom: 1.25rem;
    }
    
    .product-price {
        font-weight: 700;
        color: var(--primary-color);
    }
    
    .page-title {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.5rem;
    }
    
    .page-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background-color: var(--primary-color);
    }
    
    .empty-state {
        background-color: var(--card-bg);
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Daftar Produk IoT Monitoring</h2>
        </div>
    </div>

    <div class="row g-2">
        @foreach($products as $product)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card product-card h-100">
                <div class="product-img-container">
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="product-img" alt="{{ $product->name }}">
                    @else
                    <i class="fas fa-image product-placeholder"></i>
                    @endif
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="product-title">{{ $product->name }}</h5>
                    <p class="product-description">{{ Str::limit($product->description, 100) }}</p>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <h4 class="product-price mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</h4>
                        {{-- <form action="{{ route('client.orders.create') }}" method="GET">
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-shopping-cart me-2"></i> Beli
                            </button>
                        </form> --}}
                        <form action="{{ route('client.orders.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1"> <!-- Fix quantity to 1 -->
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-shopping-cart mr-2"></i> Beli
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($products->isEmpty())
    <div class="row">
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h4 class="mb-3">Tidak ada produk tersedia</h4>
                <p class="text-muted">Silakan cek kembali nanti atau hubungi admin untuk informasi lebih lanjut.</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
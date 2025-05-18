{{-- @extends('client.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Orders</h5>
                    <a href="{{ route('client.orders.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> New Order
                    </a>
                </div>

                <div class="card-body">
                    @if($orders->isEmpty())
                        <div class="alert alert-info text-center">
                            You don't have any orders yet. <a href="{{ route('client.orders.create') }}">Create your first order</a> to get started.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Payment Status</th>
                                        <th>Order Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>
                                            @foreach($order->items as $item)
                                                {{ $item->product->name }} (x{{ $item->quantity }})<br>
                                            @endforeach
                                        </td>
                                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : ($order->status === 'pending' ? 'warning' : 'secondary')) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('client.orders.show', $order) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Details
                                            </a>
                                            @if($order->payment_status === 'unpaid' && $order->status !== 'cancelled')
                                                <a href="{{ route('client.payments.create', $order) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-credit-card"></i> Pay
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($orders->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}


@extends('client.layouts.app')

@section('title', 'Daftar Pesanan')

@push('styles')
<style>
    .order-card {
        background-color: var(--card-bg);
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }
    
    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1.5rem 0 rgba(58, 59, 69, 0.2);
    }
    
    .order-header {
        background-color: rgba(78, 115, 223, 0.05);
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 1.5rem;
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .order-body {
        padding: 1.5rem;
    }
    
    .order-product {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px dashed var(--border-color);
    }
    
    .order-product:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .order-product-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 0.25rem;
        margin-right: 1rem;
    }
    
    .order-product-name {
        font-weight: 500;
        color: var(--text-color);
        margin-bottom: 0.25rem;
    }
    
    .order-product-qty {
        color: var(--text-muted);
        font-size: 0.875rem;
    }
    
    .order-meta {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }
    
    .order-status {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .status-badge {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
    
    .badge-paid {
        background-color: var(--success-color);
    }
    
    .badge-pending {
        background-color: var(--warning-color);
    }
    
    .badge-unpaid {
        background-color: var(--danger-color);
    }
    
    .badge-completed {
        background-color: var(--success-color);
    }
    
    .badge-processing {
        background-color: var(--info-color);
    }
    
    .badge-cancelled {
        background-color: var(--secondary-color);
    }
    
    .empty-order {
        background-color: var(--card-bg);
        border-radius: 0.5rem;
        padding: 3rem 2rem;
        text-align: center;
    }
    
    .empty-order-icon {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
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
    .pagination {
    --bs-pagination-color: var(--text-color);
    --bs-pagination-bg: var(--card-bg);
    --bs-pagination-border-color: var(--border-color);
    --bs-pagination-hover-color: var(--primary-color);
    --bs-pagination-hover-bg: var(--light-color);
    --bs-pagination-hover-border-color: var(--border-color);
    --bs-pagination-focus-color: var(--primary-color);
    --bs-pagination-focus-bg: var(--light-color);
    --bs-pagination-focus-box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    --bs-pagination-active-color: #fff;
    --bs-pagination-active-bg: var(--primary-color);
    --bs-pagination-active-border-color: var(--primary-color);
    --bs-pagination-disabled-color: var(--text-muted);
    --bs-pagination-disabled-bg: var(--card-bg);
    --bs-pagination-disabled-border-color: var(--border-color);
    }

    .page-link {
        padding: 0.5rem 0.75rem;
        min-width: 38px;
        text-align: center;
        border-radius: 0.375rem !important;
        margin: 0 2px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .page-item.active .page-link {
        box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.2);
    }

    .page-item:not(.active) .page-link:hover {
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">Daftar Pesanan Saya</h2>
        <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Pesan Baru
        </a>
    </div>

    @if($orders->isEmpty())
    <div class="empty-order">
        <i class="fas fa-box-open empty-order-icon"></i>
        <h4 class="mb-3">Belum ada pesanan</h4>
        <p class="text-muted mb-4">Mulai pesan produk IoT pertama Anda untuk memulai monitoring</p>
        <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Buat Pesanan
        </a>
    </div>
    @else
    <div class="row">
        @foreach($orders as $order)
        <div class="col-lg-6">
            <div class="order-card">
                <div class="order-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $order->order_number }}</h5>
                        <small class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</small>
                    </div>
                    <div class="d-flex">
                        <span class="order-status me-2" style="background-color: rgba(28, 200, 138, 0.1); color: var(--success-color);">
                            <span class="status-badge badge-{{ $order->payment_status }}"></span>
                            {{ ucfirst($order->payment_status) }}
                        </span>
                        <span class="order-status" style="background-color: rgba(78, 115, 223, 0.1); color: var(--primary-color);">
                            <span class="status-badge badge-{{ $order->status }}"></span>
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                <div class="order-body">
                    @foreach($order->items as $item)
                    <div class="order-product">
                        @if($item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}" class="order-product-img" alt="{{ $item->product->name }}">
                        @else
                        <div class="order-product-img bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-box text-muted"></i>
                        </div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="order-product-name">{{ $item->product->name }}</h6>
                            <div class="d-flex justify-content-between">
                                <span class="order-product-qty">x{{ $item->quantity }}</span>
                                <span class="text-primary">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="order-meta">
                        <div>
                            <h5 class="text-primary mb-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h5>
                            <small class="text-muted">Total Pembayaran</small>
                        </div>
                        <div class="d-flex">
                            <a href="{{ route('client.orders.show', $order) }}" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                            @if($order->payment_status === 'unpaid' && $order->status !== 'cancelled')
                            <a href="{{ route('client.payments.create', $order) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-credit-card me-1"></i> Bayar
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

@if($orders->hasPages())
<div class="d-flex justify-content-center mt-4">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($orders->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                @if ($page == $orders->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($orders->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif
    @endif
</div>
@endsection
{{-- @extends('client.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Order Details: #{{ $order->order_number }}</span>
                    <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : ($order->status === 'pending' ? 'warning' : 'secondary')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Order Date:</strong> {{ $order->created_at->format('d M Y H:i') }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Payment Status:</strong> 
                                    <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Payment Method:</strong> {{ $order->payment_method ?? 'Not specified' }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Total Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Order Items</h5>
                            <ul class="list-group">
                                @foreach($order->items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $item->product->name }}
                                    <span class="badge badge-primary badge-pill">
                                        x{{ $item->quantity }}
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    @if($order->devices->isNotEmpty())
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>Assigned Devices</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Device Name</th>
                                                <th>MQTT Topic</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->devices as $device)
                                            <tr>
                                                <td>{{ $device->device_name }}</td>
                                                <td><code>{{ $device->mqtt_topic }}</code></td>
                                                <td>
                                                    <span class="badge badge-{{ $device->status === 'active' ? 'success' : ($device->status === 'inactive' ? 'secondary' : 'warning') }}">
                                                        {{ ucfirst($device->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('client.devices.show', $device) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('client.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Orders
                        </a>
                        
                        @if($order->payment_status === 'unpaid' && $order->status !== 'cancelled')
                            <a href="{{ route('client.payments.create', $order) }}" class="btn btn-success">
                                <i class="fas fa-credit-card mr-1"></i> Proceed to Payment
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}

@extends('client.layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@push('styles')
<style>
    .order-detail-card {
        background-color: var(--card-bg);
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }
    
    .order-header {
        background-color: rgba(78, 115, 223, 0.05);
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .order-body {
        padding: 1.5rem;
    }
    
    .info-card {
        background-color: var(--card-bg);
        border-radius: 0.5rem;
        border: 1px solid var(--border-color);
        margin-bottom: 1.5rem;
    }
    
    .info-card-header {
        padding: 0.75rem 1.25rem;
        background-color: rgba(78, 115, 223, 0.05);
        border-bottom: 1px solid var(--border-color);
        font-weight: 600;
    }
    
    .info-card-body {
        padding: 1.25rem;
    }
    
    .info-item {
        padding: 0.75rem 0;
        border-bottom: 1px dashed var(--border-color);
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-weight: 600;
        color: var(--text-color);
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .status-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
    
    .device-table {
        background-color: var(--card-bg);
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .device-table th {
        background-color: rgba(78, 115, 223, 0.05);
        border-bottom: 1px solid var(--border-color);
        font-weight: 600;
    }
    
    .device-table td, .device-table th {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
    }
    
    .device-table tr:last-child td {
        border-bottom: none;
    }
    
    .empty-devices {
        background-color: var(--card-bg);
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
    }
    
    .empty-devices-icon {
        font-size: 2.5rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="order-detail-card">
                <div class="order-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detail Pesanan #{{ $order->order_number }}</h4>
                    <span class="status-badge" style="background-color: rgba(78, 115, 223, 0.1); color: var(--primary-color);">
                        <span class="status-dot" style="background-color: var(--primary-color);"></span>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                
                <div class="order-body">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Pesanan
                                </div>
                                <div class="info-card-body">
                                    <div class="info-item">
                                        <div class="info-label">Tanggal Pesanan</div>
                                        <div class="info-value">{{ $order->created_at->format('d M Y H:i') }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Status Pembayaran</div>
                                        <div class="info-value">
                                            <span class="status-badge" style="background-color: rgba(28, 200, 138, 0.1); color: var(--success-color);">
                                                <span class="status-dot" style="background-color: var(--success-color);"></span>
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Metode Pembayaran</div>
                                        <div class="info-value">{{ $order->payment_method ?? 'Belum ditentukan' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Total Pembayaran</div>
                                        <div class="info-value text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-boxes me-2"></i>Item Pesanan
                                </div>
                                <div class="info-card-body">
                                    @foreach($order->items as $item)
                                    <div class="info-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="info-value">{{ $item->product->name }}</div>
                                            <small class="text-muted">Rp {{ number_format($item->price, 0, ',', '.') }} per item</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">x{{ $item->quantity }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->devices->isNotEmpty())
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-microchip me-2"></i>Perangkat Terkait</h5>
                            <div class="device-table table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama Perangkat</th>
                                            <th>Topik MQTT</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->devices as $device)
                                        <tr>
                                            <td>{{ $device->device_name }}</td>
                                            <td><code>{{ $device->mqtt_topic }}</code></td>
                                            <td>
                                                <span class="status-badge" style="background-color: rgba(28, 200, 138, 0.1); color: var(--success-color);">
                                                    <span class="status-dot" style="background-color: var(--success-color);"></span>
                                                    {{ ucfirst($device->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('client.devices.show', $device) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="empty-devices">
                            <i class="fas fa-microchip empty-devices-icon"></i>
                            <h5 class="mb-2">Belum ada perangkat</h5>
                            <p class="text-muted">Perangkat akan muncul setelah pembayaran selesai dan admin mengaktifkannya</p>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('client.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Pesanan
                        </a>
                        
                        @if($order->payment_status === 'unpaid' && $order->status !== 'cancelled')
                            <a href="{{ route('client.payments.create', $order) }}" class="btn btn-primary">
                                <i class="fas fa-credit-card me-2"></i> Lanjutkan Pembayaran
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
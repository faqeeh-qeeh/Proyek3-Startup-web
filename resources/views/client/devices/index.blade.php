{{-- @extends('client.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Devices</h5>
                    <a href="{{ route('client.orders.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Buy New Device
                    </a>
                </div>

                <div class="card-body">
                    @if($devices->isEmpty())
                        <div class="alert alert-info text-center">
                            You don't have any devices yet. <a href="{{ route('client.orders.create') }}">Buy your first device</a> to get started.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Device Name</th>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>MQTT Topic</th>
                                        <th>Order Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($devices as $device)
                                    <tr>
                                        <td>{{ $device->device_name }}</td>
                                        <td>{{ $device->product->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $device->status === 'active' ? 'success' : ($device->status === 'inactive' ? 'secondary' : 'warning') }}">
                                                {{ ucfirst($device->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $device->mqtt_topic }}</code>
                                        </td>
                                        <td>{{ $device->created_at->format('d M Y') }}</td>
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

                        @if($devices->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $devices->links() }}
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

@section('title', 'Perangkat Saya')

@push('styles')
<style>
    .device-card {
        border: none;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
        background-color: var(--card-bg);
    }
    
    .device-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .device-header {
        background-color: rgba(var(--primary-rgb), 0.1);
        border-bottom: none;
        padding: 1rem 1.25rem;
    }
    
    .device-table {
        color: var(--text-color);
    }
    
    .device-table th {
        background-color: rgba(var(--primary-rgb), 0.05);
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
    }
    
    .device-table td, .device-table th {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid var(--border-color);
    }
    
    .badge-status {
        padding: 0.35rem 0.65rem;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    .badge-active {
        background-color: rgba(var(--success-rgb), 0.1);
        color: var(--success-color);
    }
    
    .badge-inactive {
        background-color: rgba(var(--secondary-rgb), 0.1);
        color: var(--secondary-color);
    }
    
    .badge-maintenance {
        background-color: rgba(var(--warning-rgb), 0.1);
        color: var(--warning-color);
    }
    
    .empty-device {
        background-color: var(--card-bg);
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
    }
    
    .empty-device i {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }
    
    .mqtt-topic {
        font-family: monospace;
        background-color: rgba(0,0,0,0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Perangkat Saya</h2>
        <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Beli Perangkat Baru
        </a>
    </div>

    <div class="card device-card">
        <div class="card-header device-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Perangkat IoT</h5>
            <div class="badge bg-primary rounded-pill">
                {{ $devices->total() }} Perangkat
            </div>
        </div>

        <div class="card-body">
            @if($devices->isEmpty())
                <div class="empty-device">
                    <i class="fas fa-microchip"></i>
                    <h4 class="mb-3">Belum Ada Perangkat</h4>
                    <p class="text-muted mb-4">Anda belum memiliki perangkat. Beli perangkat pertama Anda untuk memulai monitoring.</p>
                    <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i> Beli Sekarang
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table device-table">
                        <thead>
                            <tr>
                                <th>Nama Perangkat</th>
                                <th>Produk</th>
                                <th>Status</th>
                                <th>Topik MQTT</th>
                                <th>Tanggal Pembelian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devices as $device)
                            <tr>
                                <td class="fw-semibold">{{ $device->device_name }}</td>
                                <td>{{ $device->product->name }}</td>
                                <td>
                                    @if($device->status === 'active')
                                        <span class="badge badge-status badge-active">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Aktif
                                        </span>
                                    @elseif($device->status === 'inactive')
                                        <span class="badge badge-status badge-inactive">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Nonaktif
                                        </span>
                                    @else
                                        <span class="badge badge-status badge-maintenance">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Perawatan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="mqtt-topic">{{ $device->mqtt_topic }}</span>
                                </td>
                                <td>{{ $device->created_at->translatedFormat('d F Y') }}</td>
                                <td>
                                    <a href="{{ route('client.devices.show', $device) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($devices->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $devices->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0">Client Details</h1>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-user text-white fa-3x"></i>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $client->full_name }}</h4>
                    <p class="text-muted mb-2">{{ $client->username }}</p>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-success">{{ $client->devices->count() }} Devices</span>
                        <span class="badge bg-info">{{ $client->orders->count() }} Orders</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Full Name</label>
                                <p class="fw-semibold">{{ $client->full_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Username</label>
                                <p class="fw-semibold">{{ $client->username }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <p class="fw-semibold">{{ $client->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">WhatsApp Number</label>
                                <p class="fw-semibold">{{ $client->whatsapp_number }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Gender</label>
                                <p class="fw-semibold">{{ ucfirst($client->gender) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Birth Date</label>
                                <p class="fw-semibold">{{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->format('d F Y') : '-' }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label text-muted">Address</label>
                                <p class="fw-semibold">{{ $client->address ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Registered At</label>
                                <p class="fw-semibold">{{ $client->created_at->format('d F Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Last Updated</label>
                                <p class="fw-semibold">{{ $client->updated_at->format('d F Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-microchip me-2"></i>Client Devices</h5>
                    <span class="badge bg-primary">{{ $client->devices->count() }}</span>
                </div>
                <div class="card-body">
                    @forelse($client->devices as $device)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-microchip fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $device->device_name }}</h6>
                            <small class="text-muted">{{ $device->mqtt_topic }}</small>
                        </div>
                        <div>
                            <span class="badge bg-{{ $device->isActive() ? 'success' : 'secondary' }}">
                                {{ ucfirst($device->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <i class="fas fa-microchip-slash fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No devices registered</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Client Orders</h5>
                    <span class="badge bg-info">{{ $client->orders->count() }}</span>
                </div>
                <div class="card-body">
                    @forelse($client->orders as $order)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-receipt fa-2x text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Order #{{ $order->order_number }}</h6>
                            <small class="text-muted">{{ $order->created_at->format('d M Y') }}</small>
                        </div>
                        <div>
                            <span class="badge bg-{{ $order->isPaid() ? 'success' : 'warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <i class="fas fa-cart-arrow-down fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No orders found</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
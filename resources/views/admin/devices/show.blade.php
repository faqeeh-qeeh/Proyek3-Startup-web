{{-- @extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Device Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.devices.index') }}">Devices</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-microchip me-1"></i>
                    {{ $device->device_name }}
                </div>
                <div>
                    <span class="badge bg-{{ $device->status == 'active' ? 'success' : ($device->status == 'maintenance' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($device->status) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Device Information</h5>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-1"><strong>MQTT Topic:</strong> <code>{{ $device->mqtt_topic }}</code></p>
                            <p class="mb-1"><strong>Product:</strong> {{ $device->product->name }}</p>
                            <p class="mb-1"><strong>Order #:</strong> {{ $device->order->order_number }}</p>
                            <p class="mb-0"><strong>Assigned At:</strong> {{ $device->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Client Information</h5>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-1"><strong>Name:</strong> {{ $device->client->full_name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $device->client->email }}</p>
                            <p class="mb-1"><strong>WhatsApp:</strong> {{ $device->client->whatsapp_number }}</p>
                            <p class="mb-0"><strong>Address:</strong> {{ $device->client->address }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($device->description)
            <h5>Description</h5>
            <div class="card bg-light mb-4">
                <div class="card-body">
                    {{ $device->description }}
                </div>
            </div>
            @endif
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.devices.edit', $device->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <form action="{{ route('admin.devices.destroy', $device->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this device?')">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </form>
                <a href="{{ route('admin.devices.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection --}}


@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0">Device Details</h1>
        <a href="{{ route('admin.devices.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Devices
        </a>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.devices.index') }}">Devices</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                        <i class="fas fa-microchip text-primary fa-lg"></i>
                    </div>
                </div>
                <div>
                    <h5 class="mb-0">{{ $device->device_name }}</h5>
                    <small class="text-muted">Device ID: {{ $device->id }}</small>
                </div>
            </div>
            <div>
                <span class="badge rounded-pill bg-{{ $device->status == 'active' ? 'success' : ($device->status == 'maintenance' ? 'warning' : 'secondary') }}">
                    <i class="fas fa-circle me-1 small"></i>
                    {{ ucfirst($device->status) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-1 text-primary"></i> Device Information</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span class="fw-semibold">MQTT Topic:</span>
                                    <code class="text-primary">{{ $device->mqtt_topic }}</code>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span class="fw-semibold">Product:</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $device->product->name }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span class="fw-semibold">Order #:</span>
                                    <span>{{ $device->order->order_number }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span class="fw-semibold">Assigned At:</span>
                                    <span>{{ $device->created_at->format('d M Y, H:i') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span class="fw-semibold">Last Updated:</span>
                                    <span>{{ $device->updated_at->format('d M Y, H:i') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0"><i class="fas fa-user me-1 text-info"></i> Client Information</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span class="fw-semibold">Name:</span>
                                    <span>{{ $device->client->full_name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span class="fw-semibold">Email:</span>
                                    <a href="mailto:{{ $device->client->email }}" class="text-decoration-none">
                                        {{ $device->client->email }}
                                    </a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span class="fw-semibold">WhatsApp:</span>
                                    <a href="https://wa.me/{{ $device->client->whatsapp_number }}" target="_blank" class="text-decoration-none">
                                        {{ $device->client->whatsapp_number }}
                                    </a>
                                </li>
                                <li class="list-group-item border-0 px-0 pt-2 pb-0">
                                    <span class="fw-semibold">Address:</span>
                                    <p class="mb-0 text-muted small">{{ $device->client->address }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($device->description)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="fas fa-align-left me-1 text-secondary"></i> Description</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $device->description }}</p>
                </div>
            </div>
            @endif
            
            <div class="d-flex justify-content-between border-top pt-4">
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt me-1"></i> Delete Device
                </button>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.devices.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <a href="{{ route('admin.devices.edit', $device->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit Device
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this device? This action cannot be undone.</p>
                <p class="fw-semibold">{{ $device->device_name }}</p>
                <p class="small text-muted">Device ID: {{ $device->id }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.devices.destroy', $device->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Delete Device
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header {
        border-bottom: 1px solid var(--border-color);
    }
    
    .list-group-item {
        background-color: transparent;
    }
    
    code {
        color: var(--primary-color);
        background-color: rgba(var(--primary-color), 0.1);
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        word-break: break-word;
    }
    
    /* Dark mode adjustments */
    [data-bs-theme="dark"] .card {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    [data-bs-theme="dark"] code {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endpush
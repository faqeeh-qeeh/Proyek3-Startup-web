@extends('admin.layouts.app')

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
@endsection
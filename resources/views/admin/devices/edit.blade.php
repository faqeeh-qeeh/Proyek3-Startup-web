@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Device</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.devices.index') }}">Devices</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.devices.show', $device->id) }}">{{ $device->device_name }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-microchip me-1"></i>
            Edit Device
        </div>
        <div class="card-body">
            <form action="{{ route('admin.devices.update', $device->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="device_name" class="form-label">Device Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('device_name') is-invalid @enderror" id="device_name" name="device_name" value="{{ old('device_name', $device->device_name) }}" required>
                        @error('device_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $device->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $device->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ old('status', $device->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="mqtt_topic" class="form-label">MQTT Topic <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">/project/startup/client/</span>
                        <input type="text" class="form-control @error('mqtt_topic') is-invalid @enderror" 
                               id="mqtt_topic" name="mqtt_topic" 
                               value="{{ old('mqtt_topic', str_replace('/project/startup/client/', '', $device->mqtt_topic)) }}" 
                               required pattern="[a-zA-Z0-9_-]+" title="Only letters, numbers, underscore and hyphen">
                    </div>
                    @error('mqtt_topic')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                    <small class="text-muted">Example: client-{{ $device->client->id }}-device-{{ $device->id }}</small>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $device->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Device
                    </button>
                    <a href="{{ route('admin.devices.show', $device->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
{{-- @extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Devices</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Devices</li>
    </ol>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-microchip me-1"></i>
                    Device List
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.devices.index') }}">All Devices</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.devices.index') }}?status=active">Active</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.devices.index') }}?status=inactive">Inactive</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.devices.index') }}?status=maintenance">Maintenance</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="devicesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Device Name</th>
                            <th>Client</th>
                            <th>Product</th>
                            <th>MQTT Topic</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $device->device_name }}</td>
                            <td>{{ $device->client->full_name }}</td>
                            <td>{{ $device->product->name }}</td>
                            <td><code>{{ $device->mqtt_topic }}</code></td>
                            <td>
                                <span class="badge bg-{{ $device->status == 'active' ? 'success' : ($device->status == 'maintenance' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.devices.show', $device->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.devices.edit', $device->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.devices.destroy', $device->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this device?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No devices found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $devices->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    code {
        color: #d63384;
        word-wrap: break-word;
    }
</style>
@endpush

@push('scripts')
<script>
    // Inisialisasi DataTable
    document.addEventListener('DOMContentLoaded', function() {
        $('#devicesTable').DataTable({
            responsive: true,
            columnDefs: [
                { orderable: false, targets: -1 } // Disable sorting for actions column
            ],
            order: [[0, 'asc']] // Default order by ID
        });
    });
</script>
@endpush --}}

@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0">Device Management</h1>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
            <i class="fas fa-plus-circle me-1"></i> Add Device
        </a>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Devices</li>
    </ol>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <div>
            {{ session('success') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-microchip me-2"></i>
                <h5 class="mb-0">Device Registry</h5>
            </div>
            <div class="d-flex">
                <div class="input-group input-group-sm me-2" style="width: 200px;">
                    <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Search devices..." id="searchInput">
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.devices.index') }}">All Devices</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.devices.index') }}?status=active">Active</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.devices.index') }}?status=inactive">Inactive</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.devices.index') }}?status=maintenance">Maintenance</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="devicesTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Device</th>
                            <th>Client</th>
                            <th>Product</th>
                            <th width="200">MQTT Topic</th>
                            <th width="120">Status</th>
                            <th width="150" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-microchip text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $device->device_name }}</h6>
                                        <small class="text-muted">ID: {{ $device->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-info"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $device->client->full_name }}</h6>
                                        <small class="text-muted">Client ID: {{ $device->client->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $device->product->name }}
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" data-bs-toggle="tooltip" title="{{ $device->mqtt_topic }}">
                                    <code class="text-primary">{{ $device->mqtt_topic }}</code>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $device->status == 'active' ? 'success' : ($device->status == 'maintenance' ? 'warning' : 'secondary') }}">
                                    <i class="fas fa-circle me-1 small"></i>
                                    {{ ucfirst($device->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.devices.show', $device->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.devices.edit', $device->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="tooltip" 
                                            title="Delete"
                                            onclick="confirmDelete('{{ route('admin.devices.destroy', $device->id) }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-microchip fa-3x text-muted mb-2"></i>
                                    <h5 class="text-muted">No devices found</h5>
                                    <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                                        <i class="fas fa-plus me-1"></i> Add New Device
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($devices->hasPages())
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $devices->firstItem() }} to {{ $devices->lastItem() }} of {{ $devices->total() }} entries
                </div>
                {{ $devices->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Add Device Modal -->
<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDeviceModalLabel">Add New Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="device_name" class="form-label">Device Name</label>
                            <input type="text" class="form-control" id="device_name" name="device_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client</label>
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                <!-- Clients would be populated here -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Select Product</option>
                                <!-- Products would be populated here -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="mqtt_topic" class="form-label">MQTT Topic</label>
                            <div class="input-group">
                                <span class="input-group-text">/project/startup/client/</span>
                                <input type="text" class="form-control" id="mqtt_topic" name="mqtt_topic" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Device</button>
                </div>
            </form>
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
                <p class="fw-semibold" id="deviceNameToDelete"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
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
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom-width: 2px;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
    }
    
    code {
        color: var(--primary-color);
        background-color: rgba(var(--primary-color), 0.1);
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
    
    /* Dark mode adjustments */
    [data-bs-theme="dark"] code {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const value = this.value.toLowerCase();
                const rows = document.querySelectorAll('#devicesTable tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(value) ? '' : 'none';
                });
            });
        }
    });
    
    // Delete confirmation
    function confirmDelete(url, deviceName) {
        const deleteForm = document.getElementById('deleteForm');
        const deviceNameElement = document.getElementById('deviceNameToDelete');
        
        deleteForm.action = url;
        deviceNameElement.textContent = deviceName;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endpush
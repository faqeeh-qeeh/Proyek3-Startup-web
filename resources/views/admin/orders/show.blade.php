{{-- @extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Order Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-receipt me-1"></i>
                    Order #{{ $order->order_number }}
                </div>
                <div>
                    <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }} me-2">
                        {{ ucfirst($order->status) }}
                    </span>
                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'failed' ? 'danger' : 'secondary') }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Client Information</h5>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-1"><strong>Name:</strong> {{ $order->client->full_name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $order->client->email }}</p>
                            <p class="mb-1"><strong>WhatsApp:</strong> {{ $order->client->whatsapp_number }}</p>
                            <p class="mb-0"><strong>Address:</strong> {{ $order->client->address }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Order Information</h5>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-1"><strong>Order Date:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                            <p class="mb-1"><strong>Payment Method:</strong> {{ $order->payment_method ?? 'Not specified' }}</p>
                            <p class="mb-1"><strong>Midtrans ID:</strong> {{ $order->midtrans_transaction_id ?? '-' }}</p>
                            <p class="mb-0"><strong>Total Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <h5>Order Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total</strong></td>
                            <td><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            @if($order->notes)
            <div class="mt-3">
                <h5>Admin Notes</h5>
                <div class="card bg-light">
                    <div class="card-body">
                        {{ $order->notes }}
                    </div>
                </div>
            </div>
            @endif
            
            @if($order->status != 'completed' && $order->status != 'cancelled')
            <div class="mt-4">
                <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#statusModal">
                    <i class="fas fa-edit me-1"></i> Update Status
                </button>
                
                @if(!$order->devices->count())
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignMqttModal">
                    <i class="fas fa-microchip me-1"></i> Assign MQTT Topic
                </button>
                @endif
            </div>
            
            <!-- Status Update Modal -->
            <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Order Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select class="form-select" id="payment_status" name="payment_status" required>
                                        <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Assign MQTT Modal -->
            <div class="modal fade" id="assignMqttModal" tabindex="-1" aria-labelledby="assignMqttModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.orders.assign-mqtt', $order->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="assignMqttModalLabel">Assign MQTT Topics</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Please assign unique MQTT topics for each device in this order:</p>
                                
                                @foreach($order->items as $item)
                                <div class="mb-3">
                                    <label for="mqtt_topic_{{ $item->id }}" class="form-label">{{ $item->product->name }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">/project/startup/client/</span>
                                        <input type="text" class="form-control" id="mqtt_topic_{{ $item->id }}" 
                                               name="mqtt_topics[{{ $item->id }}]" required
                                               placeholder="unique-id" pattern="[a-zA-Z0-9_-]+" title="Only letters, numbers, underscore and hyphen">
                                    </div>
                                    <small class="text-muted">Example: client-{{ $order->client->id }}-device-{{ $item->id }}</small>
                                </div>
                                @endforeach
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Assign Topics</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            
            @if($order->devices->count())
            <div class="mt-4">
                <h5>Assigned Devices</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Device Name</th>
                                <th>MQTT Topic</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->devices as $device)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $device->device_name }}</td>
                                <td><code>{{ $device->mqtt_topic }}</code></td>
                                <td>
                                    <span class="badge bg-{{ $device->status == 'active' ? 'success' : ($device->status == 'maintenance' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($device->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.devices.edit', $device->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Orders
            </a>
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
@endpush --}}

@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0">Order Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Orders
            </a>
            @if($order->status != 'completed' && $order->status != 'cancelled')
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#statusModal">
                <i class="fas fa-edit me-1"></i> Update Status
            </button>
            @endif
        </div>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
        <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
    </ol>
    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-receipt me-2"></i>
                <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
            </div>
            <div class="d-flex gap-2">
                <span class="badge rounded-pill bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                    {{ ucfirst($order->status) }}
                </span>
                <span class="badge rounded-pill bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'failed' ? 'danger' : 'secondary') }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0"><i class="fas fa-user me-1"></i> Client Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted d-block">Name</small>
                                <p class="mb-0 fw-semibold">{{ $order->client->full_name }}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Email</small>
                                <p class="mb-0">{{ $order->client->email }}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">WhatsApp</small>
                                <p class="mb-0">{{ $order->client->whatsapp_number }}</p>
                            </div>
                            <div>
                                <small class="text-muted d-block">Address</small>
                                <p class="mb-0">{{ $order->client->address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-1"></i> Order Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted d-block">Order Date</small>
                                <p class="mb-0">{{ $order->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Payment Method</small>
                                <p class="mb-0">{{ $order->payment_method ?? 'Not specified' }}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Midtrans ID</small>
                                <p class="mb-0">{{ $order->midtrans_transaction_id ?? '-' }}</p>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Amount</small>
                                <p class="mb-0 fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-boxes me-1"></i> Order Items</h5>
                    <span class="badge bg-primary rounded-pill">{{ $order->items->count() }} items</span>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Product</th>
                                    <th width="120" class="text-end">Price</th>
                                    <th width="100" class="text-center">Qty</th>
                                    <th width="140" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="rounded me-2" 
                                                 width="40" height="40" style="object-fit: cover;">
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $item->product->name }}</h6>
                                                <small class="text-muted">{{ $item->product->category->name ?? 'Uncategorized' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-light">
                                    <td colspan="4" class="text-end fw-bold">Total</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            @if($order->notes)
            <div class="mb-4">
                <h5><i class="fas fa-sticky-note me-1"></i> Admin Notes</h5>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if($order->devices->count())
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-microchip me-1"></i> Assigned Devices</h5>
                    <span class="badge bg-primary rounded-pill">{{ $order->devices->count() }} devices</span>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Device Name</th>
                                    <th>MQTT Topic</th>
                                    <th width="120">Status</th>
                                    <th width="80">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->devices as $device)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $device->device_name }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <code class="me-2">{{ $device->mqtt_topic }}</code>
                                            <button class="btn btn-sm btn-outline-secondary btn-copy" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Copy to clipboard"
                                                    data-clipboard-text="{{ $device->mqtt_topic }}">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $device->status == 'active' ? 'success' : ($device->status == 'maintenance' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($device->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.devices.edit', $device->id) }}" 
                                           class="btn btn-sm btn-outline-warning"
                                           data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
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
            
            @if($order->status != 'completed' && $order->status != 'cancelled' && !$order->devices->count())
            <div class="d-grid">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignMqttModal">
                    <i class="fas fa-microchip me-1"></i> Assign MQTT Topics
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Order Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="payment_status" name="payment_status" required>
                            <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign MQTT Modal -->
<div class="modal fade" id="assignMqttModal" tabindex="-1" aria-labelledby="assignMqttModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.assign-mqtt', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assignMqttModalLabel">Assign MQTT Topics</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Please assign unique MQTT topics for each device in this order:</p>
                    
                    @foreach($order->items as $item)
                    <div class="mb-3">
                        <label for="mqtt_topic_{{ $item->id }}" class="form-label">{{ $item->product->name }}</label>
                        <div class="input-group">
                            <span class="input-group-text">/project/startup/client/</span>
                            <input type="text" class="form-control" id="mqtt_topic_{{ $item->id }}" 
                                   name="mqtt_topics[{{ $item->id }}]" required
                                   placeholder="unique-id" pattern="[a-zA-Z0-9_-]+" title="Only letters, numbers, underscore and hyphen">
                        </div>
                        <small class="text-muted">Example: client-{{ $order->client->id }}-device-{{ $item->id }}</small>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Topics</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header h6 {
        font-weight: 600;
    }
    
    code {
        color: var(--primary-color);
        background-color: rgba(var(--primary-color), 0.1);
        padding: 2px 4px;
        border-radius: 4px;
        font-size: 0.875em;
    }
    
    .btn-copy {
        padding: 0.15rem 0.35rem;
        font-size: 0.75rem;
    }
    
    /* Dark mode adjustments */
    [data-bs-theme="dark"] code {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@push('scripts')
<!-- Clipboard.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialize clipboard.js
        new ClipboardJS('.btn-copy');
        
        // Show feedback when copied
        document.querySelectorAll('.btn-copy').forEach(btn => {
            btn.addEventListener('click', function() {
                const originalTitle = this.getAttribute('data-bs-original-title');
                this.setAttribute('data-bs-original-title', 'Copied!');
                const tooltip = bootstrap.Tooltip.getInstance(this);
                tooltip.show();
                
                setTimeout(() => {
                    this.setAttribute('data-bs-original-title', originalTitle);
                    tooltip.hide();
                }, 1000);
            });
        });
    });
</script>
@endpush
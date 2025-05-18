{{-- @extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Orders</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Orders</li>
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
                    <i class="fas fa-shopping-cart me-1"></i>
                    Order List
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}">All Orders</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?status=pending">Pending</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?status=processing">Processing</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?status=completed">Completed</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?status=cancelled">Cancelled</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->client->full_name }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'failed' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($order->status != 'completed' && $order->status != 'cancelled')
                                    <button class="btn btn-sm btn-primary" title="Update Status" data-bs-toggle="modal" data-bs-target="#statusModal{{ $order->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Status Update Modal -->
                        <div class="modal fade" id="statusModal{{ $order->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $order->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="statusModalLabel{{ $order->id }}">Update Order Status</h5>
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
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No orders found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi DataTable
    document.addEventListener('DOMContentLoaded', function() {
        $('#ordersTable').DataTable({
            responsive: true,
            columnDefs: [
                { orderable: false, targets: -1 } // Disable sorting for actions column
            ],
            order: [[2, 'desc']] // Default order by date
        });
    });
</script>
@endpush --}}

@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0">Order Management</h1>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-file-export me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.orders.export.excel') }}"><i class="fas fa-file-excel me-2"></i> Excel</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.orders.export.pdf') }}"><i class="fas fa-file-pdf me-2"></i> PDF</a></li>
                </ul>
            </div>
        </div>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Orders</li>
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
                <i class="fas fa-shopping-cart me-2"></i>
                <h5 class="mb-0">Order List</h5>
            </div>
            <div class="d-flex">
                <div class="input-group input-group-sm me-2" style="width: 200px;">
                    <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Search orders..." id="searchInput">
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}">All Orders</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?status=pending">Pending</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?status=processing">Processing</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?status=completed">Completed</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?status=cancelled">Cancelled</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?payment_status=paid">Paid</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?payment_status=unpaid">Unpaid</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="ordersTable">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="fw-semibold">{{ $order->order_number }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $order->client->full_name }}</h6>
                                        <small class="text-muted">{{ $order->client->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td class="fw-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'failed' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($order->status != 'completed' && $order->status != 'cancelled')
                                    <button class="btn btn-sm btn-outline-warning" 
                                            title="Update Status" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#statusModal{{ $order->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-2"></i>
                                    <h5 class="text-muted">No orders found</h5>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
                </div>
                {{ $orders->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Status Update Modals -->
@foreach($orders as $order)
@if($order->status != 'completed' && $order->status != 'cancelled')
<div class="modal fade" id="statusModal{{ $order->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel{{ $order->id }}">Update Order Status</h5>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
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
    
    .badge.rounded-pill {
        padding: 0.35em 0.65em;
    }
    
    .empty-state {
        opacity: 0.7;
    }
    
    /* Dark mode adjustments */
    [data-bs-theme="dark"] .table th {
        background-color: rgba(var(--primary-color), 0.05);
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
        
        // Simple search functionality
        const searchInput = document.getElementById('searchInput');
        const ordersTable = document.getElementById('ordersTable');
        const tableRows = ordersTable.getElementsByTagName('tr');
        
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                
                for (let i = 1; i < tableRows.length; i++) {
                    const row = tableRows[i];
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchValue) ? '' : 'none';
                }
            });
        }
    });
</script>
@endpush
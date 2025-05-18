@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0">Product Management</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add New Product
        </a>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Products</li>
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
                <i class="fas fa-boxes me-2"></i>
                <h5 class="mb-0">Product Catalog</h5>
            </div>
            <div class="d-flex">
                <div class="input-group input-group-sm me-2" style="width: 200px;">
                    <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Search products..." id="searchInput">
                </div>
                {{-- <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">All Products</a></li>
                        <li><a class="dropdown-item" href="#">Active Only</a></li>
                        <li><a class="dropdown-item" href="#">Inactive Only</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Price: Low to High</a></li>
                        <li><a class="dropdown-item" href="#">Price: High to Low</a></li>
                    </ul>
                </div> --}}
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="productsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Product</th>
                            <th width="120">Price</th>
                            <th width="100">Stock</th>
                            <th width="120">Status</th>
                            <th width="150" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                             class="rounded" width="40" height="40" style="object-fit: cover;">
                                        @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-box-open text-muted"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $product->name }}</h6>
                                        <small class="text-muted">{{ $product->category->name ?? 'Uncategorized' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-semibold">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $product->stock > 0 ? 'info' : 'secondary' }}">
                                    {{ $product->stock }} in stock
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.products.show', $product->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="tooltip" 
                                                title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-box-open fa-3x text-muted mb-2"></i>
                                    <h5 class="text-muted">No products found</h5>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary mt-2">
                                        <i class="fas fa-plus me-1"></i> Add New Product
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
        <div class="card-footer bg-transparent">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
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
    
    .empty-state {
        opacity: 0.7;
    }
    
    /* Dark mode adjustments */
    [data-bs-theme="dark"] .card {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
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
        const productsTable = document.getElementById('productsTable');
        const tableRows = productsTable.getElementsByTagName('tr');
        
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
        
        // Status toggle functionality (example)
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const productId = this.dataset.id;
                const isActive = this.checked;
                
                // Here you would typically make an AJAX call to update the status
                console.log(`Toggling product ${productId} to ${isActive ? 'active' : 'inactive'}`);
                
                // You could use fetch API to update the status
                /*
                fetch(`/admin/products/${productId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ is_active: isActive })
                })
                .then(response => response.json())
                .then(data => {
                    // Handle response
                });
                */
            });
        });
    });
</script>
@endpush
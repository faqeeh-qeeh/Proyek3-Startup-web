{{-- @extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Product</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-box me-1"></i>
            Edit Product
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="price" class="form-label">Price (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
                        @error('price')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="image" class="form-label">Product Image</label>
                        <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*">
                        @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                        
                        @if($product->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="100" class="img-thumbnail">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                <label class="form-check-label" for="remove_image">
                                    Remove current image
                                </label>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-4 pt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active Product</label>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}


@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0">Edit Product</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Products
        </a>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-box me-2"></i>
                <h5 class="mb-0">Edit Product Details</h5>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                        </div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="price" class="form-label">Price (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent">Rp</span>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', $product->price) }}" 
                                   min="0" step="100" required>
                            @error('price')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                    </div>
                    @enderror
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="image" class="form-label">Product Image</label>
                        <div class="file-upload-input">
                            <input class="form-control @error('image') is-invalid @enderror" 
                                   type="file" id="image" name="image" accept="image/*">
                            <small class="text-muted">Max 2MB (JPEG, PNG, JPG, GIF)</small>
                            @error('image')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                            </div>
                            @enderror
                            
                            @if($product->image)
                            <div class="mt-3">
                                <div class="current-image-container">
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="img-thumbnail rounded"
                                         style="max-height: 150px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" 
                                               id="remove_image" name="remove_image" value="1">
                                        <label class="form-check-label" for="remove_image">
                                            Remove current image
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Product Status</label>
                            <p class="small text-muted mb-0">
                                Toggle to {{ $product->is_active ? 'deactivate' : 'activate' }} this product
                            </p>
                        </div>
                        
                        <div class="card bg-light border-0 p-3">
                            <h6 class="mb-2">Product Details</h6>
                            <div class="small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Created:</span>
                                    <span>{{ $product->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Last Updated:</span>
                                    <span>{{ $product->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between border-top pt-4">
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash-alt me-1"></i> Delete Product
                    </button>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Product
                        </button>
                    </div>
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
                <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                <p class="fw-semibold">{{ $product->name }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Delete Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .file-upload-input {
        border: 1px dashed var(--border-color);
        border-radius: 0.5rem;
        padding: 1rem;
        background-color: rgba(var(--primary-color), 0.03);
    }
    
    .current-image-container {
        position: relative;
        display: inline-block;
    }
    
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    /* Dark mode adjustments */
    [data-bs-theme="dark"] .file-upload-input {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: var(--border-color);
    }
    
    [data-bs-theme="dark"] .card.bg-light {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Example form validation
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch the form element
        const form = document.querySelector('.needs-validation');
        
        // Add validation on submit
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
        
        // Price formatting
        const priceInput = document.getElementById('price');
        if (priceInput) {
            priceInput.addEventListener('blur', function() {
                this.value = parseFloat(this.value).toFixed(2);
            });
        }
    });
</script>
@endpush
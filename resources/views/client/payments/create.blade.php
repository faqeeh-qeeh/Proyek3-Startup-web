{{-- @extends('client.layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Detail Pembayaran</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i> Silakan pilih metode pembayaran untuk melanjutkan.
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informasi Pesanan</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Nomor Pesanan:</span>
                                    <strong>{{ $order->order_number }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Tanggal:</span>
                                    <strong>{{ $order->created_at->format('d M Y H:i') }}</strong>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Rincian Harga</h5>
                            <ul class="list-group">
                                @foreach($order->items as $item)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                                    <strong>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</strong>
                                </li>
                                @endforeach
                                <li class="list-group-item d-flex justify-content-between bg-light">
                                    <span class="font-weight-bold">Total</span>
                                    <strong class="font-weight-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <form action="{{ route('client.payments.store', $order) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="payment_method" class="font-weight-bold">Metode Pembayaran</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="">Pilih metode pembayaran</option>
                                <option value="gopay">GoPay</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="credit_card">Kartu Kredit</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya menyetujui <a href="#" data-toggle="modal" data-target="#termsModal">syarat dan ketentuan</a> yang berlaku
                            </label>
                        </div>

                        <div class="text-right mt-4">
                            <a href="{{ route('client.orders.index') }}" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-credit-card mr-2"></i> Lanjutkan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Syarat dan Ketentuan -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Syarat dan Ketentuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>1. Ketentuan Umum</h6>
                <p>Dengan melakukan pembayaran, Anda menyetujui semua syarat dan ketentuan yang berlaku untuk pembelian produk IoT Monitoring dan Kendali Listrik.</p>
                
                <h6 class="mt-4">2. Proses Pembayaran</h6>
                <p>Pembayaran harus diselesaikan dalam waktu 24 jam setelah pesanan dibuat. Jika tidak, pesanan akan dibatalkan secara otomatis.</p>
                
                <h6 class="mt-4">3. Pengiriman Produk</h6>
                <p>Produk fisik akan dikirimkan setelah pembayaran diverifikasi. Akses monitoring akan diberikan setelah admin mengaktifkan perangkat.</p>
                
                <h6 class="mt-4">4. Kebijakan Pengembalian</h6>
                <p>Pengembalian dana hanya dapat dilakukan jika produk tidak sesuai pesanan atau dalam kondisi cacat.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>
@endsection --}}


@extends('client.layouts.app')

@section('title', 'Pembayaran')

@push('styles')
<style>
    .payment-card {
        border: none;
        border-radius: 0.5rem;
        overflow: hidden;
        background-color: var(--card-bg);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }
    
    .payment-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.25rem;
        border-bottom: none;
    }
    
    .payment-alert {
        border-left: 4px solid var(--info-color);
        background-color: rgba(54, 185, 204, 0.1);
        border-radius: 0.375rem;
    }
    
    .payment-list-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border-color);
        background-color: var(--card-bg);
    }
    
    .payment-list-item:last-child {
        border-bottom: none;
    }
    
    .payment-list-item.total {
        background-color: rgba(78, 115, 223, 0.1);
        font-weight: 600;
    }
    
    .payment-method-select {
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-color);
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
    }
    
    .payment-method-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
    
    .terms-check label {
        color: var(--text-color);
    }
    
    .terms-link {
        color: var(--primary-color);
        text-decoration: none;
    }
    
    .terms-link:hover {
        text-decoration: underline;
    }
    
    .payment-section-title {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 1rem;
        position: relative;
        padding-bottom: 0.5rem;
    }
    
    .payment-section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background-color: var(--primary-color);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card payment-card">
                <div class="card-header payment-header">
                    <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i> Detail Pembayaran</h4>
                </div>
                <div class="card-body">
                    <div class="alert payment-alert d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 fs-4 text-info"></i>
                        <div>Silakan pilih metode pembayaran untuk melanjutkan. Pembayaran harus diselesaikan dalam 24 jam.</div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <h5 class="payment-section-title">Informasi Pesanan</h5>
                            <div class="list-group">
                                <div class="payment-list-item">
                                    <span>Nomor Pesanan:</span>
                                    <strong>{{ $order->order_number }}</strong>
                                </div>
                                <div class="payment-list-item">
                                    <span>Tanggal:</span>
                                    <strong>{{ $order->created_at->translatedFormat('d F Y H:i') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="payment-section-title">Rincian Harga</h5>
                            <div class="list-group">
                                @foreach($order->items as $item)
                                <div class="payment-list-item">
                                    <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                                    <strong>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</strong>
                                </div>
                                @endforeach
                                <div class="payment-list-item total">
                                    <span>Total Pembayaran:</span>
                                    <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('client.payments.store', $order) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="payment_method" class="form-label fw-bold">Metode Pembayaran</label>
                            <select class="form-select payment-method-select" id="payment_method" name="payment_method" required>
                                <option value="">Pilih metode pembayaran</option>
                                <option value="gopay">GoPay</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="credit_card">Kartu Kredit</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>

                        <div class="form-check terms-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya menyetujui <a href="#" class="terms-link" data-bs-toggle="modal" data-bs-target="#termsModal">syarat dan ketentuan</a> yang berlaku
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('client.orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-credit-card me-2"></i> Lanjutkan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Syarat dan Ketentuan -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="termsModalLabel"><i class="fas fa-file-contract me-2"></i> Syarat dan Ketentuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="fw-bold text-primary">1. Ketentuan Umum</h6>
                    <p class="text-muted">Dengan melakukan pembayaran, Anda menyetujui semua syarat dan ketentuan yang berlaku untuk pembelian produk IoT Monitoring dan Kendali Listrik.</p>
                </div>
                
                <div class="mb-4">
                    <h6 class="fw-bold text-primary">2. Proses Pembayaran</h6>
                    <p class="text-muted">Pembayaran harus diselesaikan dalam waktu 24 jam setelah pesanan dibuat. Jika tidak, pesanan akan dibatalkan secara otomatis.</p>
                </div>
                
                <div class="mb-4">
                    <h6 class="fw-bold text-primary">3. Pengiriman Produk</h6>
                    <p class="text-muted">Produk fisik akan dikirimkan setelah pembayaran diverifikasi. Akses monitoring akan diberikan setelah admin mengaktifkan perangkat.</p>
                </div>
                
                <div>
                    <h6 class="fw-bold text-primary">4. Kebijakan Pengembalian</h6>
                    <p class="text-muted">Pengembalian dana hanya dapat dilakukan jika produk tidak sesuai pesanan atau dalam kondisi cacat.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>
@endsection
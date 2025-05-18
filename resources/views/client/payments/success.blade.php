{{-- @extends('client.layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    <h2 class="mb-3">Pembayaran Berhasil!</h2>
                    <p class="lead">Terima kasih telah melakukan pembayaran.</p>
                    
                    <div class="card bg-light mb-4" style="max-width: 400px; margin: 0 auto;">
                        <div class="card-body text-left">
                            <h5 class="card-title">Detail Pesanan</h5>
                            <ul class="list-unstyled">
                                <li><strong>No. Pesanan:</strong> {{ $order->order_number }}</li>
                                <li><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</li>
                                <li><strong>Status:</strong> <span class="badge badge-success">Dibayar</span></li>
                                <li><strong>Metode:</strong> {{ $order->payment_method ? ucfirst(str_replace('_', ' ', $order->payment_method)) : '-' }}</li>
                            </ul>
                        </div>
                    </div>

                    <p class="mb-4">
                        Kami telah mengirimkan detail pembayaran ke email <strong>{{ auth('client')->user()->email }}</strong>.
                        Admin akan segera memproses pesanan Anda.
                    </p>

                    <div class="d-flex justify-content-center">
                        <a href="{{ route('client.orders.show', $order) }}" class="btn btn-primary mr-3">
                            <i class="fas fa-file-invoice mr-2"></i> Lihat Detail Pesanan
                        </a>
                        <a href="{{ route('client.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}

@extends('client.layouts.app')

@section('title', 'Pembayaran Berhasil')

@push('styles')
<style>
    .success-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .success-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        background-color: var(--card-bg);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .success-icon {
        font-size: 5rem;
        color: var(--success-color);
        margin-bottom: 1.5rem;
        animation: bounce 1s;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
        40% {transform: translateY(-20px);}
        60% {transform: translateY(-10px);}
    }
    
    .success-title {
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-color);
    }
    
    .success-message {
        color: var(--text-muted);
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }
    
    .detail-card {
        background-color: rgba(28, 200, 138, 0.1);
        border: none;
        border-radius: 10px;
        max-width: 500px;
        margin: 0 auto 2rem;
    }
    
    .detail-title {
        font-weight: 600;
        color: var(--success-color);
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(28, 200, 138, 0.3);
        padding-bottom: 0.5rem;
    }
    
    .detail-item {
        margin-bottom: 0.5rem;
        display: flex;
    }
    
    .detail-label {
        font-weight: 500;
        min-width: 120px;
        color: var(--text-color);
    }
    
    .detail-value {
        color: var(--text-color);
    }
    
    .status-badge {
        background-color: var(--success-color);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .email-notification {
        color: var(--text-muted);
        margin-bottom: 2rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .action-button {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
        margin: 0 0.5rem;
    }
    
    .primary-button {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .primary-button:hover {
        background-color: #3a5bc7;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
    }
    
    .secondary-button {
        border: 1px solid var(--border-color);
        color: var(--text-color);
    }
    
    .secondary-button:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        background-color: transparent;
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="success-container">
        <div class="success-card">
            <div class="card-body text-center p-4 p-lg-5">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                
                <h1 class="success-title">Pembayaran Berhasil!</h1>
                <p class="success-message">Terima kasih telah melakukan pembayaran.</p>
                
                <div class="detail-card card mb-4">
                    <div class="card-body text-left p-4">
                        <h5 class="detail-title"><i class="fas fa-receipt me-2"></i>Detail Pesanan</h5>
                        <div class="detail-item">
                            <span class="detail-label">No. Pesanan:</span>
                            <span class="detail-value">{{ $order->order_number }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Total:</span>
                            <span class="detail-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="status-badge">Dibayar</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Metode:</span>
                            <span class="detail-value">{{ $order->payment_method ? ucfirst(str_replace('_', ' ', $order->payment_method)) : '-' }}</span>
                        </div>
                    </div>
                </div>
                
                <p class="email-notification">
                    <i class="fas fa-envelope me-2"></i>
                    Kami telah mengirimkan detail pembayaran ke email <strong>{{ auth('client')->user()->email }}</strong>.
                    Admin akan segera memproses pesanan Anda.
                </p>
                
                <div class="d-flex justify-content-center flex-wrap">
                    <a href="{{ route('client.orders.show', $order) }}" class="btn primary-button action-button mb-2 mb-sm-0">
                        <i class="fas fa-file-invoice me-2"></i> Lihat Detail Pesanan
                    </a>
                    <a href="{{ route('client.products.index') }}" class="btn secondary-button action-button">
                        <i class="fas fa-home me-2"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
{{-- @extends('client.layouts.app')

@section('title', 'Pembayaran Gagal')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-times-circle fa-5x text-danger"></i>
                    </div>
                    <h2 class="mb-3">Pembayaran Gagal</h2>
                    <p class="lead">Maaf, proses pembayaran Anda tidak berhasil.</p>
                    
                    <div class="card bg-light mb-4" style="max-width: 400px; margin: 0 auto;">
                        <div class="card-body text-left">
                            <h5 class="card-title">Detail Pesanan</h5>
                            <ul class="list-unstyled">
                                <li><strong>No. Pesanan:</strong> {{ $order->order_number }}</li>
                                <li><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</li>
                                <li><strong>Status:</strong> <span class="badge badge-danger">Gagal</span></li>
                            </ul>
                        </div>
                    </div>

                    <p class="mb-4">
                        Silakan coba lagi atau hubungi tim support kami jika masalah berlanjut.
                    </p>

                    <div class="d-flex justify-content-center">
                        <a href="{{ route('client.payments.create', $order) }}" class="btn btn-primary mr-3">
                            <i class="fas fa-credit-card mr-2"></i> Coba Lagi
                        </a>
                        <a href="{{ route('client.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list mr-2"></i> Lihat Pesanan Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}

@extends('client.layouts.app')

@section('title', 'Pembayaran Gagal')

@push('styles')
<style>
    .failure-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .failure-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        background-color: var(--card-bg);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .failure-icon {
        font-size: 5rem;
        color: var(--danger-color);
        margin-bottom: 1.5rem;
        animation: shake 0.5s;
    }
    
    @keyframes shake {
        0%, 100% {transform: translateX(0);}
        20%, 60% {transform: translateX(-5px);}
        40%, 80% {transform: translateX(5px);}
    }
    
    .failure-title {
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-color);
    }
    
    .failure-message {
        color: var(--text-muted);
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }
    
    .detail-card {
        background-color: rgba(231, 74, 59, 0.1);
        border: none;
        border-radius: 10px;
        max-width: 500px;
        margin: 0 auto 2rem;
    }
    
    .detail-title {
        font-weight: 600;
        color: var(--danger-color);
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(231, 74, 59, 0.3);
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
        background-color: var(--danger-color);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .support-message {
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
    
    .support-contact {
        margin-top: 2rem;
        color: var(--text-muted);
        font-size: 0.9rem;
    }
    
    .support-contact a {
        color: var(--primary-color);
        text-decoration: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="failure-container">
        <div class="failure-card">
            <div class="card-body text-center p-4 p-lg-5">
                <div class="failure-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                
                <h1 class="failure-title">Pembayaran Gagal</h1>
                <p class="failure-message">Maaf, proses pembayaran Anda tidak berhasil.</p>
                
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
                            <span class="status-badge">Gagal</span>
                        </div>
                    </div>
                </div>
                
                <p class="support-message">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Silakan coba lagi atau hubungi tim support kami jika masalah berlanjut.
                </p>
                
                <div class="d-flex justify-content-center flex-wrap">
<a href="#" class="btn primary-button action-button mb-2 mb-sm-0" id="retry-button">
    <i class="fas fa-credit-card me-2"></i> Coba Lagi
</a>
                    <a href="{{ route('client.orders.index') }}" class="btn secondary-button action-button">
                        <i class="fas fa-list me-2"></i> Lihat Pesanan Saya
                    </a>
                </div>
                
                <div class="support-contact">
                    Butuh bantuan? <a href="mailto:support@iot-monitoring.com">Hubungi Support</a> atau WA: +62 123 4567 890
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const retryButton = document.getElementById('retry-button');
    
    if (retryButton) {
        retryButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Tampilkan loading
            const originalHtml = retryButton.innerHTML;
            retryButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
            retryButton.disabled = true;
            
            // Kirim request POST via fetch
            fetch("{{ route('client.payments.retry', $order) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.error || 'Terjadi kesalahan saat memproses pembayaran ulang');
                retryButton.innerHTML = originalHtml;
                retryButton.disabled = false;
            });
        });
    }
});
</script>
@endpush
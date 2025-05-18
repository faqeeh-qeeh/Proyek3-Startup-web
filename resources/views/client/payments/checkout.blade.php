{{-- @extends('client.layouts.app')

@section('title', 'Checkout Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Checkout Pembayaran</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i> Silakan selesaikan pembayaran Anda dalam waktu <strong>24 jam</strong>.
                    </div>

                    <div class="text-center mb-4">
                        <h5>Pesanan #{{ $order->order_number }}</h5>
                        <h3 class="text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h3>
                    </div>

                    <div id="midtrans-checkout" class="text-center">
                        <button id="pay-button" class="btn btn-primary btn-lg">
                            <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
                        </button>
                        <p class="text-muted mt-3">
                            Anda akan diarahkan ke halaman pembayaran Midtrans
                        </p>
                    </div>

                    <hr>

                    <div class="text-center">
                        <a href="{{ route('client.orders.show', $order) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var payButton = document.getElementById('pay-button');
        
        payButton.addEventListener('click', function() {
            // Disable button untuk mencegah multiple click
            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    // Refresh status sebelum redirect
                    fetch(`/client/payments/${'{{ $order->id }}'}/check-status`)
                        .then(() => {
                            window.location.href = '{{ route('client.payments.success', $order) }}';
                        });
                },
                onPending: function(result){
                    fetch(`/client/payments/${'{{ $order->id }}'}/check-status`)
                        .then(() => {
                            window.location.href = '{{ route('client.payments.success', $order) }}';
                        });
                },
                onError: function(result){
                    window.location.href = '{{ route('client.payments.failure', $order) }}';
                },
                onClose: function(){
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-credit-card mr-2"></i> Bayar Sekarang';
                }
            });
        });
    });
</script>
@endpush --}}

@extends('client.layouts.app')

@section('title', 'Checkout Pembayaran')

@push('styles')
<style>
    .payment-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background-color: var(--card-bg);
    }
    
    .payment-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem;
        border-bottom: none;
    }
    
    .payment-alert {
        border-left: 4px solid var(--info-color);
        background-color: rgba(54, 185, 204, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        padding: 1rem;
    }
    
    .payment-alert i {
        font-size: 1.5rem;
        margin-right: 1rem;
        color: var(--info-color);
    }
    
    .amount-display {
        background-color: rgba(78, 115, 223, 0.1);
        border-radius: 8px;
        padding: 1.5rem;
        margin: 1.5rem 0;
        text-align: center;
    }
    
    .order-number {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .total-amount {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
    }
    
    .pay-button {
        padding: 12px 24px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .pay-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
    }
    
    .payment-note {
        color: var(--text-muted);
        font-size: 0.875rem;
        text-align: center;
        margin-top: 1rem;
    }
    
    .divider {
        border-top: 1px solid var(--border-color);
        margin: 2rem 0;
        opacity: 0.3;
    }
    
    .back-button {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    
    .back-button:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        background-color: transparent;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="payment-card">
                <div class="payment-header text-center">
                    <h3 class="mb-0"><i class="fas fa-credit-card me-2"></i> Checkout Pembayaran</h3>
                </div>
                
                <div class="card-body p-4 p-lg-5">
                    <div class="payment-alert">
                        <i class="fas fa-info-circle"></i>
                        <div>Silakan selesaikan pembayaran Anda dalam waktu <strong>24 jam</strong> sebelum pesanan kadaluarsa.</div>
                    </div>
                    
                    <div class="amount-display">
                        <p class="order-number">Pesanan #{{ $order->order_number }}</p>
                        <h2 class="total-amount">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h2>
                    </div>
                    
                    <div class="text-center">
                        <button id="pay-button" class="btn btn-primary pay-button">
                            <i class="fas fa-credit-card me-2"></i> Bayar Sekarang
                        </button>
                        <p class="payment-note">
                            Anda akan diarahkan ke halaman pembayaran Midtrans yang aman
                        </p>
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div class="text-center">
                        <a href="{{ route('client.orders.show', $order) }}" class="btn back-button">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Detail Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    // EXISTING PAYMENT FUNCTIONALITY REMAINS UNCHANGED
    document.addEventListener('DOMContentLoaded', function() {
        var payButton = document.getElementById('pay-button');
        
        payButton.addEventListener('click', function() {
            // Disable button untuk mencegah multiple click
            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    // Refresh status sebelum redirect
                    fetch(`/client/payments/${'{{ $order->id }}'}/check-status`)
                        .then(() => {
                            window.location.href = '{{ route('client.payments.success', $order) }}';
                        });
                },
                onPending: function(result){
                    fetch(`/client/payments/${'{{ $order->id }}'}/check-status`)
                        .then(() => {
                            window.location.href = '{{ route('client.payments.failure', $order) }}';
                        });
                },
                onError: function(result){
                    window.location.href = '{{ route('client.payments.failure', $order) }}';
                },
                onClose: function(){
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-credit-card mr-2"></i> Bayar Sekarang';
                }
            });
        });
    });
</script>
@endpush
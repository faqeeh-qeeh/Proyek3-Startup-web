@extends('client.layouts.app')

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
@endpush
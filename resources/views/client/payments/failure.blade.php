@extends('client.layouts.app')

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
@endsection
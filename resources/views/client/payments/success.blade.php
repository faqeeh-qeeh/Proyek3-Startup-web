@extends('client.layouts.app')

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
@endsection
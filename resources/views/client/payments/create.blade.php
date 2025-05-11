@extends('client.layouts.app')

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
@endsection
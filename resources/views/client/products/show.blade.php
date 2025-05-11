@extends('client.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Detail Produk: {{ $product->name }}</div>

                <div class="card-body">
                    @if($product->image))
                        <div class="text-center mb-4">
                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" alt="{{ $product->name }}" style="max-height: 300px;">
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5>Deskripsi Produk</h5>
                        <p>{{ $product->description }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informasi Harga</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Harga:</strong> Rp {{ number_format($product->price, 0, ',', '.') }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Status:</strong> 
                                    <span class="badge badge-{{ $product->is_active ? 'success' : 'danger' }}">
                                        {{ $product->is_active ? 'Tersedia' : 'Tidak Tersedia' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Spesifikasi Teknis</h5>
                            <ul class="list-group">
                                <li class="list-group-item">Mikrokontroler: ESP32</li>
                                <li class="list-group-item">Sensor: PZEM-004T</li>
                                <li class="list-group-item">Relay: 4 Channel</li>
                                <li class="list-group-item">Konektivitas: WiFi + MQTT</li>
                            </ul>
                        </div>
                    </div>

                    @if($recommendation))
                        <div class="alert alert-info">
                            <h5>{{ $recommendation['message'] }}</h5>
                            <p>{{ $recommendation['suggestion'] }}</p>
                        </div>
                    @endif

                    <div class="text-center mt-4">
                        <a href="{{ route('client.orders.create') }}?product_id={{ $product->id }}" class="btn btn-primary btn-lg">Beli Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
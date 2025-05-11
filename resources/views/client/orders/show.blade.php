@extends('client.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Order Details: #{{ $order->order_number }}</span>
                    <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : ($order->status === 'pending' ? 'warning' : 'secondary')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Order Date:</strong> {{ $order->created_at->format('d M Y H:i') }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Payment Status:</strong> 
                                    <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Payment Method:</strong> {{ $order->payment_method ?? 'Not specified' }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Total Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Order Items</h5>
                            <ul class="list-group">
                                @foreach($order->items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $item->product->name }}
                                    <span class="badge badge-primary badge-pill">
                                        x{{ $item->quantity }}
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    @if($order->devices->isNotEmpty())
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>Assigned Devices</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Device Name</th>
                                                <th>MQTT Topic</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->devices as $device)
                                            <tr>
                                                <td>{{ $device->device_name }}</td>
                                                <td><code>{{ $device->mqtt_topic }}</code></td>
                                                <td>
                                                    <span class="badge badge-{{ $device->status === 'active' ? 'success' : ($device->status === 'inactive' ? 'secondary' : 'warning') }}">
                                                        {{ ucfirst($device->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('client.devices.show', $device) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('client.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Orders
                        </a>
                        
                        @if($order->payment_status === 'unpaid' && $order->status !== 'cancelled')
                            <a href="{{ route('client.payments.create', $order) }}" class="btn btn-success">
                                <i class="fas fa-credit-card mr-1"></i> Proceed to Payment
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
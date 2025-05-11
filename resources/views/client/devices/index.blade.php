@extends('client.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Devices</h5>
                    <a href="{{ route('client.orders.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Buy New Device
                    </a>
                </div>

                <div class="card-body">
                    @if($devices->isEmpty())
                        <div class="alert alert-info text-center">
                            You don't have any devices yet. <a href="{{ route('client.orders.create') }}">Buy your first device</a> to get started.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Device Name</th>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>MQTT Topic</th>
                                        <th>Order Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($devices as $device)
                                    <tr>
                                        <td>{{ $device->device_name }}</td>
                                        <td>{{ $device->product->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $device->status === 'active' ? 'success' : ($device->status === 'inactive' ? 'secondary' : 'warning') }}">
                                                {{ ucfirst($device->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $device->mqtt_topic }}</code>
                                        </td>
                                        <td>{{ $device->created_at->format('d M Y') }}</td>
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

                        @if($devices->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $devices->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
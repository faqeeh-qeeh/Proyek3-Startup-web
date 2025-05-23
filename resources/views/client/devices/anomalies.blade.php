@extends('client.layouts.app')

@section('title', 'Deteksi Anomali - ' . $device->device_name)

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header device-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-4 text-warning"></i>
                <div>
                    <h4 class="mb-0">Deteksi Anomali</h4>
                    <small class="opacity-75">{{ $device->device_name }}</small>
                </div>
            </div>
            <div>
                <a href="{{ route('client.devices.show', $device) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <form action="{{ route('client.devices.detect-anomalies', $device) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning">
                        <i class="fas fa-sync-alt me-1"></i> Jalankan Deteksi
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Device Classification Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tags me-2"></i>
                    <span>Klasifikasi Perangkat</span>
                </div>
                <div class="card-body">
                    @if($device->classification)
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if($device->classification->category == 'industrial')
                                    <i class="fas fa-industry fa-3x text-primary"></i>
                                @else
                                    <i class="fas fa-home fa-3x text-success"></i>
                                @endif
                            </div>
                            <div>
                                <h5 class="mb-1">
                                    @if($device->classification->category == 'industrial')
                                        <span class="badge bg-primary">Perangkat Industri</span>
                                    @else
                                        <span class="badge bg-success">Perangkat Rumah Tangga</span>
                                    @endif
                                </h5>
                                <small class="text-muted">
                                    Keyakinan: {{ number_format($device->classification->confidence * 100, 1) }}% |
                                    Terakhir diperbarui: {{ $device->classification->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Perangkat ini belum diklasifikasikan. Jalankan deteksi anomali untuk mengklasifikasikan.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Anomalies List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span>Daftar Anomali Terdeteksi</span>
                    </div>
                    <small class="text-muted">
                        Total: {{ $anomalies->total() }} anomali
                    </small>
                </div>
                
                <div class="card-body">
                    @if($anomalies->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h5>Tidak ada anomali yang terdeteksi</h5>
                            <p class="text-muted">Sistem belum menemukan anomali pada perangkat ini.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Tipe</th>
                                        <th>Skor</th>
                                        <th>Detail</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($anomalies as $anomaly)
                                        <tr>
                                            <td>
                                                {{ $anomaly->monitoring->recorded_at->format('d M Y H:i') }}
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass = [
                                                        'voltage_anomaly' => 'bg-danger',
                                                        'current_anomaly' => 'bg-warning',
                                                        'power_factor_anomaly' => 'bg-info',
                                                        'general_anomaly' => 'bg-secondary'
                                                    ][$anomaly->type] ?? 'bg-primary';
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ str_replace('_', ' ', $anomaly->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $anomaly->score > 0.9 ? 'danger' : 'warning' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $anomaly->score * 100 }}%" 
                                                         aria-valuenow="{{ $anomaly->score * 100 }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ number_format($anomaly->score, 2) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#detail-{{ $anomaly->id }}">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </button>
                                            </td>
                                            <td>
                                                <form action="{{ route('client.devices.confirm-anomaly', $anomaly) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-{{ $anomaly->is_confirmed ? 'success' : 'outline-success' }}">
                                                        <i class="fas fa-check"></i> {{ $anomaly->is_confirmed ? 'Dikonfirmasi' : 'Konfirmasi' }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="detail-{{ $anomaly->id }}">
                                            <td colspan="5">
                                                <div class="p-3 bg-light rounded">
                                                    <h6>Detail Anomali:</h6>
                                                    <p>{{ $anomaly->description }}</p>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <strong>Voltage:</strong> {{ $anomaly->monitoring->voltage }} V
                                                        </div>
                                                        <div class="col-md-2">
                                                            <strong>Current:</strong> {{ $anomaly->monitoring->current }} A
                                                        </div>
                                                        <div class="col-md-2">
                                                            <strong>Power:</strong> {{ $anomaly->monitoring->power }} W
                                                        </div>
                                                        <div class="col-md-2">
                                                            <strong>Energy:</strong> {{ $anomaly->monitoring->energy }} kWh
                                                        </div>
                                                        <div class="col-md-2">
                                                            <strong>PF:</strong> {{ $anomaly->monitoring->power_factor }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $anomalies->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .anomaly-card {
        border-left: 4px solid #dc3545;
    }
    .confirmed-anomaly {
        border-left: 4px solid #28a745;
    }
    .progress {
        background-color: #f8f9fa;
    }
</style>
@endpush
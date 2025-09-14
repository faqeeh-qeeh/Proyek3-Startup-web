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
                    <small class="text-muted">{{ $device->device_name }}</small>
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

            <!-- Data Quality Summary -->
            @if (session('quality_data'))
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-chart-line me-2"></i> Ringkasan Kualitas Data (30 Hari Terakhir)
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ session('quality_data.stats.good') }}</h5>
                                    <p class="card-text">Data Normal</p>
                                    <small>(CV ≤ 0.3)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-dark mb-3">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ session('quality_data.stats.fair') }}</h5>
                                    <p class="card-text">Data Sedang</p>
                                    <small>(0.3 < CV ≤ 0.5)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white mb-3">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ session('quality_data.stats.poor') }}</h5>
                                    <p class="card-text">Data Buruk</p>
                                    <small>(CV > 0.5)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Sistem menggunakan analisis 6 parameter: Voltage, Current, Power, Energy, Frequency, dan Power Factor.
                    </div>
                </div>
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
                                <div class="mt-2">
                                    <small><strong>Fitur Klasifikasi:</strong></small>
                                    <div class="row mt-1">
                                        <div class="col-md-4">
                                            <small>Daya Rata-rata: {{ number_format($device->classification->features[0] ?? 0, 2) }} W</small>
                                        </div>
                                        <div class="col-md-4">
                                            <small>Daya Maks: {{ number_format($device->classification->features[1] ?? 0, 2) }} W</small>
                                        </div>
                                        <div class="col-md-4">
                                            <small>Jam Penggunaan: {{ number_format($device->classification->features[2] ?? 0, 1) }} jam</small>
                                        </div>
                                    </div>
                                </div>
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
                    <div>
                        <small class="text-muted me-3">
                            Threshold: 0.72 (Li et al. 2022)
                        </small>
                        <small class="text-muted">
                            Total: {{ $anomalies->total() }} anomali
                        </small>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($anomalies->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h5>Tidak ada anomali yang terdeteksi</h5>
                            <p class="text-muted">Sistem belum menemukan anomali pada perangkat ini berdasarkan analisis 6 parameter.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Tipe</th>
                                        <th>Skor</th>
                                        <th>Keparahan</th>
                                        <th>Detail</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($anomalies as $anomaly)
                                        <tr class="{{ $anomaly->is_confirmed ? 'confirmed-anomaly' : 'anomaly-card' }}">
                                            <td>
                                                <small>{{ ($anomaly->detected_at ?? $anomaly->created_at) }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $anomaly->severity == 'high' ? 'danger' : 'warning' }}">
                                                    {{ $anomaly->anomaly_type ?? 'general_anomaly' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $anomaly->anomaly_score > 0.9 ? 'danger' : ($anomaly->anomaly_score > 0.8 ? 'warning' : 'info') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ ($anomaly->anomaly_score ?? 0) * 100 }}%" 
                                                         aria-valuenow="{{ $anomaly->anomaly_score ?? 0 }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ number_format($anomaly->anomaly_score ?? 0, 2) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $severityClass = [
                                                        'critical' => 'danger',
                                                        'high' => 'warning',
                                                        'medium' => 'info',
                                                        'low' => 'secondary'
                                                    ][$anomaly->severity] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $severityClass }}">
                                                    {{ strtoupper($anomaly->severity) }}
                                                </span>
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
                                            <td colspan="6">
                                                <div class="p-3 bg-light rounded">
                                                    <h6>Detail Anomali:</h6>
                                                    <p>{{ $anomaly->description }}</p>
                                                    
                                                    <div class="row mt-3">
                                                        <div class="col-md-2">
                                                            <div class="card">
                                                                <div class="card-body text-center">
                                                                    <h6 class="card-title">Voltage</h6>
                                                                    <p class="card-text {{ $anomaly->monitoring->voltage < 200 || $anomaly->monitoring->voltage > 250 ? 'text-danger' : 'text-success' }}">
                                                                        {{ $anomaly->monitoring->voltage }} V
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card">
                                                                <div class="card-body text-center">
                                                                    <h6 class="card-title">Current</h6>
                                                                    <p class="card-text">{{ $anomaly->monitoring->current }} A</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card">
                                                                <div class="card-body text-center">
                                                                    <h6 class="card-title">Power</h6>
                                                                    <p class="card-text">{{ $anomaly->monitoring->power }} W</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card">
                                                                <div class="card-body text-center">
                                                                    <h6 class="card-title">Energy</h6>
                                                                    <p class="card-text">{{ $anomaly->monitoring->energy }} kWh</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card">
                                                                <div class="card-body text-center">
                                                                    <h6 class="card-title">Frequency</h6>
                                                                    <p class="card-text {{ abs($anomaly->monitoring->frequency - 50) > 1 ? 'text-danger' : 'text-success' }}">
                                                                        {{ $anomaly->monitoring->frequency }} Hz
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card">
                                                                <div class="card-body text-center">
                                                                    <h6 class="card-title">Power Factor</h6>
                                                                    <p class="card-text {{ $anomaly->monitoring->power_factor < 0.7 ? 'text-danger' : 'text-success' }}">
                                                                        {{ $anomaly->monitoring->power_factor }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $anomalies->onEachSide(1)->links('pagination::bootstrap-5') }}
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
        border-left: 4px solid var(--danger-color);
    }
    .confirmed-anomaly {
        border-left: 4px solid var(--success-color);
    }
    .progress {
        background-color: var(--light-color);
    }
    .device-header {
        background-color: var(--light-color);
        border-bottom: 1px solid var(--border-color);
    }
    .card-header i {
        width: 24px;
        text-align: center;
    }
    
    /* Dark mode adjustments */
    [data-bs-theme="dark"] .bg-light {
        background-color: var(--dark-color) !important;
    }
    [data-bs-theme="dark"] .text-muted {
        color: var(--text-muted) !important;
    }
    [data-bs-theme="dark"] .table {
        --bs-table-bg: var(--card-bg);
        --bs-table-striped-bg: rgba(78, 115, 223, 0.05);
        --bs-table-hover-bg: rgba(78, 115, 223, 0.1);
    }
    
    /* Pagination styling */
    .pagination {
        --bs-pagination-color: var(--text-color);
        --bs-pagination-bg: var(--card-bg);
        --bs-pagination-border-color: var(--border-color);
        --bs-pagination-hover-color: var(--primary-color);
        --bs-pagination-hover-bg: var(--light-color);
        --bs-pagination-hover-border-color: var(--border-color);
        --bs-pagination-active-bg: var(--primary-color);
        --bs-pagination-active-border-color: var(--primary-color);
        --bs-pagination-disabled-color: var(--text-muted);
        --bs-pagination-disabled-bg: var(--card-bg);
        --bs-pagination-disabled-border-color: var(--border-color);
    }
    [data-bs-theme="dark"] .table {
        --bs-table-bg: var(--card-bg);
        --bs-table-color: var(--text-color);
        --bs-table-border-color: var(--border-color);
    }

    [data-bs-theme="dark"] .table-responsive {
        background-color: var(--card-bg);
        border-radius: 0.5rem;
        overflow: hidden;
    }

    [data-bs-theme="dark"] .card-body {
        background-color: var(--card-bg);
    }

    [data-bs-theme="dark"] .pagination {
        --bs-pagination-bg: var(--card-bg);
        --bs-pagination-color: var(--text-color);
        --bs-pagination-border-color: var(--border-color);
    }
</style>
@endpush
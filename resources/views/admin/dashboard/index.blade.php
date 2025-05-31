@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Dashboard Admin</h1>
        <div class="d-flex">
            <button class="btn btn-sm btn-outline-secondary me-2" id="refresh-btn">
                <i class="fas fa-sync-alt me-1"></i> Segarkan
            </button>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="timeRangeDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-calendar-alt me-1"></i> 7 Hari Terakhir
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item time-range" href="#" data-range="7">7 Hari Terakhir</a></li>
                    <li><a class="dropdown-item time-range" href="#" data-range="30">30 Hari Terakhir</a></li>
                    <li><a class="dropdown-item time-range" href="#" data-range="90">90 Hari Terakhir</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item time-range" href="#" data-range="0">Semua Data</a></li>
                </ul>
            </div>
        </div>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Ringkasan</li>
    </ol>
    
    <!-- Statistik Utama -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Produk</h6>
                            <h2 class="mb-0">{{ $totalProducts }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-boxes text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('admin.products.index') }}" class="text-decoration-none d-flex align-items-center justify-content-between">
                        <span>Lihat semua produk</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Pesanan</h6>
                            <h2 class="mb-0">{{ $totalOrders }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-shopping-cart text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('admin.orders.index') }}" class="text-decoration-none d-flex align-items-center justify-content-between">
                        <span>Lihat semua pesanan</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Pesanan Tertunda</h6>
                            <h2 class="mb-0">{{ $pendingOrders }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('admin.orders.index') }}?status=pending" class="text-decoration-none d-flex align-items-center justify-content-between">
                        <span>Lihat pesanan tertunda</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Perangkat Aktif</h6>
                            <h2 class="mb-0">{{ $activeDevices }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-microchip text-info fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('admin.devices.index') }}" class="text-decoration-none d-flex align-items-center justify-content-between">
                        <span>Lihat semua perangkat</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafik dan Data -->
    <div class="row g-4 mb-4">
        <!-- Grafik Pesanan -->
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-chart-line me-1"></i>
                        Tren Pesanan
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chartDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item chart-filter" href="#" data-filter="daily">Harian</a></li>
                            <li><a class="dropdown-item chart-filter" href="#" data-filter="weekly">Mingguan</a></li>
                            <li><a class="dropdown-item chart-filter" href="#" data-filter="monthly">Bulanan</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Distribusi Status -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Distribusi Status Pesanan
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="chart-container" style="position: relative; height: 250px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-auto pt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <span class="d-block text-success"><i class="fas fa-circle"></i> Selesai</span>
                                <span class="fw-bold">{{ $orderStatusDistribution['completed'] }}%</span>
                            </div>
                            <div class="col-4">
                                <span class="d-block text-warning"><i class="fas fa-circle"></i> Tertunda</span>
                                <span class="fw-bold">{{ $orderStatusDistribution['pending'] }}%</span>
                            </div>
                            <div class="col-4">
                                <span class="d-block text-danger"><i class="fas fa-circle"></i> Dibatalkan</span>
                                <span class="fw-bold">{{ $orderStatusDistribution['cancelled'] }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Terbaru -->
    <div class="row g-4">
        <!-- Pesanan Terbaru -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-shopping-cart me-1"></i>
                        Pesanan Terbaru
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Klien</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order->id) }}" class="text-decoration-none">{{ $order->order_number }}</a></td>
                                    <td>{{ Str::limit($order->client->full_name, 15) }}</td>
                                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Tidak ada pesanan terbaru</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Perangkat Aktif -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-microchip me-1"></i>
                        Perangkat Aktif
                    </div>
                    <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <thead class="table-light">
                                <tr>
                                    <th>Perangkat</th>
                                    <th>Klien</th>
                                    <th>Status</th>
                                    <th>Terakhir Aktif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDevices as $device)
                                <tr>
                                    <td>{{ Str::limit($device->device_name, 15) }}</td>
                                    <td>{{ Str::limit($device->client->full_name, 15) }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $device->status == 'active' ? 'success' : ($device->status == 'maintenance' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($device->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $device->last_active_at ? $device->last_active_at->diffForHumans() : 'Tidak pernah' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Tidak ada perangkat aktif</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Grafik Tren Pesanan
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        const ordersChart = new Chart(ordersCtx, {
            type: 'line',
            data: {
                labels: @json($orderTrends['labels']),
                datasets: [{
                    label: 'Pesanan Selesai',
                    data: @json($orderTrends['completed']),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Pesanan Tertunda',
                    data: @json($orderTrends['pending']),
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Grafik Distribusi Status
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Tertunda', 'Dibatalkan'],
                datasets: [{
                    data: [
                        {{ $orderStatusDistribution['completed'] }},
                        {{ $orderStatusDistribution['pending'] }},
                        {{ $orderStatusDistribution['cancelled'] }}
                    ],
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Fungsi untuk refresh data
        document.getElementById('refresh-btn').addEventListener('click', function() {
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');
            
            // Simulasikan loading data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });

        // Filter rentang waktu
        document.querySelectorAll('.time-range').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const range = this.getAttribute('data-range');
                document.getElementById('timeRangeDropdown').innerHTML = 
                    `<i class="fas fa-calendar-alt me-1"></i> ${this.textContent}`;
                
                // Di sini Anda bisa menambahkan AJAX untuk memuat data berdasarkan range
                console.log(`Memfilter data untuk ${range} hari terakhir`);
            });
        });

        // Filter grafik
        document.querySelectorAll('.chart-filter').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.getAttribute('data-filter');
                document.getElementById('chartDropdown').innerHTML = 
                    `<i class="fas fa-filter me-1"></i> ${this.textContent}`;
                
                // Di sini Anda bisa menambahkan logika untuk mengubah tampilan grafik
                console.log(`Mengubah tampilan grafik ke ${filter}`);
            });
        });
    });
</script>
@endpush
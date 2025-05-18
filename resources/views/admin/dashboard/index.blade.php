@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Dashboard Overview</h1>
        <div class="d-flex">
            <button class="btn btn-sm btn-outline-secondary me-2" id="refresh-btn">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="timeRangeDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-calendar-alt me-1"></i> Last 7 Days
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item time-range" href="#" data-range="7">Last 7 Days</a></li>
                    <li><a class="dropdown-item time-range" href="#" data-range="30">Last 30 Days</a></li>
                    <li><a class="dropdown-item time-range" href="#" data-range="90">Last 90 Days</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item time-range" href="#" data-range="0">All Time</a></li>
                </ul>
            </div>
        </div>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Overview</li>
    </ol>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Products</h6>
                            <h2 class="mb-0">{{ $totalProducts }}</h2>
                            <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 5.2% from last month</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-boxes text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('admin.products.index') }}" class="text-decoration-none d-flex align-items-center justify-content-between">
                        <span>View all products</span>
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
                            <h6 class="text-muted mb-2">Total Orders</h6>
                            <h2 class="mb-0">{{ $totalOrders }}</h2>
                            <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 12.7% from last month</small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-shopping-cart text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('admin.orders.index') }}" class="text-decoration-none d-flex align-items-center justify-content-between">
                        <span>View all orders</span>
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
                            <h6 class="text-muted mb-2">Pending Orders</h6>
                            <h2 class="mb-0">{{ $pendingOrders }}</h2>
                            <small class="text-danger"><i class="fas fa-arrow-down me-1"></i> 3.5% from last month</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('admin.orders.index') }}?status=pending" class="text-decoration-none d-flex align-items-center justify-content-between">
                        <span>View pending orders</span>
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
                            <h6 class="text-muted mb-2">Active Devices</h6>
                            <h2 class="mb-0">{{ $activeDevices }}</h2>
                            <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 8.1% from last month</small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-microchip text-info fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('admin.devices.index') }}" class="text-decoration-none d-flex align-items-center justify-content-between">
                        <span>View all devices</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts and Data Section -->
    <div class="row g-4 mb-4">
        <!-- Orders Chart -->
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-chart-line me-1"></i>
                        Order Trends
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chartDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item chart-filter" href="#" data-filter="daily">Daily</a></li>
                            <li><a class="dropdown-item chart-filter" href="#" data-filter="weekly">Weekly</a></li>
                            <li><a class="dropdown-item chart-filter" href="#" data-filter="monthly">Monthly</a></li>
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
        
        <!-- Status Distribution -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Order Status Distribution
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="chart-container" style="position: relative; height: 250px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-auto pt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <span class="d-block text-success"><i class="fas fa-circle"></i> Completed</span>
                                <span class="fw-bold">65%</span>
                            </div>
                            <div class="col-4">
                                <span class="d-block text-warning"><i class="fas fa-circle"></i> Pending</span>
                                <span class="fw-bold">25%</span>
                            </div>
                            <div class="col-4">
                                <span class="d-block text-danger"><i class="fas fa-circle"></i> Cancelled</span>
                                <span class="fw-bold">10%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Data Section -->
    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-shopping-cart me-1"></i>
                        Recent Orders
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Client</th>
                                    <th>Amount</th>
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
                                    <td colspan="4" class="text-center py-4">No recent orders found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Active Devices -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-microchip me-1"></i>
                        Active Devices
                    </div>
                    <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <thead class="table-light">
                                <tr>
                                    <th>Device</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Last Active</th>
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
                                    <td>{{ $device->last_active_at ? $device->last_active_at->diffForHumans() : 'Never' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No active devices found</td>
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

@push('styles')
<style>
    /* Animasi yang sudah ada */
    .card-hover:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .border-start {
        border-left-width: 4px !important;
    }
    
    .table-borderless td, .table-borderless th {
        border: none;
    }

    /* Animasi baru */
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
        opacity: 0;
    }

    .animate-slide-up {
        animation: slideUp 0.6s ease-out forwards;
        opacity: 0;
    }

    .animate-delay-1 {
        animation-delay: 0.1s;
    }

    .animate-delay-2 {
        animation-delay: 0.2s;
    }

    .animate-delay-3 {
        animation-delay: 0.3s;
    }

    .animate-delay-4 {
        animation-delay: 0.4s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Animasi untuk elemen yang keluar */
    .page-exit-active {
        transition: all 0.3s ease-out;
        opacity: 0;
        transform: translateY(20px);
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tambahkan class animasi ke elemen-elemen
        function addAnimationClasses() {
            // Header
            document.querySelector('h1').classList.add('animate-slide-up');
            document.querySelector('.d-flex.justify-content-between.align-items-center.mb-4')
                .classList.add('animate-slide-up', 'animate-delay-1');
            
            // Breadcrumb
            document.querySelector('.breadcrumb').classList.add('animate-fade-in', 'animate-delay-1');
            
            // Stats Cards
            const statsCards = document.querySelectorAll('.row.g-4.mb-4 .col-xl-3');
            statsCards.forEach((card, index) => {
                card.classList.add('animate-slide-up', `animate-delay-${index + 1}`);
            });
            
            // Charts
            document.querySelector('.col-xl-8 .card').classList.add('animate-fade-in', 'animate-delay-2');
            document.querySelector('.col-xl-4 .card').classList.add('animate-fade-in', 'animate-delay-3');
            
            // Tables
            document.querySelectorAll('.col-xl-6 .card').forEach((card, index) => {
                card.classList.add('animate-fade-in', `animate-delay-${index + 3}`);
            });
        }
        
        // Panggil fungsi untuk menambahkan animasi
        addAnimationClasses();
        
        // Fungsi untuk animasi keluar saat pindah halaman
        function setupPageTransitions() {
            const links = document.querySelectorAll('a:not([href^="#"]):not([target="_blank"]):not([data-bs-toggle])');
            
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Skip untuk dropdown dan filter
                    if (this.classList.contains('dropdown-item') || 
                        this.classList.contains('time-range') || 
                        this.classList.contains('chart-filter')) {
                        return;
                    }
                    
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    
                    // Tambahkan animasi keluar ke container utama
                    const container = document.querySelector('.container-fluid.px-4');
                    container.classList.add('page-exit-active');
                    
                    // Redirect setelah animasi selesai
                    setTimeout(() => {
                        window.location.href = href;
                    }, 300);
                });
            });
        }
        
        // Panggil fungsi untuk setup animasi keluar
        setupPageTransitions();
        
        // Kode chart dan lainnya yang sudah ada...
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        
        // Order Trends Chart
        const ordersChart = new Chart(ordersCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Completed Orders',
                    data: [12, 19, 15, 22, 18, 25, 30],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Pending Orders',
                    data: [5, 8, 7, 10, 12, 8, 15],
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
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Status Distribution Chart
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Cancelled'],
                datasets: [{
                    data: [65, 25, 10],
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
        
        // Time range filter
        document.querySelectorAll('.time-range').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const range = this.getAttribute('data-range');
                document.getElementById('timeRangeDropdown').innerHTML = 
                    `<i class="fas fa-calendar-alt me-1"></i> ${this.textContent}`;
                
                // Animasi perubahan
                const dropdown = document.getElementById('timeRangeDropdown');
                dropdown.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    dropdown.style.transform = 'scale(1)';
                    dropdown.style.transition = 'transform 0.3s ease';
                }, 100);
                
                // Here you would typically make an AJAX call to update data
                console.log(`Time range changed to: ${range} days`);
            });
        });
        
        // Chart filter
        document.querySelectorAll('.chart-filter').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.getAttribute('data-filter');
                document.getElementById('chartDropdown').innerHTML = 
                    `<i class="fas fa-filter me-1"></i> ${this.textContent}`;
                
                // Animasi perubahan
                const dropdown = document.getElementById('chartDropdown');
                dropdown.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    dropdown.style.transform = 'scale(1)';
                    dropdown.style.transition = 'transform 0.3s ease';
                }, 100);
                
                // Here you would typically update the chart data
                console.log(`Chart filter changed to: ${filter}`);
            });
        });
        
        // Refresh button dengan animasi lebih smooth
        document.getElementById('refresh-btn').addEventListener('click', function() {
            // Add rotation animation
            const icon = this.querySelector('i');
            icon.style.transform = 'rotate(360deg)';
            icon.style.transition = 'transform 0.5s ease';
            
            // Animasi pada card stats
            const statsCards = document.querySelectorAll('.row.g-4.mb-4 .col-xl-3 .card');
            statsCards.forEach(card => {
                card.style.transform = 'translateY(-5px)';
                setTimeout(() => {
                    card.style.transform = 'translateY(0)';
                    card.style.transition = 'transform 0.3s ease';
                }, 300);
            });
            
            // Here you would typically refresh the data
            console.log('Refreshing dashboard data...');
            
            // Reset rotation after animation completes
            setTimeout(() => {
                icon.style.transform = 'rotate(0deg)';
            }, 500);
        });
    });
</script>
@endpush
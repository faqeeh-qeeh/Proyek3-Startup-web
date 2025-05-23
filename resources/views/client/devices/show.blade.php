{{-- @extends('client.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-microchip me-2"></i>{{ $device->device_name }}
                        </h4>
                        <span class="badge bg-{{ $device->status === 'active' ? 'success' : 'warning' }}">
                            {{ ucfirst($device->status) }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informasi Perangkat -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Perangkat</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th width="40%">Produk</th>
                                                    <td>{{ $device->product->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Topic MQTT</th>
                                                    <td><code>{{ $device->mqtt_topic }}</code></td>
                                                </tr>
                                                <tr>
                                                    <th>Aktif Sejak</th>
                                                    <td>{{ $device->created_at->translatedFormat('d F Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Data Terakhir</th>
                                                    <td id="last-updated-text">
                                                        @if($latestData)
                                                            {{ $latestData->recorded_at ? $latestData->recorded_at->format('d F Y H:i') : 'N/A' }}
                                                        @else
                                                            Belum ada data
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kontrol Perangkat -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Kontrol Relay</h5>
                                </div>
                                <div class="card-body">
                                    <form id="control-form" method="POST" action="{{ route('client.devices.control', $device) }}">
                                        @csrf
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="channel" class="form-label">Channel</label>
                                                <select class="form-select" id="channel" name="channel" required>
                                                    @for($i = 1; $i <= 4; $i++)
                                                        <option value="{{ $i }}">Channel {{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="form-label d-block">Aksi</label>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="command" id="command-on" value="on" autocomplete="off" checked>
                                                    <label class="btn btn-outline-success" for="command-on"><i class="fas fa-power-off me-1"></i> ON</label>
                                                    
                                                    <input type="radio" class="btn-check" name="command" id="command-off" value="off" autocomplete="off">
                                                    <label class="btn btn-outline-danger" for="command-off"><i class="fas fa-power-off me-1"></i> OFF</label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary w-100" id="control-button">
                                                    <i class="fas fa-paper-plane me-1"></i> Kirim Perintah
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <!-- Status Relay -->
                                    <div class="mt-4">
                                        <h6><i class="fas fa-lightbulb me-2"></i>Status Relay</h6>
                                        <div class="row" id="relay-status">
                                            @for($i = 1; $i <= 4; $i++)
                                                <div class="col-6 col-md-3 mb-2">
                                                    <div class="p-2 border rounded text-center bg-light">
                                                        <small class="d-block text-muted">Channel {{ $i }}</small>
                                                        <span class="badge bg-secondary relay-badge" id="relay-{{ $i }}">OFF</span>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Monitoring Data -->
                    <!-- Monitoring Data -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monitoring Real-time</h5>
                    <small class="text-muted" id="last-updated">
                        @if($latestData)
                            {{ $latestData->recorded_at ? $latestData->recorded_at->diffForHumans() : 'N/A' }}
                        @else
                            Belum ada data
                        @endif
                    </small>
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="monitoring-data">
                    <!-- Voltage -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-primary"><i class="fas fa-bolt me-2"></i>Tegangan</h6>
                                <h2 class="voltage-value text-primary">
                                    {{ $latestData ? number_format($latestData->voltage, 2) : '0.00' }} <small>V</small>
                                </h2>
                                <small class="text-muted">Tegangan Listrik</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-success"><i class="fas fa-tachometer-alt me-2"></i>Arus</h6>
                                <h2 class="current-value text-success">
                                    {{ $latestData ? number_format($latestData->current, 2) : '0.00' }} <small>A</small>
                                </h2>
                                <small class="text-muted">Arus Listrik</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Power -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-danger"><i class="fas fa-charging-station me-2"></i>Daya</h6>
                                <h2 class="power-value text-danger">
                                    {{ $latestData ? number_format($latestData->power, 2) : '0.00' }} <small>W</small>
                                </h2>
                                <small class="text-muted">Daya Aktif</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Energy -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-info"><i class="fas fa-battery-three-quarters me-2"></i>Energi</h6>
                                <h2 class="energy-value text-info">
                                    {{ $latestData ? number_format($latestData->energy, 2) : '0.00' }} <small>kWh</small>
                                </h2>
                                <small class="text-muted">Total Konsumsi</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Frequency -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-warning"><i class="fas fa-wave-square me-2"></i>Frekuensi</h6>
                                <h2 class="frequency-value text-warning">
                                    {{ $latestData ? number_format($latestData->frequency, 2) : '0.00' }} <small>Hz</small>
                                </h2>
                                <small class="text-muted">Frekuensi Listrik</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Power Factor -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-purple"><i class="fas fa-percentage me-2"></i>Faktor Daya</h6>
                                <h2 class="pf-value text-purple">
                                    {{ $latestData ? number_format($latestData->power_factor, 2) : '0.00' }}
                                </h2>
                                <small class="text-muted">Cos φ</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Chart Section -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">Voltage Monitor</div>
                            <div class="card-body">
                                <canvas id="voltageChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">Current Monitor</div>
                            <div class="card-body">
                                <canvas id="currentChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">Power Monitor</div>
                            <div class="card-body">
                                <canvas id="powerChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">Energy Monitor</div>
                            <div class="card-body">
                                <canvas id="energyChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">Frequency Monitor</div>
                            <div class="card-body">
                                <canvas id="frequencyChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">Power Factor Monitor</div>
                            <div class="card-body">
                                <canvas id="powerFactorChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deviceId = "{{ $device->id }}";
    
    // Fungsi untuk update nilai teks
    function updateTextValues(data) {
        // Format angka dengan 2 desimal
        const format = num => parseFloat(num || 0).toFixed(2);
        
        // Update nilai teks
        document.querySelector('.voltage-value').innerHTML = `${format(data.voltage)} <small>V</small>`;
        document.querySelector('.current-value').innerHTML = `${format(data.current)} <small>A</small>`;
        document.querySelector('.power-value').innerHTML = `${format(data.power)} <small>W</small>`;
        document.querySelector('.energy-value').innerHTML = `${format(data.energy)} <small>kWh</small>`;
        document.querySelector('.frequency-value').innerHTML = `${format(data.frequency)} <small>Hz</small>`;
        document.querySelector('.pf-value').textContent = format(data.power_factor);
        
        // Update waktu terakhir
        const lastUpdated = new Date(data.timestamp);
        document.getElementById('last-updated').textContent = 
            'Terakhir diperbarui: ' + lastUpdated.toLocaleTimeString('id-ID');
        document.getElementById('last-updated-text').textContent = 
            lastUpdated.toLocaleString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
    }

    // Inisialisasi Chart.js
    const voltageChart = initChart('voltageChart', 'Voltage (V)', 'rgba(54, 162, 235, 1)');
    const currentChart = initChart('currentChart', 'Current (A)', 'rgba(75, 192, 192, 1)');
    const powerChart = initChart('powerChart', 'Power (W)', 'rgba(255, 99, 132, 1)');
    const energyChart = initChart('energyChart', 'Energy (kWh)', 'rgba(255, 159, 64, 1)');
    const frequencyChart = initChart('frequencyChart', 'Frequency (Hz)', 'rgba(153, 102, 255, 1)');
    const powerFactorChart = initChart('powerFactorChart', 'Power Factor', 'rgba(201, 203, 207, 1)', true);

    function initChart(canvasId, label, color, isPowerFactor = false) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: label,
                    data: [],
                    borderColor: color,
                    backgroundColor: color.replace('1)', '0.1)'),
                    tension: 0.4,
                    borderWidth: 2,
                    fill: false
                }]
            },
            options: getChartOptions(label, isPowerFactor)
        });
    }

    function getChartOptions(title, isPowerFactor = false) {
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            },
            elements: {
                point: {
                    radius: 0
                }
            }
        };

        if (isPowerFactor) {
            options.scales.y = {
                ...options.scales.y,
                min: 0,
                max: 1,
                ticks: {
                    stepSize: 0.1
                }
            };
        } else {
            options.scales.y = {
                ...options.scales.y,
                beginAtZero: true
            };
        }

        return options;
    }

    // Fungsi untuk update data
    async function updateMonitoringData() {
        try {
            const response = await axios.get(`/client/devices/${deviceId}/monitoring-data`);
            const data = response.data;
            
            if (!data.success) {
                throw new Error('Invalid response format');
            }

            // Update chart data
            updateChart(voltageChart, data.chart.labels, data.chart.voltage);
            updateChart(currentChart, data.chart.labels, data.chart.current);
            updateChart(powerChart, data.chart.labels, data.chart.power);
            updateChart(energyChart, data.chart.labels, data.chart.energy);
            updateChart(frequencyChart, data.chart.labels, data.chart.frequency);
            updateChart(powerFactorChart, data.chart.labels, data.chart.power_factor);

            // Update text values
            updateTextValues(data.latest);
        } catch (error) {
            console.error('Error fetching monitoring data:', error);
            // Tambahkan notifikasi error jika diperlukan
        }
    }

    function updateChart(chart, labels, data) {
        chart.data.labels = labels;
        chart.data.datasets[0].data = data;
        chart.update();
    }

    // Update data pertama kali
    updateMonitoringData();

    // Set interval untuk polling
    const pollingInterval = setInterval(updateMonitoringData, 3000);

    // Bersihkan interval saat halaman ditutup
    window.addEventListener('beforeunload', () => {
        clearInterval(pollingInterval);
    });

    // Update data ketika tab aktif kembali
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            updateMonitoringData();
        }
    });
});
</script>
@endpush
    <script src="{{ asset('js/app.js') }}"></script>


@endpush --}}


@extends('client.layouts.app')

@section('title', $device->device_name)

@push('styles')
<style>
    .device-header {
        background-color: var(--primary-color) !important;
        color: white;
        border-bottom: none;
        padding: 1rem 1.5rem;
    }
    
    .info-card {
        border: none;
        border-radius: 0.5rem;
        background-color: var(--card-bg);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .info-card-header {
        background-color: rgba(var(--primary-rgb), 0.1);
        border-bottom: none;
        font-weight: 600;
    }
    
    .info-table {
        color: var(--text-color);
    }
    
    .info-table th {
        width: 40%;
        color: var(--text-muted);
    }
    
    .metric-card {
        border: none;
        border-radius: 0.5rem;
        background-color: var(--card-bg);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: 600;
    }
    
    .metric-unit {
        font-size: 1rem;
        opacity: 0.8;
    }
    
    .relay-control .btn-group {
        width: 100%;
    }
    
    .relay-control .btn {
        flex: 1;
    }
    
    .relay-status-badge {
        padding: 0.5rem;
        border-radius: 0.25rem;
        font-weight: 500;
        width: 100%;
        text-align: center;
    }
    
    .relay-on {
        background-color: rgba(var(--success-rgb), 0.1);
        color: var(--success-color);
    }
    
    .relay-off {
        background-color: rgba(var(--secondary-rgb), 0.1);
        color: var(--secondary-color);
    }
    
    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
    }
    
    .last-updated {
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    
    .mqtt-topic-display {
        font-family: monospace;
        background-color: rgba(0,0,0,0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.85rem;
        word-break: break-all;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header device-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-microchip me-3 fs-4"></i>
                <div>
                    <h4 class="mb-0">{{ $device->device_name }}</h4>
                    <small class="opacity-75">{{ $device->product->name }}</small>
                </div>

            </div>
            <span class="badge rounded-pill bg-{{ $device->status === 'active' ? 'success' : 'warning' }} px-3 py-2">
                {{ ucfirst($device->status) }}
            </span>
        </div>
        
        <div class="card-body">
            <!-- Device Info and Control Section -->
            <div class="row g-4 mb-4">
                <!-- Device Info -->
                <div class="col-lg-6">
                    <div class="card info-card">
                        <div class="card-header info-card-header d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <span>Informasi Perangkat</span>
                        </div>
                        <div class="card-body">
                            <table class="table info-table">
                                <tbody>
                                    <tr>
                                        <th>Produk</th>
                                        <td>{{ $device->product->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Topik MQTT</th>
                                        <td><span class="mqtt-topic-display">{{ $device->mqtt_topic }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Aktif Sejak</th>
                                        <td>{{ $device->created_at->translatedFormat('d F Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Data Terakhir</th>
                                        <td id="last-updated-text">
                                            @if($latestData)
                                                {{ $latestData->recorded_at ? $latestData->recorded_at->format('d F Y H:i') : 'N/A' }}
                                            @else
                                                Belum ada data
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div>
                                <a href="{{ route('client.devices.anomalies', $device) }}" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Check Anomalies
                                </a>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Relay Control -->
                <div class="col-lg-6">
                    <div class="card info-card">
                        <div class="card-header info-card-header d-flex align-items-center">
                            <i class="fas fa-sliders-h me-2"></i>
                            <span>Kontrol Relay</span>
                        </div>
                        <div class="card-body">
                            <form id="control-form" method="POST" action="{{ route('client.devices.control', $device) }}">
                                @csrf
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="channel" class="form-label">Channel</label>
                                        <select class="form-select" id="channel" name="channel" required>
                                            @for($i = 1; $i <= 4; $i++)
                                                <option value="{{ $i }}">Channel {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label d-block">Aksi</label>
                                        <div class="btn-group" role="group">
                                            <input type="radio" class="btn-check" name="command" id="command-on" value="on" autocomplete="off" checked>
                                            <label class="btn btn-outline-success" for="command-on"><i class="fas fa-power-off me-1"></i> ON</label>
                                            
                                            <input type="radio" class="btn-check" name="command" id="command-off" value="off" autocomplete="off">
                                            <label class="btn btn-outline-danger" for="command-off"><i class="fas fa-power-off me-1"></i> OFF</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100" id="control-button">
                                            <i class="fas fa-paper-plane me-1"></i> Kirim Perintah
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <!-- Relay Status -->
                            <div class="mt-4">
                                <h6 class="d-flex align-items-center">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <span>Status Relay</span>
                                </h6>
                                <div class="row g-2" id="relay-status">
                                    @for($i = 1; $i <= 4; $i++)
                                        <div class="col-6 col-md-3">
                                            <div class="relay-status-badge relay-off" id="relay-{{ $i }}">
                                                <small class="d-block">Channel {{ $i }}</small>
                                                <span>OFF</span>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Real-time Monitoring -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-line me-2"></i>
                        <span>Monitoring Real-time</span>
                    </h5>
                    <small class="last-updated" id="last-updated">
                        @if($latestData)
                            {{ $latestData->recorded_at ? $latestData->recorded_at->diffForHumans() : 'N/A' }}
                        @else
                            Belum ada data
                        @endif
                    </small>
                </div>
                <div class="card-body">
                    <!-- Metrics Cards -->
                    <div class="row g-3 mb-4">
                        <!-- Voltage -->
                        <div class="col-md-4 col-6">
                            <div class="card metric-card">
                                <div class="card-body text-center">
                                    <h6 class="text-primary d-flex align-items-center justify-content-center">
                                        <i class="fas fa-bolt me-2"></i>
                                        <span>Tegangan</span>
                                    </h6>
                                    <h2 class="metric-value text-primary">
                                        <span class="voltage-value">{{ $latestData ? number_format($latestData->voltage, 2) : '0.00' }}</span>
                                        <small class="metric-unit">V</small>
                                    </h2>
                                    <small class="text-muted">Tegangan Listrik</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Current -->
                        <div class="col-md-4 col-6">
                            <div class="card metric-card">
                                <div class="card-body text-center">
                                    <h6 class="text-success d-flex align-items-center justify-content-center">
                                        <i class="fas fa-tachometer-alt me-2"></i>
                                        <span>Arus</span>
                                    </h6>
                                    <h2 class="metric-value text-success">
                                        <span class="current-value">{{ $latestData ? number_format($latestData->current, 2) : '0.00' }}</span>
                                        <small class="metric-unit">A</small>
                                    </h2>
                                    <small class="text-muted">Arus Listrik</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Power -->
                        <div class="col-md-4 col-6">
                            <div class="card metric-card">
                                <div class="card-body text-center">
                                    <h6 class="text-danger d-flex align-items-center justify-content-center">
                                        <i class="fas fa-charging-station me-2"></i>
                                        <span>Daya</span>
                                    </h6>
                                    <h2 class="metric-value text-danger">
                                        <span class="power-value">{{ $latestData ? number_format($latestData->power, 2) : '0.00' }}</span>
                                        <small class="metric-unit">W</small>
                                    </h2>
                                    <small class="text-muted">Daya Aktif</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Energy -->
                        <div class="col-md-4 col-6">
                            <div class="card metric-card">
                                <div class="card-body text-center">
                                    <h6 class="text-info d-flex align-items-center justify-content-center">
                                        <i class="fas fa-battery-three-quarters me-2"></i>
                                        <span>Energi</span>
                                    </h6>
                                    <h2 class="metric-value text-info">
                                        <span class="energy-value">{{ $latestData ? number_format($latestData->energy, 2) : '0.00' }}</span>
                                        <small class="metric-unit">kWh</small>
                                    </h2>
                                    <small class="text-muted">Total Konsumsi</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Frequency -->
                        <div class="col-md-4 col-6">
                            <div class="card metric-card">
                                <div class="card-body text-center">
                                    <h6 class="text-warning d-flex align-items-center justify-content-center">
                                        <i class="fas fa-wave-square me-2"></i>
                                        <span>Frekuensi</span>
                                    </h6>
                                    <h2 class="metric-value text-warning">
                                        <span class="frequency-value">{{ $latestData ? number_format($latestData->frequency, 2) : '0.00' }}</span>
                                        <small class="metric-unit">Hz</small>
                                    </h2>
                                    <small class="text-muted">Frekuensi Listrik</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Power Factor -->
                        <div class="col-md-4 col-6">
                            <div class="card metric-card">
                                <div class="card-body text-center">
                                    <h6 class="text-purple d-flex align-items-center justify-content-center">
                                        <i class="fas fa-percentage me-2"></i>
                                        <span>Faktor Daya</span>
                                    </h6>
                                    <h2 class="metric-value text-purple">
                                        <span class="pf-value">{{ $latestData ? number_format($latestData->power_factor, 2) : '0.00' }}</span>
                                    </h2>
                                    <small class="text-muted">Cos φ</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Section -->
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-bolt me-2"></i>
                                    <span>Monitor Tegangan</span>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="voltageChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    <span>Monitor Arus</span>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="currentChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-charging-station me-2"></i>
                                    <span>Monitor Daya</span>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="powerChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-battery-three-quarters me-2"></i>
                                    <span>Monitor Energi</span>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="energyChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-battery-three-quarters me-2"></i>
                                    <span>Monitor Frekuensi</span>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="frequencyChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-battery-three-quarters me-2"></i>
                                    <span>Monitor Faktor Daya</span>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="powerFactorChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded'); // Debugging
    
    const deviceId = "{{ $device->id }}";
    
    // Debug endpoint URLs
    console.log('Monitoring endpoint:', `/client/devices/${deviceId}/monitoring-data`);
    console.log('Relay endpoint:', `/client/devices/${deviceId}/relay-status`);

    // Fungsi untuk handle error
    function handleError(error, context) {
        console.error(`Error in ${context}:`, error);
        // Tambahkan notifikasi error ke UI jika perlu
    }

    // Inisialisasi chart dengan error handling
    let voltageChart, currentChart, powerChart, energyChart, frequencyChart, powerFactorChart;
    
    try {
        voltageChart = initChart('voltageChart', 'Voltage (V)', 'rgba(78, 115, 223, 1)');
        currentChart = initChart('currentChart', 'Current (A)', 'rgba(28, 200, 138, 1)');
        powerChart = initChart('powerChart', 'Power (W)', 'rgba(231, 74, 59, 1)');
        energyChart = initChart('energyChart', 'Energy (kWh)', 'rgba(54, 162, 235, 1)');
        frequencyChart = initChart('frequencyChart', 'Frequency (Hz)', 'rgba(246, 194, 62, 1)');
        powerFactorChart = initChart('powerFactorChart', 'Power Factor', 'rgba(153, 102, 255, 1)', true);
        
        console.log('Charts initialized');
    } catch (error) {
        handleError(error, 'chart initialization');
    }

    function initChart(canvasId, label, color, isPowerFactor = false) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) {
            throw new Error(`Canvas element #${canvasId} not found`);
        }
        
        return new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: { labels: [], datasets: [{
                label: label,
                data: [],
                borderColor: color,
                backgroundColor: color.replace('1)', '0.1)'),
                borderWidth: 2,
                fill: true
            }]},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        beginAtZero: !isPowerFactor,
                        ...(isPowerFactor && { min: 0, max: 1 })
                    }
                }
            }
        });
    }

    // Fungsi untuk update data
    async function fetchMonitoringData() {
        try {
            const response = await axios.get(`/client/devices/${deviceId}/monitoring-data`);
            console.log('Monitoring data:', response.data); // Debug
            
            if (response.data.success) {
                updateCharts(response.data.chart);
                updateMetrics(response.data.latest);
            } else {
                throw new Error('Invalid response format');
            }
        } catch (error) {
            handleError(error, 'fetching monitoring data');
        }
    }

    async function fetchRelayStatus() {
        try {
            const response = await axios.get(`/client/devices/${deviceId}/relay-status`);
            console.log('Relay status:', response.data); // Debug
            updateRelays(response.data.relays);
        } catch (error) {
            handleError(error, 'fetching relay status');
        }
    }

    function updateCharts(data) {
        try {
            updateChart(voltageChart, data.labels, data.voltage);
            updateChart(currentChart, data.labels, data.current);
            updateChart(powerChart, data.labels, data.power);
            updateChart(energyChart, data.labels, data.energy);
            updateChart(frequencyChart, data.labels, data.frequency);
            updateChart(powerFactorChart, data.labels, data.power_factor);
        } catch (error) {
            handleError(error, 'updating charts');
        }
    }

    function updateChart(chart, labels, data) {
        if (!chart) return;
        
        chart.data.labels = labels;
        chart.data.datasets[0].data = data;
        chart.update();
    }

    function updateMetrics(data) {
        try {
            const format = num => parseFloat(num || 0).toFixed(2);
            
            document.querySelector('.voltage-value').textContent = format(data.voltage);
            document.querySelector('.current-value').textContent = format(data.current);
            document.querySelector('.power-value').textContent = format(data.power);
            document.querySelector('.energy-value').textContent = format(data.energy);
            document.querySelector('.frequency-value').textContent = format(data.frequency);
            document.querySelector('.pf-value').textContent = format(data.power_factor);
            
            const lastUpdated = new Date(data.timestamp);
            document.getElementById('last-updated').textContent = 
                'Updated: ' + lastUpdated.toLocaleTimeString();
            document.getElementById('last-updated-text').textContent = 
                lastUpdated.toLocaleString();
        } catch (error) {
            handleError(error, 'updating metrics');
        }
    }

    function updateRelays(relays) {
        try {
            relays.forEach(relay => {
                const element = document.getElementById(`relay-${relay.channel}`);
                if (element) {
                    element.className = `relay-status-badge relay-${relay.status}`;
                    element.querySelector('span').textContent = relay.status.toUpperCase();
                }
            });
        } catch (error) {
            handleError(error, 'updating relays');
        }
    }

    // Jalankan pertama kali
    fetchMonitoringData();
    fetchRelayStatus();
    
    // Set interval untuk polling (5 detik)
    const monitoringInterval = setInterval(fetchMonitoringData, 1000);
    const relayInterval = setInterval(fetchRelayStatus, 1000);

    // Bersihkan interval saat komponen unmount
    window.addEventListener('beforeunload', () => {
        clearInterval(monitoringInterval);
        clearInterval(relayInterval);
    });
});
</script>
@endpush
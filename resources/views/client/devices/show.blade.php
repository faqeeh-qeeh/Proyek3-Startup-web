@extends('client.layouts.app')

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
        const deviceId = "{{ $device->id }}";
        const authToken = "{{ auth('client')->user()->api_token }}"; // Pastikan ada API token
        
        // 1. Setup Echo instance
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config('broadcasting.connections.pusher.key') }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true,
            auth: {
                headers: {
                    'Authorization': 'Bearer ' + authToken
                }
            },
            enabledTransports: ['ws', 'wss']
        });
    
        // 2. Connect to private channel
        const channel = window.Echo.private(`device.${deviceId}`);
    
        // 3. Listen for updates
        channel.listen('.device.data.updated', (data) => {
            console.log('Real-time data received:', data);
            updateMonitoringDisplay(data);
            updateLastUpdated(data.timestamp);
        });
    
        // 4. Handle connection events
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('WebSocket connected');
            $('#connection-status').removeClass('bg-danger').addClass('bg-success')
                .html('<i class="fas fa-wifi"></i> Terhubung real-time');
        });
    
        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            console.log('WebSocket disconnected');
            $('#connection-status').removeClass('bg-success').addClass('bg-danger')
                .html('<i class="fas fa-wifi-slash"></i> Koneksi terputus');
        });
    
        // 5. Initial data load
        fetchLatestData();
    
        // 6. Fallback polling (jika WebSocket terputus)
        let pollingInterval;
        function startPolling() {
            pollingInterval = setInterval(fetchLatestData, 5000);
        }
    
        function stopPolling() {
            clearInterval(pollingInterval);
        }
    
        // 7. Functions
        function fetchLatestData() {
            axios.get(`/api/devices/${deviceId}/monitoring`)
                .then(response => {
                    if (response.data.success) {
                        updateMonitoringDisplay(response.data.data);
                        updateLastUpdated(response.data.last_updated);
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }
    
        function updateMonitoringDisplay(data) {
            if (!data) return;
            
            // Format angka dengan 2 desimal
            const format = num => num ? parseFloat(num).toFixed(2) : '0.00';
            
            $('.voltage-value').text(format(data.voltage));
            $('.current-value').text(format(data.current));
            $('.power-value').text(format(data.power));
            $('.energy-value').text(format(data.energy));
            $('.frequency-value').text(format(data.frequency));
            $('.pf-value').text(format(data.power_factor));
        }
    
        function updateLastUpdated(timestamp) {
            if (!timestamp) return;
            
            const date = new Date(timestamp);
            const options = { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            
            const formatted = date.toLocaleDateString('id-ID', options);
            $('#last-updated').html(`<i class="far fa-clock me-1"></i>${formatted}`);
            $('#last-updated-text').text(formatted);
        }
    
        // 8. Connection status indicator (tambahkan di HTML)
        $(document).on('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                fetchLatestData();
            }
        });
    
        // 9. Cleanup saat komponen dihancurkan
        window.addEventListener('beforeunload', function() {
            window.Echo.leave(`device.${deviceId}`);
            stopPolling();
        });
    });
    </script>
<!-- Pastikan ini berada di dalam tag <head> atau sebelum penutup tag <body> -->
    <script src="{{ asset('js/app.js') }}"></script>
<script>
    window.Echo.channel('device-data')
        .listen('DeviceDataUpdated', (e) => {
            console.log('DeviceDataUpdated event:', e);
            alert('Device ' + e.device_id + ' updated! Data: ' + JSON.stringify(e.data));
        });
</script>

@endpush
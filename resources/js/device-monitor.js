import axios from 'axios';
class DeviceMonitor {
    constructor(deviceId) {
        this.deviceId = deviceId;
        this.pollingInterval = 2000; // 2 detik
        this.retryCount = 0;
        this.maxRetries = 3;
        
        this.init();
    }

    init() {
        this.startPolling();
        console.log(`Monitoring initialized for device ${this.deviceId}`);
    }

    startPolling() {
        // Immediate first poll
        this.fetchData();
        
        // Set up interval
        setInterval(() => this.fetchData(), this.pollingInterval);
    }

    async fetchData() {
        try {
            const response = await axios.get(`/api/devices/${this.deviceId}/monitoring`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            this.retryCount = 0; // Reset retry counter
            
            if (response.data.status === 'success') {
                this.updateDisplay(response.data.data);
                this.updateLastUpdated(response.data.timestamp);
            } else {
                console.warn('No data available');
            }
        } catch (error) {
            this.handleFetchError(error);
        }
    }

    updateDisplay(data) {
        if (!data) return;

        // Format angka dengan 2 digit desimal
        const format = num => parseFloat(num).toFixed(2);
        
        $('.voltage-value').html(`${format(data.voltage)} <small>V</small>`);
        $('.current-value').html(`${format(data.current)} <small>A</small>`);
        $('.power-value').html(`${format(data.power)} <small>W</small>`);
        $('.energy-value').html(`${format(data.energy)} <small>kWh</small>`);
        $('.frequency-value').html(`${format(data.frequency)} <small>Hz</small>`);
        $('.pf-value').text(format(data.pf));
    }

    updateLastUpdated(timestamp) {
        const date = new Date(timestamp);
        const formatted = date.toLocaleString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        $('#last-updated').text(formatted);
        $('#last-updated-text').text(formatted);
    }

    handleFetchError(error) {
        this.retryCount++;
        
        if (this.retryCount <= this.maxRetries) {
            console.warn(`Retry ${this.retryCount}/${this.maxRetries}`);
            setTimeout(() => this.fetchData(), 1000);
        } else {
            console.error('Max retries reached:', error);
            $('#last-updated').html('<span class="text-danger">Gagal memuat data</span>');
        }
    }
}

// Inisialisasi
$(document).ready(function() {
    if ($('#device-monitoring-container').length) {
        const deviceId = $('#device-monitoring-container').data('device-id');
        window.deviceMonitor = new DeviceMonitor(deviceId);
    }
});
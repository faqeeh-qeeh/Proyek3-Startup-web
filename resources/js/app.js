// require('./bootstrap');
import './bootstrap';

// Global Device Monitoring Class
// class DeviceMonitor {
//     constructor(deviceId) {
//         this.deviceId = deviceId;
//         this.isConnected = true;
//         this.retryCount = 0;
//         this.maxRetries = 5;
//         this.isLoading = false;
//         this.pollingInterval = null;
//         this.echoChannel = null;
        
//         this.init();
//     }
    
//     init() {
//         this.initPolling();
//         this.initEcho();
//         this.initEventListeners();
//     }
    
//     initPolling() {
//         // Clear existing interval if any
//         if (this.pollingInterval) {
//             clearInterval(this.pollingInterval);
//         }
        
//         // Set up new polling interval (3 seconds)
//         this.pollingInterval = setInterval(() => {
//             if (this.isConnected && !this.isLoading) {
//                 this.fetchMonitoringData();
//             }
//         }, 3000);
        
//         // Initial fetch
//         this.fetchMonitoringData();
//     }
    
//     initEcho() {
//         // Unsubscribe from previous channel if exists
//         if (this.echoChannel) {
//             window.Echo.leave(`device.${this.deviceId}`);
//         }
        
//         // Subscribe to new channel
//         this.echoChannel = window.Echo.private(`device.${this.deviceId}`)
//             .listen('.device.data.updated', (data) => {
//                 this.handleRealTimeData(data);
//             })
//             .error((error) => {
//                 console.error('Echo channel error:', error);
//                 this.showAlert('danger', 'Koneksi real-time terputus, menggunakan polling');
//             });
//     }
    
//     initEventListeners() {
//         // Handle control form submission
//         $('#control-form').off('submit').on('submit', (e) => {
//             e.preventDefault();
//             this.sendControlCommand($(e.target));
//         });
        
//         // Handle visibility change (tab switching)
//         $(document).on('visibilitychange', () => {
//             if (document.visibilityState === 'visible') {
//                 this.fetchMonitoringData();
//             }
//         });
//     }
    
//     fetchMonitoringData() {
//         if (this.isLoading) return;
        
//         this.isLoading = true;
//         $('#last-updated').html('<i class="fas fa-spinner fa-spin me-1"></i>Memuat...');
        
//         axios.get(`/api/devices/${this.deviceId}/monitoring`, {
//             headers: {
//                 'X-Requested-With': 'XMLHttpRequest'
//             }
//         })
//         .then(response => {
//             this.retryCount = 0;
            
//             if (response.data.success) {
//                 this.updateDisplay(response.data.data);
//                 this.updateLastUpdated(response.data.last_updated);
//                 this.updateFooterTime();
//             } else {
//                 this.showAlert('warning', response.data.message || 'Data tidak tersedia');
//             }
//         })
//         .catch(error => {
//             this.handleFetchError(error);
//         })
//         .finally(() => {
//             this.isLoading = false;
//         });
//     }
    
//     handleRealTimeData(data) {
//         this.updateDisplay(data);
//         this.updateLastUpdated(data.timestamp);
//         this.updateFooterTime();
//     }
    
//     updateDisplay(data) {
//         if (!data) return;
        
//         $('.voltage-value').text(data.voltage ? data.voltage.toFixed(2) : '0.00');
//         $('.current-value').text(data.current ? data.current.toFixed(2) : '0.00');
//         $('.power-value').text(data.power ? data.power.toFixed(2) : '0.00');
//         $('.energy-value').text(data.energy ? data.energy.toFixed(2) : '0.00');
//         $('.frequency-value').text(data.frequency ? data.frequency.toFixed(2) : '0.00');
//         $('.pf-value').text(data.power_factor ? data.power_factor.toFixed(2) : '0.00');
//     }
    
//     updateLastUpdated(timestamp) {
//         if (!timestamp) return;
        
//         const date = new Date(timestamp);
//         $('#last-updated').html(`<i class="far fa-clock me-1"></i>${this.formatTime(date)}`);
//         $('#last-updated-text').text(this.formatTime(date));
//     }
    
//     updateFooterTime() {
//         const now = new Date();
//         const timeString = now.toLocaleTimeString('en-US', { 
//             hour: '2-digit', 
//             minute: '2-digit',
//             hour12: true
//         });
//         const dateString = now.toLocaleDateString('en-US', { 
//             month: 'numeric', 
//             day: 'numeric', 
//             year: 'numeric'
//         });
        
//         $('.footer-time').text(`${timeString} ${dateString}`);
//     }
    
//     formatTime(date) {
//         return date.toLocaleTimeString() + ' ' + date.toLocaleDateString();
//     }
    
//     sendControlCommand(form) {
//         const button = form.find('button[type="submit"]');
//         const originalText = button.html();
        
//         // Show loading state
//         button.prop('disabled', true);
//         button.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengirim...');
        
//         axios.post(form.attr('action'), form.serialize())
//             .then(response => {
//                 this.showAlert('success', 'Perintah berhasil dikirim');
//                 this.fetchRelayStatus();
//             })
//             .catch(error => {
//                 const errorMsg = error.response?.data?.message || 'Gagal mengirim perintah';
//                 this.showAlert('danger', errorMsg);
//             })
//             .finally(() => {
//                 button.prop('disabled', false);
//                 button.html(originalText);
//             });
//     }
    
//     fetchRelayStatus() {
//         axios.get(`/api/devices/${this.deviceId}/relay-status`)
//             .then(response => {
//                 if (response.data.relays) {
//                     this.updateRelayStatus(response.data.relays);
//                 }
//             })
//             .catch(error => {
//                 console.error('Error fetching relay status:', error);
//             });
//     }
    
//     updateRelayStatus(relays) {
//         relays.forEach(relay => {
//             const badge = $(`#relay-${relay.channel}`);
//             badge.removeClass('bg-success bg-danger bg-secondary');
            
//             if (relay.status === 'on') {
//                 badge.addClass('bg-success').text('ON');
//             } else {
//                 badge.addClass('bg-danger').text('OFF');
//             }
//         });
//     }
    
//     handleFetchError(error) {
//         this.retryCount++;
        
//         if (this.retryCount >= this.maxRetries) {
//             this.showAlert('danger', 'Gagal memuat data setelah beberapa percobaan');
//             this.isConnected = false;
            
//             setTimeout(() => {
//                 this.isConnected = true;
//                 this.retryCount = 0;
//             }, 30000);
//         } else {
//             console.error('Error fetching data:', error);
//             $('#last-updated').html('<i class="fas fa-exclamation-triangle me-1"></i>Gagal memuat');
//         }
//     }
    
//     showAlert(type, message) {
//         const alert = $(`
//             <div class="alert alert-${type} alert-dismissible fade show mb-3" role="alert">
//                 ${message}
//                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
//             </div>
//         `);
        
//         $('#app').prepend(alert);
        
//         setTimeout(() => {
//             alert.alert('close');
//         }, 5000);
//     }
// }

// // Initialize when device page is loaded
// if ($('#device-monitoring-container').length) {
//     const deviceId = $('#device-monitoring-container').data('device-id');
//     window.deviceMonitor = new DeviceMonitor(deviceId);
// }
<!DOCTYPE html>
<html>
<head>
    <title>Test Listen</title>
</head>
<body>
    <h1>Listening to DeviceDataUpdated...</h1>

    @vite('resources/js/app.js')

    <script>
        // Tunggu sampai window.Echo siap
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Window Echo:', window.Echo); // Debug
            window.Echo.channel('device-data')
                .listen('.DeviceDataUpdated', (e) => {
                    alert('DeviceDataUpdated event received: ' + JSON.stringify(e));
                });
        });
    </script>
</body>
</html>

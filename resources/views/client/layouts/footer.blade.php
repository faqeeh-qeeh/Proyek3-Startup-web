<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Tentang Kami</h5>
                <p>Sistem IoT Monitoring dan Kendali Listrik berbasis ESP32 dan PZEM-004T.</p>
            </div>
            <div class="col-md-4">
                <h5>Kontak</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-envelope me-2"></i> support@iot-monitoring.com</li>
                    <li><i class="fas fa-phone me-2"></i> +62 123 4567 890</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white">Kebijakan Privasi</a></li>
                    <li><a href="#" class="text-white">Syarat dan Ketentuan</a></li>
                    <li><a href="#" class="text-white">FAQ</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-4 bg-light">
        <div class="text-center">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
        </div>
    </div>
</footer>
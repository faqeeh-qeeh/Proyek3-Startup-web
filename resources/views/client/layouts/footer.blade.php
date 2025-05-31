{{-- <footer class="bg-dark text-white py-4 mt-5">
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
</footer> --}}

<footer class="footer py-5 mt-auto">
    <div class="container-fluid px-4">
        <div class="row g-4">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="footer-logo d-flex align-items-center mb-3">
                    <i class="fas fa-bolt text-primary me-2 fs-4"></i>
                    <span class="fw-bold fs-4">Mocid</span>
                </div>
                <p class="text-muted">Sistem IoT Monitoring dan Kendali berbasis ESP32 dan Sensor yang modern dan efisien.</p>
                <div class="social-icons mt-4">
                    <a href="#" class="text-decoration-none me-3">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-decoration-none me-3">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-decoration-none me-3">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-decoration-none">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                <h5 class="fw-bold mb-4">Menu</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('client.products.index') }}" class="text-decoration-none">Produk</a></li>
                    <li class="mb-2"><a href="{{ route('client.devices.index') }}" class="text-decoration-none">Perangkat</a></li>
                    <li class="mb-2"><a href="{{ route('client.orders.index') }}" class="text-decoration-none">Pesanan</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Profil</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                <h5 class="fw-bold mb-4">Dukungan</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-decoration-none">Kontak Kami</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">FAQ</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Dokumentasi</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Forum</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4">
                <h5 class="fw-bold mb-4">Kontak</h5>
                <ul class="list-unstyled text-muted">
                    <li class="mb-3 d-flex">
                        <i class="fas fa-map-marker-alt text-primary mt-1 me-2"></i>
                        <span>Jl. Raya Lohbener Lama No.08, Legok, Kec. Lohbener, Kabupaten Indramayu, Jawa Barat 45252</span>
                    </li>
                    <li class="mb-3 d-flex">
                        <i class="fas fa-envelope text-primary mt-1 me-2"></i>
                        <span>support@iot-monitoring.com</span>
                    </li>
                    <li class="d-flex">
                        <i class="fas fa-phone-alt text-primary mt-1 me-2"></i>
                        <span>+62 896 6398 3455</span>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="my-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-muted">&copy; {{ date('Y') }} Mocid. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item"><a href="#" class="text-decoration-none">Kebijakan Privasi</a></li>
                    <li class="list-inline-item"><a href="#" class="text-decoration-none px-2">·</a></li>
                    <li class="list-inline-item"><a href="#" class="text-decoration-none">Syarat & Ketentuan</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
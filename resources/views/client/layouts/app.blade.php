<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/client.css') }}" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>  
    @stack('styles')
</head>
<body class="bg-body">
    <div id="app">
        <!-- Navigation -->
        @include('client.layouts.navigation')
        
        <main class="py-4">
            <div class="container-fluid px-4">
                <!-- Notifikasi -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <!-- Content -->
                @yield('content')
            </div>
        </main>
        
        <!-- Footer -->
        @include('client.layouts.footer')
    </div>
    
    <!-- jQuery, Bootstrap JS, dan dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Paho MQTT -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.1.0/paho-mqtt.min.js"></script>
    
    <!-- Custom JS untuk tampilan -->
    <script src="{{ asset('js/client.js') }}"></script>
    
    @stack('scripts')
    <!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Profil Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        @if(Auth::guard('client')->user()->gender === 'male')
                            <i class="fas fa-male fs-1 text-primary mb-3"></i>
                        @else
                            <i class="fas fa-female fs-1 text-primary mb-3"></i>
                        @endif
                        <h4>{{ Auth::guard('client')->user()->full_name }}</h4>
                        <p class="text-muted">{{ Auth::guard('client')->user()->email }}</p>
                    </div>
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Informasi Pribadi</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Username:</strong> {{ Auth::guard('client')->user()->username }}</p>
                                        <p><strong>Jenis Kelamin:</strong> 
                                            {{ Auth::guard('client')->user()->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse(Auth::guard('client')->user()->birth_date)->format('d F Y') }}</p>
                                        <p><strong>Nomor WhatsApp:</strong> {{ Auth::guard('client')->user()->whatsapp_number }}</p>
                                    </div>
                                </div>
                                <p><strong>Alamat:</strong> {{ Auth::guard('client')->user()->address }}</p>
                            </div>
                        </div>
                        {{-- <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Statistik Akun</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <h3 class="text-primary">5</h3>
                                        <p class="text-muted">Perangkat</p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h3 class="text-primary">12</h3>
                                        <p class="text-muted">Pesanan</p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h3 class="text-primary">3</h3>
                                        <p class="text-muted">Aktif</p>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary">Edit Profil</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
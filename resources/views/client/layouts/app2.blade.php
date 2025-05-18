<!DOCTYPE html>
<html lang="en">
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
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/client.css') }}" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>   --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>  
    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Navigation -->
        @include('client.layouts.navigation')
        
        <main class="py-4">
            <div class="container">
                <!-- Notifikasi -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
</body>
</html>
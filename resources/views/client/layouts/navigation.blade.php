{{-- <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('client.products.index') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.products.*') ? 'active' : '' }}" 
                       href="{{ route('client.products.index') }}">
                        <i class="fas fa-box me-1"></i> Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.devices.*') ? 'active' : '' }}" 
                       href="{{ route('client.devices.index') }}">
                        <i class="fas fa-microchip me-1"></i> Perangkat Saya
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.orders.*') ? 'active' : '' }}" 
                       href="{{ route('client.orders.index') }}">
                        <i class="fas fa-shopping-cart me-1"></i> Pesanan
                    </a>
                </li>
            </ul>
            
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" 
                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> {{ Auth::guard('client')->user()->full_name }}
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user me-1"></i> Profil
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-1"></i> Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('client.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                        
                        <form id="logout-form" action="{{ route('client.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav> --}}


<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('client.products.index') }}">
            <i class="fas fa-bolt me-2"></i>
            <span class="fw-bold">Mocid</span>
        </a>
        
        <div class="d-flex align-items-center order-lg-3 ms-auto">
            <!-- Theme Toggle -->
            <button class="btn btn-link text-white me-2" id="themeToggle">
                <i class="fas fa-moon" id="themeIcon"></i>
            </button>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        
        <div class="collapse navbar-collapse order-lg-2" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.products.*') ? 'active' : '' }}" 
                       href="{{ route('client.products.index') }}">
                        <i class="fas fa-box me-1"></i> Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.devices.*') ? 'active' : '' }}" 
                       href="{{ route('client.devices.index') }}">
                        <i class="fas fa-microchip me-1"></i> Perangkat Saya
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.orders.*') ? 'active' : '' }}" 
                       href="{{ route('client.orders.index') }}">
                        <i class="fas fa-shopping-cart me-1"></i> Pesanan
                    </a>
                </li>
            </ul>
            
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-lg-auto">

                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" 
                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if(Auth::guard('client')->user()->gender === 'male')
                            <i class="fas fa-male me-2"></i>
                        @else
                            <i class="fas fa-female me-2"></i>
                        @endif
                        <span class="d-none d-lg-inline">{{ Auth::guard('client')->user()->full_name }}</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end shadow-sm">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    @if(Auth::guard('client')->user()->gender === 'male')
                                        <i class="fas fa-male fs-3 text-primary"></i>
                                    @else
                                        <i class="fas fa-female fs-3 text-primary"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ Auth::guard('client')->user()->full_name }}</h6>
                                    <small class="text-muted">{{ Auth::guard('client')->user()->email }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                            <i class="fas fa-user me-2"></i> Profil
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i> Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('client.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                        
                        <form id="logout-form" action="{{ route('client.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
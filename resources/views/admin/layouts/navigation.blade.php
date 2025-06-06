<nav class="navbar navbar-expand-lg navbar-dark bg-primary border-bottom">
    <div class="container-fluid">
        <button class="btn btn-sm btn-transparent" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="d-flex align-items-center ms-auto">
            <!-- Dark/Light Mode Toggle -->
            <div class="form-check form-switch me-3">
                <input class="form-check-input" type="checkbox" id="darkModeToggle">
                <label class="form-check-label text-white" for="darkModeToggle">
                    <i class="fas fa-moon"></i>
                </label>
            </div>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> {{ Auth::guard('admin')->user()->username }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
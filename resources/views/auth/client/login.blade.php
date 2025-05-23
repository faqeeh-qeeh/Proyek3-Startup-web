@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="glass-card p-4">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">Client Portal</h2>
                    <p class="text-muted">Login sebagai client untuk memanfaatkan sistem</p>
                </div>

                {{-- <div class="card-body px-4 py-4"> --}}
                    <form method="POST" action="{{ route('client.login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="login" class="form-label small text-muted">Username/Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-person text-muted"></i>
                                </span>
                                <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" 
                                       name="login" value="{{ old('login') }}" required autofocus placeholder="masukkan username atau email">
                            </div>
                            @error('login')
                                <div class="invalid-feedback d-block small">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label small text-muted">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required placeholder="masukkan password">
                                <button class="btn btn-light toggle-password" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block small">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="remember">
                                    Remember Me
                                </label>
                            </div>
                            @if (Route::has('client.password.request'))
                                <a href="{{ route('client.password.request') }}" class="small text-decoration-none">
                                    Forgot Password?
                                </a>
                            @endif
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </div>

                        <div class="text-center small pt-2">
                            Don't have an account? 
                            <a href="{{ route('client.register') }}" class="text-decoration-none fw-semibold">
                                Register
                            </a>
                        </div>
                    </form>
                {{-- </div> --}}
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 10px !important;
    }
    
    .toggle-password {
        border-left: 0;
        background: #f8f9fa;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.1);
        border-color: #6366f1;
    }
    
    .input-group-text {
        border-right: 0;
    }
    
    .form-control {
        border-left: 0;
        background: #f8f9fa;
    }
</style>

<script>
    // Password toggle script same as before
    document.querySelectorAll('.toggle-password').forEach(function(button) {
        button.addEventListener('click', function() {
            const passwordInput = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });
</script>
@endsection
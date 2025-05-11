{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Client Products</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4>Welcome, {{ Auth::guard('client')->user()->full_name }}!</h4>
                    <p>Here are the products available for you:</p>
                    
                    <!-- Daftar produk akan ditampilkan di sini -->
                    <div class="alert alert-info">
                        Product list will be displayed here.
                    </div>

                    <form method="POST" action="{{ route('client.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger mt-3">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
<div class="border-end" id="sidebar-wrapper">
    <div class="sidebar-heading text-white py-4 d-flex align-items-center justify-content-between">
        <div>
            <i class="fas fa-bolt me-2"></i> <b>Mocid</b>
        </div>
        <div class="sidebar-close d-lg-none">
            <i class="fas fa-times"></i>
        </div>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
        <a href="{{ route('admin.clients.index') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-users me-2"></i> Clients
        </a>
        <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-boxes me-2"></i> Products
        </a>
        <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-shopping-cart me-2"></i> Orders
        </a>
        <a href="{{ route('admin.devices.index') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-microchip me-2"></i> Devices
        </a>
    </div>
</div>
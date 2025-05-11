<div class="bg-dark border-right" id="sidebar-wrapper">
    <div class="sidebar-heading text-white py-4">
        <i class="fas fa-bolt me-2"></i> IoT Monitoring
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
        <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-boxes me-2"></i> Products
        </a>
        <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-shopping-cart me-2"></i> Orders
        </a>
        <a href="{{ route('admin.devices.index') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-microchip me-2"></i> Devices
        </a>
    </div>
</div>
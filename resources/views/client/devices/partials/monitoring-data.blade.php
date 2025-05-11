<div class="col-md-4 mb-3">
    <div class="card bg-light">
        <div class="card-body text-center">
            <h6><i class="fas fa-bolt me-2"></i>Voltage</h6>
            <h3 id="voltage-value">{{ $voltage }} <small>V</small></h3>
        </div>
    </div>
</div>
<div class="col-md-4 mb-3">
    <div class="card bg-light">
        <div class="card-body text-center">
            <h6><i class="fas fa-tachometer-alt me-2"></i>Current</h6>
            <h3 id="current-value">{{ $current }} <small>A</small></h3>
        </div>
    </div>
</div>
<div class="col-md-4 mb-3">
    <div class="card bg-light">
        <div class="card-body text-center">
            <h6><i class="fas fa-charging-station me-2"></i>Power</h6>
            <h3 id="power-value">{{ $power }} <small>W</small></h3>
        </div>
    </div>
</div>
<div class="col-md-4 mb-3">
    <div class="card bg-light">
        <div class="card-body text-center">
            <h6><i class="fas fa-battery-three-quarters me-2"></i>Energy</h6>
            <h3 id="energy-value">{{ $energy }} <small>kWh</small></h3>
        </div>
    </div>
</div>
<div class="col-md-4 mb-3">
    <div class="card bg-light">
        <div class="card-body text-center">
            <h6><i class="fas fa-wave-square me-2"></i>Frequency</h6>
            <h3 id="frequency-value">{{ $frequency }} <small>Hz</small></h3>
        </div>
    </div>
</div>
<div class="col-md-4 mb-3">
    <div class="card bg-light">
        <div class="card-body text-center">
            <h6><i class="fas fa-percentage me-2"></i>Power Factor</h6>
            <h3 id="pf-value">{{ $powerFactor }}</h3>
        </div>
    </div>
</div>
<div class="col-12 text-center mt-2">
    <small class="text-muted">Last updated: <span id="last-updated">Never</span></small>
</div>
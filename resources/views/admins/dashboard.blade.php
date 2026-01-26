@extends('layouts.admin_app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-start">
    <div>
        <h6 class="mb-1">{{ \Carbon\Carbon::now()->format('l, M d, Y') }}</h6>
        <h4 class="fw-bold">
            Good day, {{ auth()->user()->role ?? 'Administrator' }}!
        </h4>
    </div>
    <!-- Temporary Add Marketplace Button -->
    <a href="{{ route('admins.marketplaces.create') }}" class="btn btn-primary">
        <i class="bx bx-plus me-1"></i> Add Marketplace
    </a>
</div>
<hr class="my-4">

<div class="row g-4 mb-4">

    <!-- Active Tenants -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ url('/tenants/approved') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-primary rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-user text-primary fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Active Tenants</span>
                        <h1 class="fw-bold mb-3" id="activeTenants">-</h1>
                        <small class="text-muted d-block fw-semibold">Currently operating tenants</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Vacant Stalls -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ url('/stalls') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-warning rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-store text-warning fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Vacant Stalls</span>
                        <h1 class="fw-bold mb-3" id="vacantStalls">-</h1>
                        <small class="text-muted d-block fw-semibold">Stalls with no ongoing contract</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Expiring Contracts -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ url('/contracts') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-danger rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-file text-danger fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Expiring Contracts</span>
                        <h1 class="fw-bold mb-3" id="expiringContracts">-</h1>
                        <small class="text-muted d-block fw-semibold">Expiring within 30 days</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Rent Collected -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ url('/bills') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-success rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-credit-card text-success fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Rent Collected</span>
                        <h1 class="fw-bold mb-3" id="rentCollected">₱-</h1>
                        <small class="text-muted d-block fw-semibold">Total rent collected this month</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load dashboard statistics
    $.ajax({
        url: "{{ route('admins.dashboard.stats') }}",
        method: 'GET',
        success: function(response) {
            $('#activeTenants').text(response.activeTenants);
            $('#vacantStalls').text(response.vacantStalls);
            $('#expiringContracts').text(response.expiringContracts);
            $('#rentCollected').text('₱' + parseFloat(response.rentCollected).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        },
        error: function() {
            console.error('Failed to load dashboard statistics');
        }
    });
});
</script>
@endpush
@endsection

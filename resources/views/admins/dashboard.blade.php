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
</div>
<hr class="my-4">

<div class="row g-4 mb-4">

    <!-- Manage Users -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ url('/users') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-primary rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-user text-primary fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Manage Users</span>
                        <h1 class="fw-bold mb-3" id="manageUsers">{{ $dashboardStats['manageUsers'] ?? 0 }}</h1>
                        <small class="text-muted d-block fw-semibold">Total registered accounts</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Occupied Stalls -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ url('/stalls') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-warning rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-store text-warning fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Occupied Stalls</span>
                        <h1 class="fw-bold mb-3" id="occupiedStalls">
                            {{ ($dashboardStats['occupiedStalls'] ?? 0) . '/' . ($dashboardStats['totalStalls'] ?? 0) }}
                        </h1>
                        <small class="text-muted d-block fw-semibold">
                            {{ ($dashboardStats['occupiedStalls'] ?? 0) . ' out of ' . ($dashboardStats['totalStalls'] ?? 0) . ' occupied' }}
                        </small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Expiring Contracts -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('admins.leases.index', ['status' => 'Expiring']) }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-danger rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-file text-danger fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Expiring Contracts</span>
                        <h1 class="fw-bold mb-3" id="expiringContracts">{{ $dashboardStats['expiringContracts'] ?? 0 }}</h1>
                        <small class="text-muted d-block fw-semibold">View expiring leases</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Expected Rent Collected -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ url('/bills') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-success rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-credit-card text-success fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Expected Rent Collected</span>
                        <h1 class="fw-bold mb-3" id="expectedRentCollected">₱{{ $dashboardStats['expectedRentCollected'] ?? '0.00' }}</h1>
                        <small class="text-muted d-block fw-semibold">Bills due this month</small>
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
            $('#manageUsers').text(response.manageUsers);
            const occupied = response.occupiedStalls ?? 0;
            const total = response.totalStalls ?? 0;
            $('#occupiedStalls').text(`${occupied}/${total}`);
            $('#occupiedStalls').next('small').text(`${occupied} out of ${total} occupied`);
            $('#expiringContracts').text(response.expiringContracts);
            $('#expectedRentCollected').text('₱' + parseFloat(response.expectedRentCollected).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        },
        error: function() {
            console.error('Failed to load dashboard statistics');
        }
    });
});
</script>
@endpush
@endsection

@extends('layouts.tenant_app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h6 class="mb-1">{{ \Carbon\Carbon::now()->format('l, M d, Y') }}</h6>
    <h4 class="fw-bold">
        Good day, {{ auth()->user()->firstName ?? 'Tenant' }}!
    </h4>
</div>
<hr class="my-4">

<div class="row g-4 mb-4">
    <!-- Active Leases -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('tenants.leases.index') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-primary rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-file text-primary fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Active Leases</span>
                        <h1 class="fw-bold mb-3" id="activeLeases">-</h1>
                        <small class="text-muted d-block fw-semibold">Your active contracts</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Upcoming Bills -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('tenants.bills.index') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-warning rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-calendar text-warning fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Upcoming Bills</span>
                        <h1 class="fw-bold mb-3" id="upcomingBills">-</h1>
                        <small class="text-muted d-block fw-semibold">Due in next 30 days</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Overdue Bills -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('tenants.bills.index') }}?status=Due" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-danger rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-error text-danger fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Overdue Bills</span>
                        <h1 class="fw-bold mb-3" id="overdueBills">-</h1>
                        <small class="text-muted d-block fw-semibold">Past due date</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Pending Amount -->
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('tenants.bills.index') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3 bg-label-info rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-money text-info fs-3"></i>
                    </div>
                    <div>
                        <span class="d-block text-dark fw-semibold fs-5 mb-2">Pending Amount</span>
                        <h1 class="fw-bold mb-3" id="pendingAmount">₱-</h1>
                        <small class="text-muted d-block fw-semibold">Total pending payment</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div id="tenantEmptyState" class="alert alert-info d-none">
    Your dashboard is empty right now. Once you have an active lease or bills, the summaries will appear here.
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load dashboard statistics
    $.ajax({
        url: "{{ route('tenants.dashboard.stats') }}",
        method: 'GET',
        success: function(response) {
            $('#activeLeases').text(response.activeLeases);
            $('#upcomingBills').text(response.upcomingBills);
            $('#overdueBills').text(response.overdueBills);
            $('#pendingAmount').text('₱' + parseFloat(response.pendingAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

            const hasData = Number(response.activeLeases) > 0
                || Number(response.upcomingBills) > 0
                || Number(response.overdueBills) > 0
                || parseFloat(response.pendingAmount) > 0;
            if (!hasData) {
                $('#tenantEmptyState').removeClass('d-none');
            }
        },
        error: function() {
            console.error('Failed to load dashboard statistics');
        }
    });
});
</script>
@endpush
@endsection

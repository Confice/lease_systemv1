@extends('layouts.admin_app')

@section('title', 'Feedback #' . $feedback->feedbackID)
@section('page-title', 'Feedback Details')

@section('content')
<div class="row">
  <div class="col-12 col-lg-10 mx-auto">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="card-title mb-0">
          <i class="bx bx-message-detail me-2"></i>Feedback #{{ $feedback->feedbackID }}
        </h5>
        <a href="{{ route('admins.feedback.index') }}" class="btn btn-label-secondary">
          <i class="bx bx-arrow-back me-1"></i> Back to Tenant Feedback
        </a>
      </div>
      <div class="card-body">
        @php
          $tenant = $feedback->tenant;
          $tenantName = $tenant
            ? trim(($tenant->firstName ?? '') . ' ' . ($tenant->lastName ?? ''))
            : 'Unknown Tenant';
        @endphp

        <div class="d-flex justify-content-between flex-wrap gap-3 mb-4">
          <div>
            <h6 class="text-muted mb-1">Tenant</h6>
            <p class="mb-0 fw-semibold">{{ $tenantName }}</p>
            @if($tenant)
              <small class="text-muted">{{ $tenant->email ?? '' }}</small>
              @if($tenant->contactNo)
                <span class="text-muted"> • {{ $tenant->contactNo }}</span>
              @endif
            @endif
          </div>
          <div>
            <h6 class="text-muted mb-1">Submitted</h6>
            <p class="mb-0 fw-semibold">{{ $feedback->created_at ? $feedback->created_at->format('F d, Y h:i A') : '—' }}</p>
          </div>
        </div>

        <div class="row mb-4 g-3">
          <div class="col-md-6">
            <div class="border rounded p-3 h-100">
              <h6 class="text-muted mb-2">Contract</h6>
              <p class="mb-1"><strong>ID:</strong> {{ $feedback->contractID ?? '—' }}</p>
              <p class="mb-1"><strong>Start:</strong> {{ $feedback->contract && $feedback->contract->startDate ? $feedback->contract->startDate->format('M d, Y') : '—' }}</p>
              <p class="mb-0"><strong>End:</strong> {{ $feedback->contract && $feedback->contract->endDate ? $feedback->contract->endDate->format('M d, Y') : '—' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3 h-100">
              <h6 class="text-muted mb-2">Stall & Marketplace</h6>
              @php
                $stall = $feedback->contract && $feedback->contract->stall ? $feedback->contract->stall : null;
                $marketplace = $stall && $stall->marketplace ? $stall->marketplace : null;
              @endphp
              <p class="mb-1"><strong>Stall:</strong> {{ $stall ? $stall->stallNo : '—' }}</p>
              <p class="mb-1"><strong>Marketplace:</strong> {{ $marketplace ? $marketplace->marketplace : '—' }}</p>
              <p class="mb-0"><strong>Address:</strong> {{ optional($marketplace)->marketplaceAddress ?? '—' }}</p>
            </div>
          </div>
        </div>

        @foreach($sections as $sectionName => $items)
          <div class="card mb-3">
            <div class="card-body">
              <h6 class="card-title text-primary mb-3">{{ $sectionName }}</h6>
              <ul class="list-unstyled mb-0">
                @foreach($items as $field => $label)
                  <li class="d-flex justify-content-between border-bottom py-2">
                    <span class="me-3">{{ $label }}</span>
                    <span class="fw-semibold">{{ $feedback->$field ?? '—' }} / 5</span>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
        @endforeach

        <div class="mt-4">
          <h6 class="text-muted mb-2">Additional Comments</h6>
          <p class="mb-0">{{ $feedback->comments ?: 'No additional comments.' }}</p>
        </div>

        <div class="mt-4 pt-3 border-top">
          <form action="{{ route('admins.feedback.archive', $feedback) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this feedback?');">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">
              <i class="bx bx-archive me-1"></i> Archive
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@extends('layouts.admin_app')

@section('title', 'Tenant Feedback')
@section('page-title', 'Tenant Feedback')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <!-- Search Bar -->
        <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
          <div class="position-relative w-100">
            <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 1; color: #7F9267;">
              <i class="bx bx-search fs-5"></i>
            </span>
            <input type="text" id="feedbackSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search by tenant name..." aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;">
          </div>
        </div>
        <!-- /Search Bar -->
        
        <span class="badge bg-label-primary text-uppercase">
          {{ $feedbackEntries->total() }} Total Entries
        </span>
      </div>
      @php
        $sections = [
          'Marketplace & Stall Experience' => [
            'usability_comprehension' => 'My stall was clean and ready for operations on move-in.',
            'usability_learning' => 'Maintenance requests for my stall are addressed promptly.',
            'usability_effort' => 'Common areas (restrooms, walkways, etc.) are well-maintained.',
            'usability_interface' => 'The marketplace environment feels safe and welcoming.',
          ],
          'Lease Operations & Support' => [
            'functionality_registration' => 'Store applications and renewals are easy to process.',
            'functionality_tasks' => 'Lease/marketplace staff respond to my concerns promptly.',
            'functionality_results' => 'Billing and charges are accurate and easy to understand.',
            'functionality_security' => 'Policies on security and crowd control are effective.',
          ],
          'System Experience' => [
            'reliability_error_handling' => 'I can accomplish my leasing tasks in the system without issues.',
            'reliability_command_tolerance' => 'System errors are handled clearly (helpful messages or guidance).',
            'reliability_recovery' => 'I can recover or retry easily if something fails online.',
          ],
        ];
      @endphp
      <div class="table-responsive feedback-table-wrapper">
        <table class="table table-hover table-striped mb-0 align-middle feedback-table">
          <thead class="table-light">
            <tr>
              <th style="width: 140px;">Date Submitted</th>
              <th style="width: 220px;">Tenant</th>
              <th>Stall Experience</th>
              <th>Operations & Support</th>
              <th>System Use</th>
              <th style="width: 220px;">Comments</th>
              <th class="text-center" style="width: 120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @php
              $formatScore = function ($scores) {
                  $filtered = collect($scores)->filter(fn ($value) => !is_null($value));
                  if ($filtered->isEmpty()) {
                      return ['avg' => null, 'count' => 0];
                  }
                  $avg = round($filtered->avg(), 1);
                  return ['avg' => $avg, 'count' => $filtered->count()];
              };
            @endphp
            @forelse($feedbackEntries as $feedback)
              @php
                $stallExperience = $formatScore([
                  $feedback->usability_comprehension,
                  $feedback->usability_learning,
                  $feedback->usability_effort,
                  $feedback->usability_interface,
                ]);
                $operationsSupport = $formatScore([
                  $feedback->functionality_registration,
                  $feedback->functionality_tasks,
                  $feedback->functionality_results,
                  $feedback->functionality_security,
                ]);
                $systemUse = $formatScore([
                  $feedback->reliability_error_handling,
                  $feedback->reliability_command_tolerance,
                  $feedback->reliability_recovery,
                ]);

                $tenant = $feedback->tenant;
                $tenantName = $tenant
                  ? trim(($tenant->firstName ?? '') . ' ' . ($tenant->lastName ?? ''))
                  : 'Unknown Tenant';
              @endphp
              <tr>
                <td data-label="Date Submitted">
                  <div class="fw-semibold">{{ $feedback->created_at?->format('m/d/y') ?? '—' }}</div>
                  <small class="text-muted">{{ $feedback->created_at?->format('h:i A') }}</small>
                </td>
                <td data-label="Tenant">
                  <div class="fw-semibold text-dark">{{ $tenantName }}</div>
                  <small class="text-muted">ID: {{ $tenant->id ?? '—' }}</small>
                </td>
                <td data-label="Stall Experience">
                  @if($stallExperience['avg'])
                    <div class="fw-semibold">{{ $stallExperience['avg'] }} / 5</div>
                    <small class="text-muted">{{ $stallExperience['count'] }} items</small>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td data-label="Operations & Support">
                  @if($operationsSupport['avg'])
                    <div class="fw-semibold">{{ $operationsSupport['avg'] }} / 5</div>
                    <small class="text-muted">{{ $operationsSupport['count'] }} items</small>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td data-label="System Use">
                  @if($systemUse['avg'])
                    <div class="fw-semibold">{{ $systemUse['avg'] }} / 5</div>
                    <small class="text-muted">{{ $systemUse['count'] }} items</small>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td data-label="Comments">
                  <p class="mb-0 text-wrap">{{ $feedback->comments ?? 'No additional comments.' }}</p>
                </td>
                @php
                  $sectionPayload = [];
                  foreach ($sections as $sectionName => $items) {
                      $sectionPayload[$sectionName] = collect($items)->map(function ($label, $field) use ($feedback) {
                          return [
                              'label' => $label,
                              'value' => $feedback->{$field},
                          ];
                      })->values();
                  }

                  $detailPayload = [
                      'submitted_at' => optional($feedback->created_at)->format('F d, Y h:i A'),
                      'tenant' => [
                          'name' => $tenantName,
                          'email' => $tenant->email ?? null,
                          'contact' => $tenant->contactNo ?? null,
                      ],
                      'contract' => [
                          'id' => $feedback->contractID,
                          'start_date' => optional(optional($feedback->contract)->startDate)->format('M d, Y'),
                          'end_date' => optional(optional($feedback->contract)->endDate)->format('M d, Y'),
                      ],
                      'stall' => [
                          'stall_no' => optional(optional($feedback->contract)->stall)->stallNo,
                          'marketplace' => optional(optional(optional($feedback->contract)->stall)->marketplace)->marketplace,
                          'marketplace_address' => optional(optional(optional($feedback->contract)->stall)->marketplace)->marketplaceAddress,
                      ],
                      'sections' => $sectionPayload,
                      'comments' => $feedback->comments,
                  ];
                @endphp
                <td class="text-center actions-cell" data-label="Actions">
                  <div class="d-flex justify-content-center gap-2">
                    <button
                      type="button"
                      class="btn btn-sm btn-icon btn-outline-primary btn-view-feedback"
                      title="View response"
                      data-bs-toggle="offcanvas"
                      data-bs-target="#feedbackDetailDrawer"
                      data-feedback='@json($detailPayload)'
                    >
                      <i class="bx bx-show"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-icon btn-outline-secondary text-muted btn-archive-feedback" data-id="{{ $feedback->feedbackID }}" data-url="{{ route('admins.feedback.archive', $feedback) }}" title="Archive response">
                      <i class="bx bx-archive"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  No feedback submissions yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($feedbackEntries->hasPages())
        <div class="card-footer">
          {{ $feedbackEntries->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

@include('admins.feedback_form.modal')

@push('styles')
<style>
  .feedback-table-wrapper {
    width: 100%;
  }

  @media (max-width: 991.98px) {
    .feedback-table thead {
      display: none;
    }

    .feedback-table tbody tr {
      display: block;
      margin-bottom: 1rem;
      border: 1px solid #e4e4e4;
      border-radius: 0.5rem;
      padding: 0.5rem 0;
    }

    .feedback-table tbody td {
      display: flex;
      justify-content: space-between;
      padding: 0.5rem 1rem;
      border: none !important;
    }

    .feedback-table tbody td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #6c757d;
      text-transform: uppercase;
      font-size: 0.7rem;
      margin-right: 1rem;
      flex: 1;
    }

    .feedback-table tbody td > * {
      flex: 1;
      text-align: right;
    }

    .feedback-table tbody td.actions-cell {
      justify-content: flex-start;
    }

    .feedback-table tbody td.actions-cell::before {
      flex: initial;
      margin-right: 0.5rem;
    }

    .feedback-table tbody td.actions-cell > div {
      flex: initial;
    }
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  // Search functionality for feedback table
  $('#feedbackSearch').on('keyup', function() {
    const searchTerm = $(this).val().toLowerCase();
    $('.feedback-table tbody tr').each(function() {
      const rowText = $(this).text().toLowerCase();
      if (rowText.includes(searchTerm)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  // Clear search when input is cleared
  $('#feedbackSearch').on('input', function() {
    if ($(this).val() === '') {
      $('.feedback-table tbody tr').show();
    }
  });

  // Archive feedback with SweetAlert confirmation
  $(document).on('click', '.btn-archive-feedback', function() {
    const feedbackId = $(this).data('id');
    const archiveUrl = $(this).data('url');
    const $button = $(this);

    Swal.fire({
      title: 'Archive Feedback?',
      text: 'Are you sure you want to archive this feedback? This action can be undone from the archived items page.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#7F9267',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, archive it!',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        // Create a form and submit it
        const form = $('<form>', {
          'method': 'POST',
          'action': archiveUrl
        });
        form.append($('<input>', {
          'type': 'hidden',
          'name': '_token',
          'value': $('meta[name="csrf-token"]').attr('content')
        }));
        $('body').append(form);
        form.submit();
      }
    });
  });
});
</script>
@endpush
@endsection


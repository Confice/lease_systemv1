@extends('layouts.tenant_app')

@section('title', 'Feedback')
@section('page-title', 'Feedback')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-header">
        <h5 class="mb-1">Tenant Satisfaction Survey</h5>
        <small class="text-muted">
          Directions: Rate each statement about your renting experience. 5 = Excellent, 4 = Good, 3 = Average, 2 = Below Average, 1 = Poor.
        </small>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('tenants.feedback.store') }}">
          @csrf

          @php
            $scale = [
              5 => 'Excellent',
              4 => 'Good',
              3 => 'Average',
              2 => 'Below Avg',
              1 => 'Poor',
            ];

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

          @foreach($sections as $section => $questions)
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 fw-semibold">{{ $section }}</h6>
                <span class="text-danger">*</span>
              </div>
              <div class="table-responsive border rounded survey-table-wrapper">
                <table class="table table-borderless align-middle mb-0 survey-table">
                  <thead>
                    <tr>
                      <th class="w-50"></th>
                      @foreach($scale as $value => $label)
                        <th class="text-center">{{ $label }}</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($questions as $field => $label)
                      <tr>
                        <td class="fw-semibold">{{ $label }}</td>
                        @foreach($scale as $value => $text)
                          <td class="text-center">
                            <input
                              type="radio"
                              class="form-check-input"
                              name="{{ $field }}"
                              id="{{ $field }}_{{ $value }}"
                              value="{{ $value }}"
                              {{ old($field) == $value ? 'checked' : '' }}
                              required
                            >
                          </td>
                        @endforeach
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @foreach($questions as $field => $label)
                @error($field)
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                  @break
                @enderror
              @endforeach
            </div>
          @endforeach

          <div class="mb-4">
            <label for="comments" class="form-label">Additional comments or suggestions (optional)</label>
            <textarea
              id="comments"
              name="comments"
              rows="4"
              class="form-control @error('comments') is-invalid @enderror"
              placeholder="Share any details we should know."
            >{{ old('comments') }}</textarea>
            @error('comments')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-send me-1"></i> Submit Survey
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  .survey-table {
    min-width: 480px;
  }
  .survey-table th,
  .survey-table td {
    vertical-align: middle;
    padding: 0.75rem;
  }
  .survey-table input[type="radio"] {
    width: 18px;
    height: 18px;
  }

  @media (max-width: 767.98px) {
    .survey-table-wrapper {
      overflow-x: auto;
    }
    .survey-table {
      width: 100%;
      font-size: 0.85rem;
    }
    .survey-table th:first-child,
    .survey-table td:first-child {
      min-width: 220px;
    }
    .survey-table input[type="radio"] {
      width: 16px;
      height: 16px;
    }
    .card-header small {
      display: block;
    }
  }
</style>
@endpush
@endsection



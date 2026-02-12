@extends('layouts.tenant_app')

@section('title', 'Submission of Requirements')

@push('styles')
<style>
  .page-title-green {
    color: #7F9267 !important;
  }
  .accordion-button {
    background-color: #6B7A56 !important;
    color: white !important;
  }
  .accordion-button:not(.collapsed) {
    background-color: #6B7A56 !important;
    color: white !important;
  }
  .accordion-button:hover {
    background-color: #6B7A56 !important;
    color: white !important;
  }
  .accordion-button:focus {
    background-color: #6B7A56 !important;
    color: white !important;
    box-shadow: 0 0 0 0.25rem rgba(107, 122, 86, 0.25);
  }
  .accordion-button::after {
    filter: brightness(0) invert(1) !important;
  }
  .accordion-button:not(.collapsed)::after {
    filter: brightness(0) invert(1) !important;
  }
  .accordion-item {
    border: none !important;
    background-color: #EFEFEA !important;
  }
  .accordion-header {
    padding-bottom: 0.25rem !important;
    background-color: transparent !important;
  }
  .accordion-body {
    background-color: #EFEFEA !important;
    padding: 0.5rem !important;
  }
  .accordion-collapse {
    background-color: #EFEFEA !important;
    margin-top: 1rem !important;
  }
  .accordion {
    background-color: #EFEFEA !important;
  }
  .card.mb-4 {
    background-color: #EFEFEA !important;
    border: none !important;
  }
  .submission-card {
    min-height: 90vh !important;
  }
  .submission-card .card-body {
    padding: 2rem !important;
  }
</style>
@endpush

@section('page-title')
@endsection

@section('content')
<div class="card submission-card">
  <div class="card-body">
      <!-- Header with Back Button and Title (back goes to marketplace map when from=marketplace, or My Applications when from=stalls) -->
      @php
        $tenantFrom = request('from', 'marketplace');
        $tenantBackUrl = $tenantFrom === 'stalls' ? route('tenants.stalls.index') : route('tenants.marketplace.index');
        $tenantBackLabel = $tenantFrom === 'stalls' ? 'Back to My Applications' : 'Back to Marketplace Map';
      @endphp
      <div class="d-flex align-items-center justify-content-between mb-4" style="margin-top: -1rem !important; position: relative;">
        <a href="{{ $tenantBackUrl }}" class="btn btn-label-primary">
          <i class="bx bx-arrow-back me-1"></i> {{ $tenantBackLabel }}
        </a>
        <h4 class="mb-0 page-title-green fw-bold position-absolute start-50 translate-middle-x">
          <i class="bx bx-clipboard me-2"></i> Submission of Requirements
        </h4>
        <div style="width: 1px; visibility: hidden;">
          <a href="{{ $tenantBackUrl }}" class="btn btn-label-primary">
            <i class="bx bx-arrow-back me-1"></i> {{ $tenantBackLabel }}
          </a>
        </div>
      </div>

      <form id="applicationForm" action="{{ route('tenants.applications.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="stallID" value="{{ $stall->stallID }}">

        <!-- Card 1: Stall Information Accordion -->
        <div class="card mb-4">
          <div class="accordion" id="accordionStallInfo">
            <div class="accordion-item border-0">
              <h2 class="accordion-header" id="headingStallInfo">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStallInfo" aria-expanded="true" aria-controls="collapseStallInfo">
                  <i class="bx bx-store-alt me-2"></i> Stall Information
                </button>
              </h2>
              <div id="collapseStallInfo" class="accordion-collapse collapse show" aria-labelledby="headingStallInfo" data-bs-parent="#accordionStallInfo">
                <div class="accordion-body">
              <ul class="list-unstyled mb-0">
                <li class="mb-3 d-flex align-items-start">
                  <i class="bx bx-map text-primary me-3 mt-1" style="font-size: 1.25rem;"></i>
                  <div>
                    <div class="fw-semibold text-muted small">Location</div>
                    <div class="fw-semibold">{{ $stall->marketplace->marketplace ?? '-' }}</div>
                  </div>
                </li>
                <li class="mb-3 d-flex align-items-start">
                  <i class="bx bx-hash text-primary me-3 mt-1" style="font-size: 1.25rem;"></i>
                  <div>
                    <div class="fw-semibold text-muted small">Stall Name</div>
                    <div class="fw-semibold">{{ $stall->stallNo ?? '-' }}</div>
                  </div>
                </li>
                <li class="mb-3 d-flex align-items-start">
                  <i class="bx bx-ruler text-primary me-3 mt-1" style="font-size: 1.25rem;"></i>
                  <div>
                    <div class="fw-semibold text-muted small">Size (sq. m.)</div>
                    <div class="fw-semibold">{{ $stall->size ?? '-' }}</div>
                  </div>
                </li>
                <li class="mb-3 d-flex align-items-start">
                  <i class="bx bx-money text-primary me-3 mt-1" style="font-size: 1.25rem;"></i>
                  <div>
                    <div class="fw-semibold text-muted small">Monthly Rental Fee</div>
                    <div class="fw-semibold">{{ $stall->rentalFee ? '₱' . number_format($stall->rentalFee, 2) : '-' }}</div>
                  </div>
                </li>
                <li class="mb-0 d-flex align-items-start">
                  <i class="bx bx-calendar text-primary me-3 mt-1" style="font-size: 1.25rem;"></i>
                  <div>
                    <div class="fw-semibold text-muted small">Application Deadline</div>
                    <div class="fw-semibold">{{ $stall->applicationDeadline ? $stall->applicationDeadline->format('F d, Y') : 'No deadline set' }}</div>
                  </div>
                </li>
              </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 2: List of Requirements Accordion -->
        <div class="card mb-4">
          <div class="accordion" id="accordionRequirements">
            <div class="accordion-item border-0">
              <h2 class="accordion-header" id="headingRequirements">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRequirements" aria-expanded="false" aria-controls="collapseRequirements">
                  <i class="bx bx-list-check me-2"></i> List of Requirements
                </button>
              </h2>
              <div id="collapseRequirements" class="accordion-collapse collapse" aria-labelledby="headingRequirements" data-bs-parent="#accordionRequirements">
                <div class="accordion-body">
              <p class="text-muted mb-4">Please prepare and upload the following requirements before submitting your stall application.</p>
              
              <!-- Proposal Requirements -->
              @if($proposalRequirements->count() > 0)
              <div class="mb-4">
                <ul class="list-unstyled mb-0">
                  @foreach($proposalRequirements as $req)
                  <li class="mb-2 d-flex align-items-center">
                    <i class="bx bx-check-circle text-primary me-2"></i>
                    <span class="fw-semibold me-2">{{ $req->requirement_name }}</span>
                    @if($req->is_active)
                    <span class="badge bg-label-danger">Required</span>
                    @else
                    <span class="badge bg-label-secondary">Optional</span>
                    @endif
                  </li>
                  @endforeach
                </ul>
              </div>
              @endif

              <!-- Tenancy Requirements (only show if application is approved) -->
              @if($tenancyRequirements->count() > 0)
              <div class="mb-4">
                <h6 class="fw-bold text-primary mb-3">
                  <i class="bx bx-paperclip me-2"></i> Tenancy Requirements
                </h6>
                <ul class="list-unstyled mb-0">
                  @foreach($tenancyRequirements as $req)
                  <li class="mb-2 d-flex align-items-center">
                    <i class="bx bx-check-circle text-primary me-2"></i>
                    <span class="fw-semibold me-2">{{ $req->requirement_name }}</span>
                    @if($req->is_active)
                    <span class="badge bg-label-danger">Required</span>
                    @else
                    <span class="badge bg-label-secondary">Optional</span>
                    @endif
                  </li>
                  @endforeach
                </ul>
              </div>
              @endif

              @if($proposalRequirements->count() === 0 && $tenancyRequirements->count() === 0)
              <div class="alert alert-info mb-0">
                <i class="bx bx-info-circle me-2"></i> No requirements have been set for this application yet.
              </div>
              @endif
            </div>
          </div>
        </div>
          </div>
        </div>

        <!-- Card 3: Upload Section Accordion -->
        <div class="card mb-4">
          <div class="accordion" id="accordionUpload">
            <div class="accordion-item border-0">
              <h2 class="accordion-header" id="headingUpload">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUpload" aria-expanded="false" aria-controls="collapseUpload">
                  <i class="bx bx-upload me-2"></i> Upload Section
                </button>
              </h2>
              <div id="collapseUpload" class="accordion-collapse collapse" aria-labelledby="headingUpload" data-bs-parent="#accordionUpload">
                <div class="accordion-body">
              @if($proposalRequirements->count() > 0)
              <div class="mb-4">
                <div class="row g-3">
                  @foreach($proposalRequirements as $req)
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      {{ $req->requirement_name }}
                      @if($req->is_active)
                      <span class="text-danger">*</span>
                      @endif
                    </label>
                    <input type="file" 
                           name="files[{{ $req->requirement_name }}]" 
                           class="form-control" 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           @if($req->is_active) required @endif>
                    <small class="text-muted">Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)</small>
                  </div>
                  @endforeach
                </div>
              </div>
              @endif

              @if($tenancyRequirements->count() > 0)
              <div class="mb-4">
                <h6 class="fw-bold text-primary mb-3">
                  <i class="bx bx-paperclip me-2"></i> Upload Tenancy Requirements
                </h6>
                <div class="row g-3">
                  @foreach($tenancyRequirements as $req)
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      {{ $req->requirement_name }}
                      @if($req->is_active)
                      <span class="text-danger">*</span>
                      @endif
                    </label>
                    <input type="file" 
                           name="files[{{ $req->requirement_name }}]" 
                           class="form-control" 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           @if($req->is_active) required @endif>
                    <small class="text-muted">Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)</small>
                  </div>
                  @endforeach
                </div>
              </div>
              @endif

              @if($proposalRequirements->count() === 0 && $tenancyRequirements->count() === 0)
              <div class="alert alert-warning mb-0">
                <i class="bx bx-error-circle me-2"></i> No requirements available for upload. Please contact the administrator.
              </div>
              @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-4 d-flex justify-content-end">
          <button type="submit" class="btn btn-primary btn-lg" id="btnSubmitRequirements">
            <i class="bx bx-check me-2"></i> Submit Requirements
          </button>
        </div>
      </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    // Form submission
    $('#applicationForm').on('submit', function(e) {
      e.preventDefault();
      
      const $btn = $('#btnSubmitRequirements');
      $btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-2"></i> Submitting...');

      const formData = new FormData(this);

      $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: { 'Accept': 'application/json' },
        success: function(response) {
          Swal.fire({
            icon:'success',
            title:'Success',
            text: response.message || 'Your application has been submitted successfully!',
            toast:true,
            position:'top',
            showConfirmButton:false,
            showCloseButton:true,
            timer: 2000,
            timerProgressBar:true
          }).then(() => {
            window.location.href = response.redirect || "{{ route('tenants.stalls.index') }}";
          });
        },
        error: function(xhr) {
          $btn.prop('disabled', false).html('<i class="bx bx-check me-2"></i> Submit Requirements');
          
          let errorMsg = 'Failed to submit application. Please try again.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
          } else if (xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = Object.values(xhr.responseJSON.errors).flat();
            errorMsg = Array.isArray(errors) ? errors.join('<br>') : errors;
          }

          Swal.fire({
            icon:'error',
            title:'Error',
            html: errorMsg,
            toast: true,
            position:'top',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 4000,
            timerProgressBar: true
          });
        }
      });
    });
  });
</script>
@endpush


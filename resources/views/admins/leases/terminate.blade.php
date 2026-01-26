@extends('layouts.admin_app')

@section('title', 'Terminate Contract')
@section('page-title', 'Terminate Contract')

@push('styles')
<style>
  .contract-info-card {
    background-color: #EFEFEA;
    border: 1px solid rgba(127, 146, 103, 0.2);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
  }
</style>
@endpush

@section('content')
<div class="row">
  <div class="col-12 col-lg-8 mx-auto">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="bx bx-x me-2"></i>Terminate Contract
        </h5>
      </div>
      <div class="card-body">
        <!-- Contract Information -->
        <div class="contract-info-card">
          <h6 class="mb-3 text-primary">
            <i class="bx bx-file me-2"></i>Contract Information
          </h6>
          <div class="row">
            <div class="col-md-6 mb-2">
              <strong>Contract ID:</strong> {{ $contract->contractID }}
            </div>
            <div class="col-md-6 mb-2">
              <strong>Status:</strong> 
              <span class="badge bg-success">{{ $contract->contractStatus }}</span>
            </div>
            @if($contract->user)
              <div class="col-md-6 mb-2">
                <strong>Tenant:</strong> {{ $contract->user->firstName }} {{ $contract->user->lastName }}
              </div>
            @endif
            @if($contract->stall)
              <div class="col-md-6 mb-2">
                <strong>Stall:</strong> {{ $contract->stall->stallNo }}
              </div>
              @if($contract->stall->marketplace)
                <div class="col-md-6 mb-2">
                  <strong>Marketplace:</strong> {{ $contract->stall->marketplace->marketplace }}
                </div>
              @endif
            @endif
            <div class="col-md-6 mb-2">
              <strong>Start Date:</strong> {{ $contract->startDate->format('M d, Y') }}
            </div>
            <div class="col-md-6 mb-2">
              <strong>End Date:</strong> {{ $contract->endDate ? $contract->endDate->format('M d, Y') : 'N/A' }}
            </div>
          </div>
        </div>

        <!-- Terminate Form -->
        <form id="terminateContractForm" action="{{ route('admins.leases.terminate', $contract->contractID) }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label">Reason for Termination <span class="text-danger">*</span></label>
            <textarea name="reason" id="terminateReason" class="form-control" rows="4" required placeholder="Enter reason for terminating this contract...">{{ old('reason') }}</textarea>
            <small class="text-muted">This will mark the contract as terminated and set the stall status to Vacant.</small>
            @error('reason')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="alert alert-warning">
            <i class="bx bx-error-circle me-2"></i>
            <strong>Warning:</strong> This action cannot be undone. The contract will be marked as terminated and the stall will become vacant.
          </div>

          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('admins.leases.index') }}" class="btn btn-secondary">
              <i class="bx bx-x me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-danger" id="submitBtn">
              <i class="bx bx-x me-1"></i> Terminate Contract
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  // Form submit with confirmation
  $('#terminateContractForm').on('submit', function(e) {
    e.preventDefault();
    
    const reason = $('#terminateReason').val().trim();
    if (!reason) {
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Please provide a reason for termination.',
        animation: false
      });
      return;
    }

    Swal.fire({
      title: 'Terminate Contract?',
      text: 'This action cannot be undone. The contract will be marked as terminated and the stall will become vacant.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, terminate it!',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        const $form = $('#terminateContractForm');
        const $submitBtn = $('#submitBtn');
        $submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Terminating...');

        const formData = {
          reason: reason
        };
        
        $.ajax({
          url: $form.attr('action'),
          method: 'POST',
          data: formData,
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response.message || 'Contract terminated successfully.',
              showConfirmButton: true,
              confirmButtonText: 'OK'
            }).then(() => {
              window.location.href = "{{ route('admins.leases.index') }}";
            });
          },
          error: function(xhr) {
            let errorMsg = 'Failed to terminate contract.';
            if (xhr.responseJSON?.message) {
              errorMsg = xhr.responseJSON.message;
            } else if (xhr.responseJSON?.errors?.reason) {
              errorMsg = xhr.responseJSON.errors.reason[0];
            }
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: errorMsg,
              animation: false
            });
            $submitBtn.prop('disabled', false).html('<i class="bx bx-x me-1"></i> Terminate Contract');
          }
        });
      }
    });
  });
});
</script>
@endpush


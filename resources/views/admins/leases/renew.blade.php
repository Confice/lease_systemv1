@extends('layouts.admin_app')

@section('title', 'Renew Contract')
@section('page-title', 'Renew Contract')

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
          <i class="bx bx-refresh me-2"></i>Renew Contract
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
              @if($contract->stall->rentalFee)
                <div class="col-md-6 mb-2">
                  <strong>Monthly Rent:</strong> ₱{{ number_format($contract->stall->rentalFee, 2) }}
                </div>
              @endif
            @endif
            <div class="col-md-6 mb-2">
              <strong>Start Date:</strong> {{ $contract->startDate->format('M d, Y') }}
            </div>
            <div class="col-md-6 mb-2">
              <strong>Current End Date:</strong> {{ $contract->endDate ? $contract->endDate->format('M d, Y') : 'N/A' }}
            </div>
          </div>
        </div>

        <!-- Renew Form -->
        <form id="renewContractForm" action="{{ route('admins.leases.renew', $contract->contractID) }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label">Renew for (months) <span class="text-danger">*</span></label>
            <select name="months" id="renewMonths" class="form-select" required>
              <option value="1">1 Month</option>
              <option value="2">2 Months</option>
              <option value="3">3 Months</option>
              <option value="6">6 Months</option>
              <option value="12">12 Months</option>
            </select>
            <small class="text-muted">Monthly bills will be generated for the renewal period.</small>
            @error('months')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="alert alert-info">
            <i class="bx bx-info-circle me-2"></i>
            <small>Renewing the contract will extend the end date and generate monthly bills for the renewal period.</small>
          </div>

          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('admins.leases.index') }}" class="btn btn-secondary">
              <i class="bx bx-x me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
              <i class="bx bx-check me-1"></i> Renew Contract
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

  // Form submit
  $('#renewContractForm').on('submit', function(e) {
    e.preventDefault();
    
    const $submitBtn = $('#submitBtn');
    $submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Renewing...');

    const formData = {
      months: $('#renewMonths').val()
    };
    
    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: formData,
      success: function(response) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: response.message || 'Contract renewed successfully.',
          showConfirmButton: true,
          confirmButtonText: 'OK'
        }).then(() => {
          window.location.href = "{{ route('admins.leases.index') }}";
        });
      },
      error: function(xhr) {
        let errorMsg = 'Failed to renew contract.';
        if (xhr.responseJSON?.message) {
          errorMsg = xhr.responseJSON.message;
        } else if (xhr.responseJSON?.errors?.months) {
          errorMsg = xhr.responseJSON.errors.months[0];
        }
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: errorMsg,
          animation: false
        });
        $submitBtn.prop('disabled', false).html('<i class="bx bx-check me-1"></i> Renew Contract');
      }
    });
  });
});
</script>
@endpush


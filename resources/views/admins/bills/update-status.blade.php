@extends('layouts.admin_app')

@section('title', 'Update Bill Status')
@section('page-title', 'Update Bill Status')

@push('styles')
<style>
  .bill-info-card {
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
          <i class="bx bx-edit me-2"></i>Update Bill Status
        </h5>
      </div>
      <div class="card-body">
        <!-- Bill Information -->
        <div class="bill-info-card">
          <h6 class="mb-3 text-primary">
            <i class="bx bx-receipt me-2"></i>Bill Information
          </h6>
          <div class="row">
            <div class="col-md-6 mb-2">
              <strong>Bill ID:</strong> {{ $bill->billID }}
            </div>
            <div class="col-md-6 mb-2">
              <strong>Amount:</strong> ₱{{ number_format($bill->amount, 2) }}
            </div>
            <div class="col-md-6 mb-2">
              <strong>Due Date:</strong> {{ $bill->dueDate->format('M d, Y') }}
            </div>
            <div class="col-md-6 mb-2">
              <strong>Current Status:</strong> 
              <span class="badge 
                @if($bill->status === 'Paid') bg-success
                @elseif($bill->status === 'Due') bg-danger
                @elseif($bill->status === 'Pending') bg-warning
                @else bg-secondary
                @endif">
                {{ $bill->status }}
              </span>
            </div>
            @if($bill->contract && $bill->contract->user)
              <div class="col-md-6 mb-2">
                <strong>Tenant:</strong> {{ $bill->contract->user->firstName }} {{ $bill->contract->user->lastName }}
              </div>
            @endif
            @if($bill->stall)
              <div class="col-md-6 mb-2">
                <strong>Stall:</strong> {{ $bill->stall->stallNo }}
              </div>
              @if($bill->stall->marketplace)
                <div class="col-md-6 mb-2">
                  <strong>Marketplace:</strong> {{ $bill->stall->marketplace->marketplace }}
                </div>
              @endif
            @endif
            @if($bill->datePaid)
              <div class="col-md-6 mb-2">
                <strong>Date Paid:</strong> {{ $bill->datePaid->format('M d, Y h:i A') }}
              </div>
            @endif
          </div>
        </div>

        <!-- Update Status Form -->
        <form id="updateStatusForm" action="{{ route('admins.bills.update-status', $bill->billID) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="statusSelect" class="form-select" required>
              <option value="Pending" {{ $bill->status === 'Pending' ? 'selected' : '' }}>Pending</option>
              <option value="Paid" {{ $bill->status === 'Paid' ? 'selected' : '' }}>Paid</option>
              <option value="Due" {{ $bill->status === 'Due' ? 'selected' : '' }}>Due</option>
              <option value="Invalid" {{ $bill->status === 'Invalid' ? 'selected' : '' }}>Invalid</option>
            </select>
            @error('status')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="alert alert-info">
            <i class="bx bx-info-circle me-2"></i>
            <small>Updating the bill status will reflect immediately in the system.</small>
          </div>

          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('admins.bills.index') }}" class="btn btn-secondary">
              <i class="bx bx-x me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
              <i class="bx bx-check me-1"></i> Update Status
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
  $('#updateStatusForm').on('submit', function(e) {
    e.preventDefault();
    
    const $submitBtn = $('#submitBtn');
    $submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Updating...');

    const formData = {
      status: $('#statusSelect').val()
    };
    
    $.ajax({
      url: $(this).attr('action'),
      method: 'PUT',
      data: formData,
      success: function(response) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: response.message || 'Bill status updated successfully.',
          showConfirmButton: true,
          confirmButtonText: 'OK'
        }).then(() => {
          window.location.href = "{{ route('admins.bills.index') }}";
        });
      },
      error: function(xhr) {
        let errorMsg = 'Failed to update bill status.';
        if (xhr.responseJSON?.message) {
          errorMsg = xhr.responseJSON.message;
        } else if (xhr.responseJSON?.errors?.status) {
          errorMsg = xhr.responseJSON.errors.status[0];
        }
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: errorMsg,
          animation: false
        });
        $submitBtn.prop('disabled', false).html('<i class="bx bx-check me-1"></i> Update Status');
      }
    });
  });
});
</script>
@endpush


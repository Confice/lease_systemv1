@extends('layouts.tenant_app')

@section('title', 'Upload Payment Proof')
@section('page-title', 'Upload Payment Proof')

@push('styles')
<style>
  .upload-area {
    border: 2px dashed #7F9267;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    background-color: #EFEFEA;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .upload-area:hover {
    background-color: rgba(127, 146, 103, 0.1);
    border-color: #6B7A56;
  }
  
  .upload-area.dragover {
    background-color: rgba(127, 146, 103, 0.2);
    border-color: #6B7A56;
  }
  
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
          <i class="bx bx-upload me-2"></i>Upload Payment Proof
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
              <strong>Status:</strong> 
              <span class="badge 
                @if($bill->status === 'Paid') bg-success
                @elseif($bill->status === 'Due') bg-danger
                @elseif($bill->status === 'Pending') bg-warning
                @else bg-secondary
                @endif">
                {{ $bill->status }}
              </span>
            </div>
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
          </div>
        </div>

        <!-- Upload Form -->
        <form id="uploadProofForm" enctype="multipart/form-data" action="{{ route('tenants.bills.upload-proof', $bill->billID) }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label">Payment Proof <span class="text-danger">*</span></label>
            <div class="upload-area" id="uploadArea">
              <i class="bx bx-cloud-upload fs-1 text-muted mb-2"></i>
              <p class="mb-1">Click to upload or drag and drop</p>
              <p class="text-muted small mb-0">PDF, JPG, JPEG, PNG, WEBP (Max 5MB)</p>
            </div>
            <input type="file" id="paymentProofFile" name="paymentProof" accept=".pdf,.jpg,.jpeg,.png,.webp" class="d-none" required>
            <div id="fileName" class="mt-2 text-muted small"></div>
            <div class="invalid-feedback d-block" id="fileError"></div>
            @error('paymentProof')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="alert alert-info">
            <i class="bx bx-info-circle me-2"></i>
            <small>Upload a clear photo or PDF of your payment receipt. The admin will verify your payment.</small>
          </div>

          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('tenants.bills.index') }}" class="btn btn-secondary">
              <i class="bx bx-x me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
              <i class="bx bx-upload me-1"></i> Upload Payment Proof
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

  // Upload area click
  $('#uploadArea').on('click', function() {
    $('#paymentProofFile').click();
  });

  // File input change
  $('#paymentProofFile').on('change', function() {
    const file = this.files[0];
    if (file) {
      $('#fileName').text('Selected: ' + file.name);
      $('#fileError').text('');
      
      // Validate file size (5MB)
      if (file.size > 5 * 1024 * 1024) {
        $('#fileError').text('File size must be less than 5MB.');
        $(this).val('');
        $('#fileName').text('');
        return;
      }
    }
  });

  // Drag and drop
  $('#uploadArea').on('dragover', function(e) {
    e.preventDefault();
    $(this).addClass('dragover');
  });

  $('#uploadArea').on('dragleave', function() {
    $(this).removeClass('dragover');
  });

  $('#uploadArea').on('drop', function(e) {
    e.preventDefault();
    $(this).removeClass('dragover');
    const files = e.originalEvent.dataTransfer.files;
    if (files.length > 0) {
      $('#paymentProofFile')[0].files = files;
      $('#paymentProofFile').trigger('change');
    }
  });

  // Form submit
  $('#uploadProofForm').on('submit', function(e) {
    e.preventDefault();
    
    const fileInput = $('#paymentProofFile')[0];
    if (!fileInput.files || !fileInput.files[0]) {
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Please select a file to upload.',
        animation: false
      });
      return;
    }

    // Validate file size
    if (fileInput.files[0].size > 5 * 1024 * 1024) {
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'File size must be less than 5MB.',
        animation: false
      });
      return;
    }

    // Disable submit button
    const $submitBtn = $('#submitBtn');
    $submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Uploading...');

    const formData = new FormData(this);
    
    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: { 'Accept': 'application/json' },
      success: function(response) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: response.message || 'Payment proof uploaded successfully.',
          showConfirmButton: true,
          confirmButtonText: 'OK'
        }).then(() => {
          window.location.href = "{{ route('tenants.bills.index') }}";
        });
      },
      error: function(xhr) {
        let errorMsg = 'Failed to upload payment proof.';
        if (xhr.responseJSON?.message) {
          errorMsg = xhr.responseJSON.message;
        } else if (xhr.responseJSON?.errors?.paymentProof) {
          errorMsg = xhr.responseJSON.errors.paymentProof[0];
        }
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: errorMsg,
          animation: false
        });
        $submitBtn.prop('disabled', false).html('<i class="bx bx-upload me-1"></i> Upload Payment Proof');
      }
    });
  });
});
</script>
@endpush


@extends('layouts.tenant_app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Change password</h5>
  </div>
  <div class="card-body">
    <p class="text-muted mb-3">To change your password, we'll send a secure link to your email. Click the link and set a new password (same flow as forgot password).</p>
    <div id="passwordChangeAlert" class="alert d-none mb-3" role="alert"></div>
    <button type="button" class="btn btn-primary" id="sendPasswordChangeBtn">
      <i class="bx bx-envelope me-1"></i> Email me a link to change my password
    </button>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
  $('#sendPasswordChangeBtn').on('click', function() {
    var btn = $(this);
    var alertEl = $('#passwordChangeAlert');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Sending…');
    alertEl.addClass('d-none').removeClass('alert-success alert-danger');
    $.ajax({
      url: "{{ route('password.change.link') }}",
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' },
      success: function(res) {
        alertEl.removeClass('alert-danger').addClass('alert-success').text(res.message || 'Check your email.').removeClass('d-none');
      },
      error: function(xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong. Please try again.';
        alertEl.removeClass('alert-success').addClass('alert-danger').text(msg).removeClass('d-none');
      },
      complete: function() {
        btn.prop('disabled', false).html('<i class="bx bx-envelope me-1"></i> Email me a link to change my password');
      }
    });
  });
});
</script>
@endpush

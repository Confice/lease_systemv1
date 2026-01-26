@extends('layouts.auth')

@section('title','Reset Password')

@section('content')
<div class="container-xxl d-flex align-items-center justify-content-center min-vh-100">
  <div class="authentication-wrapper authentication-basic w-100">
    <div class="authentication-inner row">

      <!-- Left Illustration -->
      <div class="d-none d-lg-flex col-lg-6 align-items-center justify-content-center">
        <img src="{{ asset('sneat/assets/img/illustrations/setup_password.svg') }}" 
             class="img-fluid" 
             alt="Reset password illustration">
      </div>

      <!-- Reset Password Form -->
      <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="card p-4 shadow-sm rounded-3">
          <h4 class="mb-3 text-center">🔁 Create a New Password</h4>
          <p class="mb-4 text-center">Update your credentials and get back on track.</p>

          <form method="POST" action="{{ route('password.update') }}" autocomplete="off" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <!-- Password -->
            <div class="mb-3">
              <label class="form-label">New Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-lock"></i></span>
                <input type="password" 
                       name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       placeholder="••••••••">
                <span class="input-group-text cursor-pointer toggle-password"><i class="bx bx-hide"></i></span>
              </div>
              @error('password')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
              <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-lock"></i></span>
                <input type="password" 
                       name="password_confirmation" 
                       class="form-control" 
                       placeholder="••••••••">
                <span class="input-group-text cursor-pointer toggle-password"><i class="bx bx-hide"></i></span>
              </div>
            </div>

            <!-- Button -->
            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center w-100">
              <i class="bx bx-key me-1"></i> RESET PASSWORD
            </button>
          </form>

          <p class="text-center mt-3">
            <a href="{{ route('login') }}">← Back to login</a>
          </p>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('styles')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
@endpush

@push('scripts')
<script>
$(function(){
  // Toggle password show/hide
  $(document).on('click','.toggle-password',function(){
    let input = $(this).siblings('input');
    let icon = $(this).find('i');
    input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    icon.toggleClass('bx-hide bx-show');
  });

  // Remove red border & error when user edits input
  $(document).on('input change', '.form-control', function() {
    $(this).removeClass('is-invalid');
    $('[data-error="'+$(this).attr('name')+'"]').text('');
  });
});
</script>
@endpush

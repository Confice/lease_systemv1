@extends('layouts.auth')

@section('title','Set Your Password')

@section('content')
<div class="container-xxl d-flex align-items-center justify-content-center min-vh-100">
  <div class="authentication-wrapper authentication-basic w-100">
    <div class="authentication-inner row">
      
      <!-- Left Illustration -->
      <div class="d-none d-lg-flex col-lg-6 align-items-center justify-content-center">
        <img src="{{ asset('sneat/assets/img/illustrations/setup_password.svg') }}" 
             class="img-fluid" 
             alt="Set password illustration">
      </div>
      
      <!-- Setup Password Form -->
      <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="card p-4 shadow-sm rounded-3">
          <h4 class="mb-3 text-center">🛡️ Secure Your Account</h4>
          <p class="mb-4 text-center">Choose a strong password to keep your data safe.</p>

          <form method="POST" action="{{ route('setup.password.store') }}" autocomplete="off" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <!-- Password -->
            <div class="mb-3">
              <label class="form-label">Password <span class="text-danger">*</span></label>
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
              <i class="bx bx-key me-1"></i> SET PASSWORD
            </button>
          </form>
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
});
</script>
@endpush

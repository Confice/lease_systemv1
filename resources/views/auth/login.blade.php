@extends('layouts.auth')

@section('title','Login')

@section('content')
<div class="container-xxl d-flex align-items-center justify-content-center min-vh-100">
  <div class="authentication-wrapper authentication-basic w-100">
    <div class="authentication-inner row">

      <!-- Left Illustration -->
      <div class="d-none d-lg-flex col-lg-6 align-items-center justify-content-center">
        <img src="{{ asset('sneat/assets/img/illustrations/signin.svg') }}" class="img-fluid" alt="Login illustration">
      </div>

      <!-- Login Form -->
      <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="card p-4 shadow-sm rounded-3">
          <h4 class="mb-3 text-center">🔑 Access Your Account</h4>
          <p class="mb-4 text-center">Your personal dashboard is only a click away.</p>

          <form id="loginForm" action="{{ route('login.store') }}" method="POST" autocomplete="off" novalidate>
            @csrf

            <!-- Email -->
            <div class="mb-3">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="example@email.com">
              </div>
              <div class="invalid-feedback d-block" data-error="email"></div>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label class="form-label">Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••">
                <span class="input-group-text cursor-pointer toggle-password"><i class="bx bx-hide"></i></span>
              </div>
              <div class="invalid-feedback d-block" data-error="password"></div>
                
                <!-- Forgot password link -->
                <div class="text-end mt-1">
                  <a href="{{ route('password.request') }}">Forgot Password?</a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center w-100">
              <i class="bx bx-log-in me-1"></i> SIGN IN
            </button>
          </form>

          <p class="text-center mt-3">
            <span>Don’t have an account yet?</span>
            <a href="{{ route('register') }}">Sign up today.</a>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  let submitting = false;

  // Toggle password show/hide
  $(document).on('click','.toggle-password',function(){
    let input = $(this).siblings('input');
    let icon = $(this).find('i');
    input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    icon.toggleClass('bx-hide bx-show');
  });

  // Handle login via AJAX
  $('#loginForm').on('submit', function(e){
    e.preventDefault();
    if(submitting) return;
    submitting = true;

    $('[data-error]').text('');
    $('.form-control').removeClass('is-invalid');

    $.ajax({
      url: $(this).attr('action'),
      method: "POST",
      data: $(this).serialize(),
    })
    .done(function(res){
        submitting = false;
        if(res.success){
          Swal.fire({
            icon:'success',
            title:'Success',
            text:'Login successful!',
            toast:true,
            position:'top',
            showConfirmButton:false,
            showCloseButton:true,
            timer: 2000,
            timerProgressBar:true
          }).then(()=>window.location.href=res.redirect);
        }
    })
    .fail(function(xhr){
        submitting = false;
        if(xhr.status === 422 && xhr.responseJSON.errors){
            $.each(xhr.responseJSON.errors, function(field, messages){
                $('[data-error="'+field+'"]').text(messages[0]);
                $('[name="'+field+'"]').addClass('is-invalid');
            });
            return;
        }
        Swal.fire({
            icon:'error',
            title:'Login failed',
            text: xhr.responseJSON?.message || 'Something went wrong.',
            toast:true,
            position:'top',
            showConfirmButton:false,
            showCloseButton:true,
            timer: 2000,
            timerProgressBar:true
        });
    });
  });

  // Remove red border & error when user edits input
  $(document).on('input change', '.form-control', function() {
    $(this).removeClass('is-invalid');
    $('[data-error="'+$(this).attr('name')+'"]').text('');
  });

});
</script>
@endpush

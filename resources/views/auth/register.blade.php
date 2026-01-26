@extends('layouts.auth')

@section('title','Register')

@section('content')
<div class="container-xxl d-flex align-items-center justify-content-center min-vh-100">
  <div class="authentication-wrapper authentication-basic w-100">
    <div class="authentication-inner row">
      
      <!-- Left Illustration -->
      <div class="d-none d-lg-flex col-lg-6 align-items-center justify-content-center">
        <img src="{{ asset('sneat/assets/img/illustrations/signup.svg') }}" class="img-fluid" alt="Register illustration">
      </div>
      
      <!-- Register Form -->
      <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="card p-4 shadow-sm rounded-3">
          <h4 class="mb-3 text-center">✨ Get Started Today</h4>
          <p class="mb-4 text-center">Join us in just a few easy steps.</p>

          <form id="registerForm" action="{{ route('register.store') }}" method="POST" autocomplete="off" novalidate>
            @csrf

            <!-- First Name -->
            <div class="mb-3">
              <label class="form-label">First Name <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-user"></i></span>
                <input type="text" name="firstName" class="form-control" placeholder="Enter first name">
              </div>
              <div class="invalid-feedback d-block" data-error="firstName"></div>
            </div>

            <!-- Middle Name -->
            <div class="mb-3">
              <label class="form-label">Middle Name</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-user"></i></span>
                <input type="text" name="middleName" class="form-control" placeholder="Enter middle name">
              </div>
              <div class="invalid-feedback d-block" data-error="middleName"></div>
            </div>

            <!-- Last Name -->
            <div class="mb-3">
              <label class="form-label">Last Name <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-user"></i></span>
                <input type="text" name="lastName" class="form-control" placeholder="Enter last name">
              </div>
              <div class="invalid-feedback d-block" data-error="lastName"></div>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="example@email.com">
              </div>
              <div class="invalid-feedback d-block" data-error="email"></div>
            </div>

            <!-- Birth Date -->
            <div class="mb-3">
              <label class="form-label">Birthdate <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                <input type="date" name="birthDate" class="form-control">
              </div>
              <div class="invalid-feedback d-block" data-error="birthDate"></div>
            </div>

            <!-- Contact No -->
            <div class="mb-3">
              <label class="form-label">Contact Number <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-phone"></i></span>
                <input type="text" name="contactNo" class="form-control" placeholder="09XXXXXXXXX">
              </div>
              <div class="invalid-feedback d-block" data-error="contactNo"></div>
            </div>

            <!-- Home Address -->
            <div class="mb-3">
              <label class="form-label">Home Address <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-home"></i></span>
                <textarea name="homeAddress" class="form-control" rows="2" placeholder="Enter home address"></textarea>
              </div>
              <div class="invalid-feedback d-block" data-error="homeAddress"></div>
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
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
              <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bx bx-lock"></i></span>
                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                <span class="input-group-text cursor-pointer toggle-password"><i class="bx bx-hide"></i></span>
              </div>
              <div class="invalid-feedback d-block" data-error="password_confirmation"></div>
            </div>
            
            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center w-100">
              <i class="bx bx-user-plus me-1"></i> SIGN UP
            </button>
          </form>

          <p class="text-center mt-3">
            <span>Have an account?</span>
            <a href="{{ route('login') }}">Log in to continue.</a>
          </p>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('styles')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){
  let submitting = false;

  // Toggle password show/hide
  $(document).on('click','.toggle-password',function(){
    let input = $(this).siblings('input');
    let icon = $(this).find('i');
    input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    icon.toggleClass('bx-hide bx-show');
  });

  // Handle register submit (AJAX with inline validation)
  $('#registerForm').on('submit', function(e){
    e.preventDefault();
    if(submitting) return;
    submitting = true;

    $('[data-error]').text('');
    $('.form-control').removeClass('is-invalid');

    $.post($(this).attr('action'), $(this).serialize())
      .done(function(res){
        Swal.fire({
          icon:'success',
          title:'Success',
          text: res.message,
          toast:true,
          position:'top',
          showConfirmButton:false,
          showCloseButton:true,
          timer: 2000,
          timerProgressBar:true
        }).then(() => window.location.href = "{{ route('login') }}");
      })
      .fail(function(xhr){
        if(xhr.status === 422){
          $.each(xhr.responseJSON.errors, function(field, messages){
            $('[data-error="'+field+'"]').text(messages[0]);
            $('[name="'+field+'"]').addClass('is-invalid');
          });
        } else {
          Swal.fire({
            icon:'error',
            title:'Error',
            text:'Something went wrong.',
            toast:true,
            position:'top',
            showConfirmButton:false,
            showCloseButton:true,
            timer: 2000,
            timerProgressBar:true
          });
        }
      })
      .always(function(){ submitting = false; });
  });

  // Remove red border & error when user edits input
  $(document).on('input change', '.form-control', function() {
    $(this).removeClass('is-invalid');
    $('[data-error="'+$(this).attr('name')+'"]').text('');
  });
});
</script>
@endpush
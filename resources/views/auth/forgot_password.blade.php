q@extends('layouts.auth')

@section('title','Forgot Password')

@section('content')
<div class="container-xxl d-flex align-items-center justify-content-center min-vh-100">
  <div class="authentication-wrapper authentication-basic w-100">
    <div class="authentication-inner row">

      <!-- Left Illustration -->
      <div class="d-none d-lg-flex col-lg-6 align-items-center justify-content-center">
        <img src="{{ asset('sneat/assets/img/illustrations/forgot_password.svg') }}" 
             class="img-fluid" 
             alt="Forgot password illustration">
      </div>

      <!-- Forgot Password Form -->
      <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="card p-4 shadow-sm rounded-3">
          <h4 class="mb-3 text-center">💭 Can’t Remember Your Password?</h4>
          <p class="mb-4 text-center">No worries — we’ll send you quick recovery instructions.</p>

          <form id="forgotForm" action="{{ route('password.email') }}" method="POST" autocomplete="off" novalidate>
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

            <!-- Button -->
            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center w-100">
              <i class="bx bx-mail-send me-1"></i> SEND RESET LINK
            </button>
          </form>

          <p class="text-center mt-3">
            <a href="{{ route('login') }}" 
              class="btn btn-label-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 py-2">
              <i class="bx bx-log-in-circle fs-5"></i>
              Back to Login
            </a>
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
  let submitting = false;

  // Handle forgot password submit (AJAX with inline validation)
  $('#forgotForm').on('submit', function(e){
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

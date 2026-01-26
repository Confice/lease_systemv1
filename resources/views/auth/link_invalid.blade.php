@extends('layouts.auth')

@section('title', 'Link Invalid or Expired')

@section('content')
<div class="container-xxl d-flex align-items-center justify-content-center min-vh-100">
  <div class="card p-5 shadow-lg rounded-4 text-center" style="max-width: 720px;" data-email="{{ isset($email) && !empty($email) ? $email : '' }}"> 
    <h3 class="mb-3 text-danger fw-bold">Oops!</h3>
    <p class="mb-4 text-muted fs-5">{{ $message ?? '' }}</p>

    @if(isset($email) && !empty($email) && isset($resendRoute) && !empty($resendRoute))
    <form id="resendLinkForm" method="POST" action="{{ $resendRoute }}" class="w-100 mb-3">
      @csrf
      <input type="hidden" name="email" value="{{ $email }}">
      <button type="submit" id="requestNewLinkBtn" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold py-2">
        <i class="bx bx-link-external fs-5"></i>
        <span id="requestNewLinkText">Request New Link</span>
      </button>
      <div id="timerContainer" class="mt-2" style="display: none;">
        <small class="text-muted">Please wait <span id="timerDisplay">150</span> seconds before requesting again</small>
      </div>
    </form>
    @else
    <a href="{{ route('password.request') }}" 
       id="requestNewLinkBtn" 
       class="btn btn-primary w-100 mb-3 d-flex align-items-center justify-content-center gap-2 fw-semibold py-2">
      <i class="bx bx-link-external fs-5"></i>
      <span id="requestNewLinkText">Request New Link</span>
    </a>
    <div id="timerContainer" class="mt-2" style="display: none;">
      <small class="text-muted">Please wait <span id="timerDisplay">150</span> seconds before requesting again</small>
    </div>
    @endif

    @if(!isset($hideBack) || empty($hideBack) || !$hideBack)
    <a href="{{ route('login') }}" 
       class="btn btn-label-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 py-2">
      <i class="bx bx-log-in-circle fs-5"></i>
      Back to Login
    </a>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){
  let timerInterval = null;
  let timeLeft = 0;
  
  // Check if there's a timer in localStorage
  const emailValue = $('.card').data('email') || 'default';
  const timerKey = 'resendLinkTimer_' + emailValue;
  const savedTime = localStorage.getItem(timerKey);
  if (savedTime) {
    const elapsed = Math.floor((Date.now() - parseInt(savedTime)) / 1000);
    if (elapsed < 150) {
      timeLeft = 150 - elapsed;
      startTimer();
    } else {
      localStorage.removeItem(timerKey);
    }
  }

  function startTimer() {
    const $btn = $('#requestNewLinkBtn');
    const $text = $('#requestNewLinkText');
    const $timerContainer = $('#timerContainer');
    const $timerDisplay = $('#timerDisplay');
    
    $btn.prop('disabled', true).addClass('disabled').css('opacity', '0.6');
    $timerContainer.show();
    
    timerInterval = setInterval(function() {
      $timerDisplay.text(timeLeft);
      timeLeft--;
      
      if (timeLeft < 0) {
        clearInterval(timerInterval);
        $btn.prop('disabled', false).removeClass('disabled').css('opacity', '1');
        $timerContainer.hide();
        localStorage.removeItem(timerKey);
      }
    }, 1000);
  }

  // Handle form submission
  $('#resendLinkForm').on('submit', function(e) {
    e.preventDefault();
    
    if ($('#requestNewLinkBtn').prop('disabled')) {
      return;
    }
    
    const form = $(this);
    const formData = form.serialize();
    const action = form.attr('action');
    
    // Start timer immediately on click
    localStorage.setItem(timerKey, Date.now().toString());
    timeLeft = 150;
    startTimer();
    
    // Change header text immediately
    $('h3').text('Success!').removeClass('text-danger').addClass('text-success');
    $('p').text('We\'ve sent you an email with a new link. Please check your inbox.').removeClass('text-muted');
    
    $.ajax({
      url: action,
      method: 'POST',
      data: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
      .done(function(res) {
        // Success - timer and text already updated
        if (res && res.message) {
          $('p').text(res.message);
        }
      })
      .fail(function(xhr) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.',
          toast: true,
          position: 'top',
          showConfirmButton: false,
          showCloseButton: true,
          timer: 3000,
          timerProgressBar: true
        });
        // Reset timer on error
        clearInterval(timerInterval);
        localStorage.removeItem(timerKey);
        $('#requestNewLinkBtn').prop('disabled', false).removeClass('disabled').css('opacity', '1');
        $('#timerContainer').hide();
        $('h3').text('Oops!').removeClass('text-success').addClass('text-danger');
        $('p').text({!! json_encode($message ?? '') !!}).addClass('text-muted');
      });
  });
  
  // Handle link click (for password.request route) - this will navigate to forgot password page
  // Timer will be handled on that page if needed
  $('#requestNewLinkBtn[href]').on('click', function(e) {
    if ($(this).prop('disabled')) {
      e.preventDefault();
      return false;
    }
    // Allow navigation to forgot password page
  });
});
</script>
@endpush

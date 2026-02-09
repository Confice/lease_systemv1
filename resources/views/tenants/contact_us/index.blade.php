@extends('layouts.tenant_app')

@section('title', 'Contact Us')
@section('page-title', 'Contact Us')

@push('styles')
<style>
  .contact-card {
    border: 2px solid #7F9267;
    border-radius: 12px;
    transition: all 0.3s ease;
  }
  
  .contact-card:hover {
    box-shadow: 0 4px 12px rgba(127, 146, 103, 0.15);
  }
  
  .contact-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: #7F9267;
    color: #FFFFFF;
    font-size: 24px;
    margin-bottom: 1rem;
  }
  
  .contact-info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(127, 146, 103, 0.1);
  }
  
  .contact-info-item:last-child {
    border-bottom: none;
  }
  
  .contact-info-item i {
    color: #7F9267;
    font-size: 20px;
    width: 24px;
  }
  
  .map-container {
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #7F9267;
    height: 450px;
  }
  
  .map-container iframe {
    width: 100%;
    height: 100%;
    border: none;
  }
</style>
@endpush

@section('content')
<div class="row g-4">
  <!-- Contact Information Card -->
  <div class="col-lg-5">
    <div class="card contact-card h-100">
      <div class="card-body p-4">
        <div class="text-center mb-4">
          <div class="contact-icon mx-auto">
            <i class="bx bx-phone"></i>
          </div>
          <h4 class="fw-bold mb-2">Get in Touch</h4>
          <p class="text-muted mb-0">We're here to help! Reach out to us through any of the following channels.</p>
        </div>
        
        <div class="contact-info-list mt-4">
          <!-- Phone Number -->
          <div class="contact-info-item">
            <i class="bx bx-phone"></i>
            <div>
              <strong>Phone</strong>
              <div class="text-muted">+63 9XX XXX XXXX</div>
            </div>
          </div>
          
          <!-- Facebook -->
          <div class="contact-info-item">
            <i class="bx bxl-facebook"></i>
            <div>
              <strong>Facebook</strong>
              <div class="text-muted">@YourFacebookPageName</div>
            </div>
          </div>
          
          <!-- Email (if you want to add it later) -->
          <div class="contact-info-item">
            <i class="bx bx-envelope"></i>
            <div>
              <strong>Email</strong>
              <div class="text-muted">support@leaseease.com</div>
            </div>
          </div>
          
          <!-- Business Hours -->
          <div class="contact-info-item">
            <i class="bx bx-time"></i>
            <div>
              <strong>Business Hours</strong>
              <div class="text-muted">Monday - Friday: 9:00 AM - 6:00 PM</div>
              <div class="text-muted">Saturday: 9:00 AM - 1:00 PM</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Google Maps Card -->
  <div class="col-lg-7">
    <div class="card contact-card h-100">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">
          <i class="bx bx-map me-2"></i>Our Location
        </h5>
        <p class="text-muted mb-3">Visit us at our office location:</p>
        
        <div class="map-container">
          <!-- Google Maps Embed -->
          <!-- Replace the src URL with your actual location coordinates or place ID -->
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.5!2d121.0!3d14.6!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTTCsDM2JzAwLjAiTiAxMjHCsDAwJzAwLjAiRQ!5e0!3m2!1sen!2sph!4v1234567890123!5m2!1sen!2sph"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="LeaseEase Office Location">
          </iframe>
        </div>
        
        <div class="mt-3">
          <p class="mb-1"><strong>Address:</strong></p>
          <p class="text-muted mb-0">123 Business Street, Makati City, Metro Manila, Philippines 1234</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Send a message form -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card contact-card">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">
          <i class="bx bx-send me-2"></i>Send us a message
        </h5>
        <p class="text-muted mb-3">Send your question or feedback and we'll get back to you.</p>
        <div id="tenantContactFormAlert" class="alert d-none mb-3" role="alert"></div>
        <form id="tenantContactForm" action="{{ route('landing.contact.submit') }}" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" value="{{ auth()->user() ? trim((auth()->user()->firstName ?? '') . ' ' . (auth()->user()->lastName ?? '')) : '' }}" required>
              <div class="invalid-feedback d-block" data-error="name"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" value="{{ auth()->user()->email ?? '' }}" required>
              <div class="invalid-feedback d-block" data-error="email"></div>
            </div>
            <div class="col-12">
              <label class="form-label">Message <span class="text-danger">*</span></label>
              <textarea name="message" class="form-control" rows="4" placeholder="Your message or question..." required></textarea>
              <div class="invalid-feedback d-block" data-error="message"></div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary" id="tenantContactSubmitBtn">
                <i class="bx bx-send me-1"></i> Send message
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Additional Information Section -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card contact-card">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">
          <i class="bx bx-info-circle me-2"></i>Need Help?
        </h5>
        <p class="text-muted mb-3">
          If you have questions about your lease, billing, or need assistance with the system, 
          please don't hesitate to contact us. Our team is ready to assist you during business hours.
        </p>
        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('tenants.feedback.index') }}" class="btn btn-primary">
            <i class="bx bx-message-dots me-1"></i> Submit Feedback
          </a>
          <a href="{{ route('tenants.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bx bx-home me-1"></i> Back to Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
  var form = $('#tenantContactForm');
  if (!form.length) return;
  var alertEl = $('#tenantContactFormAlert');
  var submitBtn = $('#tenantContactSubmitBtn');
  var sending = false;
  form.on('submit', function(e) {
    e.preventDefault();
    if (sending) return;
    sending = true;
    alertEl.addClass('d-none').removeClass('alert-success alert-danger');
    form.find('[data-error]').text('');
    form.find('.form-control.is-invalid').removeClass('is-invalid');
    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Sending…');
    $.ajax({
      url: form.attr('action'),
      method: 'POST',
      data: form.serialize(),
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      success: function(res) {
        if (res.success) {
          alertEl.removeClass('alert-danger').addClass('alert-success').text(res.message || 'Thank you! Your message has been sent.').removeClass('d-none');
          form[0].reset();
        } else {
          alertEl.removeClass('alert-success').addClass('alert-danger').text(res.message || 'Something went wrong.').removeClass('d-none');
          if (res.errors) {
            $.each(res.errors, function(field, msgs) {
              var input = form.find('[name="' + field + '"]');
              var errEl = form.find('[data-error="' + field + '"]');
              if (input.length) input.addClass('is-invalid');
              if (errEl.length && msgs && msgs[0]) errEl.text(msgs[0]);
            });
          }
        }
      },
      error: function(xhr) {
        var res = xhr.responseJSON || {};
        var msg = res.message || 'Something went wrong. Please try again.';
        alertEl.removeClass('alert-success').addClass('alert-danger').text(msg).removeClass('d-none');
        if (res.errors) {
          $.each(res.errors, function(field, msgs) {
            var input = form.find('[name="' + field + '"]');
            var errEl = form.find('[data-error="' + field + '"]');
            if (input.length) input.addClass('is-invalid');
            if (errEl.length && msgs && msgs[0]) errEl.text(msgs[0]);
          });
        }
      },
      complete: function() {
        sending = false;
        submitBtn.prop('disabled', false).html('<i class="bx bx-send me-1"></i> Send message');
      }
    });
  });
});
</script>
@endpush


@extends('layouts.tenant_app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('tenants.settings.update') }}">
      @csrf
      <div class="row g-4">
        <div class="col-12">
          <h6 class="fw-bold mb-2">Appearance</h6>
          <p class="text-muted mb-3">Choose how your tenant portal looks and feels.</p>
          <div class="border rounded p-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold">Dark mode</div>
                <small class="text-muted">Reduce eye strain in low light.</small>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="darkModeToggle" {{ (auth()->user()?->themePreference ?? 'light') === 'dark' ? 'checked' : '' }}>
              </div>
            </div>
            <input type="hidden" name="themePreference" id="themePreference" value="{{ auth()->user()?->themePreference ?? 'light' }}">
          </div>
        </div>

        <div class="col-12">
          <h6 class="fw-bold mb-2">Accessibility</h6>
          <p class="text-muted mb-3">Make the interface easier to use.</p>
          <div class="border rounded p-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold">Reduce motion</div>
                <small class="text-muted">Minimize animations and transitions.</small>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="reduceMotion" value="1" {{ auth()->user()?->reduceMotion ? 'checked' : '' }}>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary">
          <i class="bx bx-save me-1"></i> Save Settings
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('darkModeToggle');
    const input = document.getElementById('themePreference');
    if (toggle && input) {
      toggle.addEventListener('change', function () {
        input.value = toggle.checked ? 'dark' : 'light';
      });
    }
  });
</script>
@endpush

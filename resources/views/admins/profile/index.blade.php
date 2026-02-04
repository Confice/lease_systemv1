@extends('layouts.admin_app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="card">
  <div class="card-body">
    <div class="d-flex align-items-center mb-4">
      <div class="avatar me-3">
        <div class="avatar-initial rounded-circle bg-label-primary" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #EFEFEA; background-color: #7F9267 !important;">
          {{ strtoupper(substr($user->firstName ?? 'U', 0, 1) . substr($user->lastName ?? '', 0, 1)) }}
        </div>
      </div>
      <div>
        <h5 class="mb-1">{{ trim(($user->firstName ?? '') . ' ' . ($user->middleName ?? '') . ' ' . ($user->lastName ?? '')) ?: 'Lease Manager' }}</h5>
        <small class="text-muted">{{ $user->email ?? 'No email on file' }}</small>
      </div>
    </div>

    <form method="POST" action="{{ route('admins.profile.update') }}">
      @csrf
      @method('PUT')

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">First Name</label>
          <input type="text" name="firstName" class="form-control" value="{{ old('firstName', $user->firstName) }}" required>
          @error('firstName')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Middle Name</label>
          <input type="text" name="middleName" class="form-control" value="{{ old('middleName', $user->middleName) }}">
          @error('middleName')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Last Name</label>
          <input type="text" name="lastName" class="form-control" value="{{ old('lastName', $user->lastName) }}" required>
          @error('lastName')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label class="form-label">Contact No.</label>
          <input type="text" name="contactNo" class="form-control" value="{{ old('contactNo', $user->contactNo) }}" placeholder="09XXXXXXXXX or XXXX-XXX-XXXX" required>
          @error('contactNo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Birthdate</label>
          <input type="date" name="birthDate" class="form-control" value="{{ old('birthDate', $user->birthDate ? $user->birthDate->format('Y-m-d') : '') }}">
          @error('birthDate')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
          <label class="form-label">Home Address</label>
          <input type="text" name="homeAddress" class="form-control" value="{{ old('homeAddress', $user->homeAddress) }}">
          @error('homeAddress')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary">
          <i class="bx bx-save me-1"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

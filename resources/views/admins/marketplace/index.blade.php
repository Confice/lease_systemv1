@extends('layouts.admin_app')

@section('title', 'Marketplace Map')
@section('page-title', 'Marketplace Map')

@push('styles')
<style>
  .marketplace-tabs {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 0;
    padding-bottom: 0;
  }
  
  .marketplace-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    background-color: rgba(127, 146, 103, 0.2);
    color: #000000;
    font-weight: 500;
    padding: 15px 30px;
    margin-bottom: -2px;
    transition: all 0.3s ease;
    border-radius: 0.375rem 0.375rem 0 0;
  }
  
  .marketplace-tabs .nav-link:hover {
    color: #7F9267;
    border-bottom-color: rgba(127, 146, 103, 0.4);
    background-color: rgba(127, 146, 103, 0.3);
  }
  
  .marketplace-tabs .nav-link.active {
    color: #FFFFFF !important;
    border-bottom-color: #7F9267 !important;
    background-color: #7F9267 !important;
    font-weight: bold;
  }
  
  .marketplace-tabs .nav-link.active,
  .marketplace-tabs .nav-link.active:focus,
  .marketplace-tabs .nav-link.active:hover {
    color: #FFFFFF !important;
    background-color: #7F9267 !important;
    border-bottom-color: #7F9267 !important;
  }
  
  .tab-content-wrapper {
    min-height: 600px;
  }

  /* Make card body padding uniform */
  .marketplace-tabs + .card .card-body {
    padding: 1.5rem !important;
  }
</style>
@endpush

@section('content')
<!-- Centered Tabs -->
<ul class="nav nav-pills marketplace-tabs" id="marketplaceTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="hub-tab" data-bs-toggle="pill" data-bs-target="#hub-pane" type="button" role="tab" aria-controls="hub-pane" aria-selected="true">
      The Hub by D & G Properties
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="bazaar-tab" data-bs-toggle="pill" data-bs-target="#bazaar-pane" type="button" role="tab" aria-controls="bazaar-pane" aria-selected="false">
      Your One-Stop Bazaar
    </button>
  </li>
</ul>

<!-- Tab Content -->
<div class="card">
  <div class="card-body">
    <div class="tab-content tab-content-wrapper" id="marketplaceTabContent">
      <!-- The Hub Tab -->
      <div class="tab-pane fade show active" id="hub-pane" role="tabpanel" aria-labelledby="hub-tab">
        @include('admins.marketplace.hub-content')
      </div>
      
      <!-- Bazaar Tab -->
      <div class="tab-pane fade" id="bazaar-pane" role="tabpanel" aria-labelledby="bazaar-tab">
        @include('admins.marketplace.bazaar-content')
      </div>
    </div>
  </div>
</div>
@endsection


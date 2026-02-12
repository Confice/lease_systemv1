@extends('layouts.admin_app')

@section('title','Prospective Tenants')
@section('page-title', 'Prospective Tenants')

@push('styles')
<style>
  .stall-card {
    border: 2px solid #6B7A56;
    border-radius: 12px;
    transition: all 0.3s ease;
    height: 100%;
  }
  
  .stall-card:hover {
    border-color: #7F9267;
    box-shadow: 0 4px 12px rgba(127, 146, 103, 0.15);
    background-color: #EFEFEA;
  }
  
  .application-count-badge {
    background-color: #7F9267;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: bold;
  }
  
  .application-count-badge.is-empty {
    background-color: #6c757d;
  }
  
  .application-count-badge.has-applications {
    background-color: #198754;
  }
  
  #stallsContainer {
    min-height: 400px;
  }
  
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
  }
  
  .empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #dee2e6;
  }
  
  /* Filter tabs - active state */
  #filterTabs .filter-tab.active {
    font-weight: 600;
  }
  #filterTabs .filter-tab.active.btn-outline-primary {
    background-color: rgba(127, 146, 103, 0.15);
    border-color: rgba(127, 146, 103, 0.5);
    color: #7F9267;
  }
  #filterTabs .filter-tab.active.btn-outline-secondary {
    background-color: rgba(108, 117, 125, 0.2);
    border-color: rgba(108, 117, 125, 0.5);
    color: #6c757d;
  }

  /* Search input placeholder styling */
  #prospectiveTenantsSearch::placeholder {
    color: rgba(127, 146, 103, 0.6) !important;
  }

  #prospectiveTenantsSearch:focus {
    background-color: rgba(127, 146, 103, 0.15) !important;
    border-color: rgba(127, 146, 103, 0.4) !important;
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(127, 146, 103, 0.25) !important;
  }
  
  /* Sort dropdown styling */
  .sort-option .sort-text {
    color: #201F23 !important;
  }
  
  .sort-option.active .sort-text {
    color: #7F9267 !important;
  }
  
  .sort-option .sort-check-icon {
    color: #7F9267 !important;
  }
  
  .sort-option:not(.active) .sort-check-icon {
    display: none !important;
  }
  
  .sort-option.active .sort-check-icon {
    display: inline-block !important;
    color: #7F9267 !important;
  }
  
  /* Override Bootstrap dropdown-item hover for sort options */
  .sort-option:hover .sort-text {
    color: #201F23 !important;
  }
  
  .sort-option.active:hover .sort-text {
    color: #7F9267 !important;
  }
  
  .sort-option.active:hover .sort-check-icon {
    color: #7F9267 !important;
  }
</style>
@endpush

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <!-- Search Bar -->
    <div class="d-flex align-items-center gap-2" style="flex: 1 1 auto; min-width: 0;">
      <div class="position-relative" style="flex: 1 1 auto; min-width: 0; max-width: 400px;">
        <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 1; color: #7F9267;">
          <i class="bx bx-search fs-5"></i>
        </span>
        <input type="text" id="prospectiveTenantsSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search" aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;">
      </div>
      <!-- Sort Dropdown -->
      <div class="dropdown flex-shrink-0">
        <button class="btn btn-label-primary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="white-space: nowrap;">
          <i class="bx bx-sort me-1"></i> <span id="sortDropdownText">Stall Name</span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
          <li><a class="dropdown-item sort-option active" href="#" data-sort="stall_name">
            <i class="bx bx-check me-1 sort-check-icon" style="color: #7F9267 !important; display: inline-block !important;"></i> <span class="sort-text" style="color: #7F9267 !important;">Stall Name</span>
          </a></li>
          <li><a class="dropdown-item sort-option" href="#" data-sort="recent_application">
            <i class="bx bx-check me-1 sort-check-icon" style="display: none !important; color: #7F9267 !important;"></i> <span class="sort-text" style="color: #201F23 !important;">Most Recent Application</span>
          </a></li>
          <li><a class="dropdown-item sort-option" href="#" data-sort="deadline_desc">
            <i class="bx bx-check me-1 sort-check-icon" style="display: none !important; color: #7F9267 !important;"></i> <span class="sort-text" style="color: #201F23 !important;">Application Deadline (Newest First)</span>
          </a></li>
          <li><a class="dropdown-item sort-option" href="#" data-sort="deadline_asc">
            <i class="bx bx-check me-1 sort-check-icon" style="display: none !important; color: #7F9267 !important;"></i> <span class="sort-text" style="color: #201F23 !important;">Application Deadline (Oldest First)</span>
          </a></li>
        </ul>
      </div>
    </div>
    <!-- /Search Bar -->
    <a href="{{ route('admins.marketplace.index') }}" class="btn btn-label-primary flex-shrink-0">
      <i class="bx bx-map me-1"></i> Go to Marketplace Map
    </a>
  </div>

  <div class="card-body">
    <div class="d-flex flex-wrap gap-2 mb-3" id="filterTabs">
      <button type="button" class="btn btn-outline-primary filter-tab active" data-filter="all">
        All <span class="badge bg-primary ms-1">{{ $statusCounts['All'] ?? 0 }}</span>
      </button>
      <button type="button" class="btn btn-outline-primary filter-tab" data-filter="hub">
        The Hub <span class="badge bg-primary ms-1">{{ $statusCounts['The Hub'] ?? 0 }}</span>
      </button>
      <button type="button" class="btn btn-outline-secondary filter-tab" data-filter="bazaar">
        Bazaar <span class="badge bg-secondary ms-1">{{ $statusCounts['Bazaar'] ?? 0 }}</span>
      </button>
    </div>

    <!-- Stalls Container -->
    <div id="stallsContainer" class="row g-4">
      <!-- Stalls will be loaded here via AJAX -->
      <div class="col-12 empty-state">
        <i class="bx bx-store-alt"></i>
        <h5>No vacant stalls found</h5>
        <p>There are currently no vacant stalls available.</p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  
  let allStalls = [];
  let currentSort = 'stall_name'; // Default sort
  let currentFilter = 'all'; // Default filter (All, hub, bazaar)
  
  function formatDate(dateString) {
    if (!dateString) return 'No deadline set';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
  }
  
  function formatCurrency(amount) {
    if (!amount || amount === '-') return '-';
    return '₱' + parseFloat(amount.replace(/,/g, '')).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }
  
  function loadStalls(searchQuery = '', sortBy = 'stall_name', filter = 'all') {
    $.get("{{ route('admins.prospective-tenants.data') }}", { sort: sortBy, filter: filter }, function(response) {
      allStalls = response.data || [];
      
      // Filter by search query
      let filteredStalls = allStalls;
      if (searchQuery) {
        const query = searchQuery.toLowerCase();
        filteredStalls = allStalls.filter(function(stall) {
          return (
            stall.stallNo.toLowerCase().includes(query) ||
            stall.location.toLowerCase().includes(query) ||
            stall.formattedStallId.toLowerCase().includes(query)
          );
        });
      }
      
      const container = $('#stallsContainer');
      container.empty();
      
      if (filteredStalls.length > 0) {
        filteredStalls.forEach(function(stall) {
          const card = `
            <div class="col-md-6 col-lg-4">
              <div class="card stall-card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                      <h5 class="card-title mb-1">${stall.stallNo}</h5>
                      <small class="text-muted">${stall.formattedStallId}</small>
                    </div>
                    <span class="application-count-badge ${stall.applicationCount > 0 ? 'has-applications' : 'is-empty'}">${stall.applicationCount} Application${stall.applicationCount !== 1 ? 's' : ''}</span>
                  </div>
                  
                  <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bx bx-map text-primary me-2"></i>
                      <div>
                        <small class="text-muted d-block">Location</small>
                        <span class="fw-semibold">${stall.location}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="bx bx-ruler text-success me-2"></i>
                      <div>
                        <small class="text-muted d-block">Stall Size</small>
                        <span class="fw-semibold">${stall.size} sq. m.</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="bx bx-money text-warning me-2"></i>
                      <div>
                        <small class="text-muted d-block">Monthly Rental Fee</small>
                        <span class="fw-semibold">${formatCurrency(stall.rentalFee)}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center">
                      <i class="bx bx-time-five text-danger me-2"></i>
                      <div>
                        <small class="text-muted d-block">Application Deadline</small>
                        <span class="fw-semibold">${formatDate(stall.applicationDeadline)}</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="text-center mt-3">
                    <button class="btn btn-primary btn-more" data-stall-id="${stall.stallID}">
                      <i class="bx bx-chevron-right me-1"></i> More
                    </button>
                  </div>
                </div>
              </div>
            </div>
          `;
          container.append(card);
        });
      } else {
        container.html(`
          <div class="col-12 empty-state">
            <i class="bx bx-store-alt"></i>
            <h5>${searchQuery ? 'No matching stalls found' : 'No vacant stalls found'}</h5>
            <p>${searchQuery ? 'Try adjusting your search query.' : 'There are currently no vacant stalls available.'}</p>
          </div>
        `);
      }
    }).fail(function() {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Failed to load stalls. Please try again.',
        toast: true,
        position: 'top',
        timer: 2000
      });
    });
  }
  
  // Search functionality
  $('#prospectiveTenantsSearch').on('keyup', function(){
    const query = $(this).val();
    loadStalls(query, currentSort, currentFilter);
  });
  
  // Clear search when input is cleared
  $('#prospectiveTenantsSearch').on('input', function(){
    if ($(this).val() === '') {
      loadStalls('', currentSort, currentFilter);
    }
  });

  // Filter tabs (The Hub / Bazaar)
  $('#filterTabs').on('click', '.filter-tab', function() {
    $('#filterTabs .filter-tab').removeClass('active');
    $(this).addClass('active');
    currentFilter = $(this).data('filter');
    const searchQuery = $('#prospectiveTenantsSearch').val();
    loadStalls(searchQuery, currentSort, currentFilter);
  });
  
  // Sort functionality
  $('.sort-option').on('click', function(e) {
    e.preventDefault();
    const sortValue = $(this).data('sort');
    currentSort = sortValue;
    
    // Update active state
    $('.sort-option').removeClass('active');
    $('.sort-option').find('.sort-check-icon').hide().css('color', '#7F9267');
    $('.sort-option').find('.sort-text').css('color', '#201F23');
    
    $(this).addClass('active');
    $(this).find('.sort-check-icon').show().css('color', '#7F9267');
    $(this).find('.sort-text').css('color', '#7F9267');
    
    // Update dropdown button text
    const sortText = $(this).find('.sort-text').text();
    $('#sortDropdownText').text(sortText);
    
    // Reload with new sort
    const searchQuery = $('#prospectiveTenantsSearch').val();
    loadStalls(searchQuery, currentSort, currentFilter);
  });
  
  // More button click
  $(document).on('click', '.btn-more', function() {
    const stallId = $(this).data('stall-id');
    // Navigate to view applications for this stall
    window.location.href = "{{ route('admins.prospective-tenants.applications', ':stallId') }}".replace(':stallId', stallId) + "?from=prospective";
  });
  
  // Load stalls on page load
  loadStalls('', currentSort, currentFilter);
});
</script>
@endpush

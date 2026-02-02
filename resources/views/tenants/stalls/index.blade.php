@extends('layouts.tenant_app')

@section('title', 'My Stalls')
@section('page-title', 'My Stalls')

@push('styles')
<style>
  .application-card {
    border: 2px solid #6B7A56;
    border-radius: 12px;
    height: auto;
    transition: all 0.3s ease;
  }
  
  .application-card:hover {
    border-color: #7F9267;
    box-shadow: 0 4px 12px rgba(127, 146, 103, 0.15);
    background-color: #EFEFEA;
  }
  
  .application-card .card-body {
    padding-bottom: 0.75rem !important;
  }
  
  .btn-view-submission {
    background-color: #6B7A56 !important;
    border-color: #6B7A56 !important;
    color: #FFFFFF !important;
    transition: none !important;
    animation: none !important;
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
    font-size: 0.9rem !important;
  }
  
  .btn-view-submission:hover {
    background-color: #5A6749 !important;
    border-color: #5A6749 !important;
    color: #FFFFFF !important;
  }
  
  .btn-withdraw-submission {
    transition: none !important;
    animation: none !important;
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
    font-size: 0.9rem !important;
    border-color: #dc3545 !important; /* Match text color */
    color: #000000 !important; /* Black text */
  }
  
  .btn-withdraw-submission:hover {
    border: none !important; /* No border on hover */
    color: #ffffff !important; /* White text on hover */
  }
  
  .status-badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
  }
  
  /* PROPOSAL RECEIVED - Any shade of blue */
  .status-proposal-received {
    background-color: rgba(13, 110, 253, 0.15);
    color: #0d6efd;
  }
  
  /* PRESENTATION SCHEDULED - Any shade of violet */
  .status-presentation-scheduled {
    background-color: rgba(111, 66, 193, 0.15);
    color: #6f42c1;
  }
  
  /* PENDING SUBMISSION - Any shade of yellow */
  .status-pending-submission {
    background-color: rgba(255, 193, 7, 0.15);
    color: #cc9a00;
  }
  
  /* PROPOSAL REJECTED - Any shade of red */
  .status-proposal-rejected {
    background-color: rgba(220, 53, 69, 0.15);
    color: #dc3545;
  }
  
  /* REQUIREMENTS RECEIVED - Any shade of orange */
  .status-requirements-received {
    background-color: rgba(253, 126, 20, 0.15);
    color: #fd7e14;
  }

  /* APPROVED - Any shade of green */
  .status-approved {
    background-color: rgba(25, 135, 84, 0.15);
    color: #198754;
  }
  
  /* WITHDRAWN - Any shade of gray */
  .status-withdrawn {
    background-color: rgba(108, 117, 125, 0.15);
    color: #000000 !important; /* Black text */
  }
  
  #applicationsContainer {
    padding-bottom: 0 !important;
  }
  
  #applicationsContainer .row.g-4 {
    margin-bottom: 0 !important;
  }
  
  .applications-section {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
  }
  
  #assignedStallsContainer {
    padding-bottom: 0 !important;
  }
  
  #assignedStallsContainer .row.g-4 {
    margin-bottom: 0 !important;
  }
  
  .assigned-stalls-section {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
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
  
  .section-divider {
    height: 2px;
    background: linear-gradient(to right, transparent, #6B7A56, transparent);
    margin: 3rem 0;
    border: none;
  }
  
  .section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #6B7A56;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .assigned-stall-card {
    border: 2px solid #6B7A56;
    border-radius: 12px;
    height: auto;
    transition: all 0.3s ease;
  }
  
  .assigned-stall-card:hover {
    border-color: #7F9267;
    box-shadow: 0 4px 12px rgba(127, 146, 103, 0.15);
    background-color: #EFEFEA;
  }
  
  .assigned-stall-card .card-body {
    padding-bottom: 0.75rem !important;
  }
  
  .btn-view-stall {
    background-color: #6B7A56 !important;
    border-color: #6B7A56 !important;
    color: #FFFFFF !important;
    transition: none !important;
    animation: none !important;
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
    font-size: 0.9rem !important;
  }
  
  .btn-view-stall:hover {
    background-color: #5A6749 !important;
    border-color: #5A6749 !important;
    color: #FFFFFF !important;
  }
  
  /* Tab styling to match admin side drawers */
  #viewSubmissionDrawer .nav-pills .nav-link {
    color: #7F9267;
    border-radius: 0.375rem;
  }
  
  #viewSubmissionDrawer .nav-pills .nav-link:hover {
    color: #7F9267;
    background-color: #EFEFEA;
  }
  
  #viewSubmissionDrawer .nav-pills .nav-link.active {
    background-color: #7F9267;
    color: white;
  }
  
  #viewSubmissionDrawer .nav-pills .nav-link.active:hover {
    background-color: #6B7A56;
    color: white;
  }
  
  /* Requirement File Modal - Position above side drawer */
  #requirementFileModal {
    z-index: 1105 !important;
  }
  
  #requirementFileModal.show {
    z-index: 1105 !important;
  }
  
  #requirementFileModal.modal.show {
    z-index: 1105 !important;
  }
  
  body:has(#requirementFileModal.show) .modal-backdrop,
  .modal-backdrop.show {
    z-index: 1104 !important;
  }
  
  /* Ensure modal is above offcanvas */
  .offcanvas.show ~ #requirementFileModal,
  #requirementFileModal.show {
    z-index: 1105 !important;
  }
</style>
@endpush

@section('content')
<div class="card">
  <div class="card-body">
    <!-- Applications Section -->
    <div class="applications-section">
      <h5 class="section-title">
        <i class="bx bx-file-blank"></i>
        Applications
      </h5>
      <div id="applicationsContainer" class="row g-4">
        <!-- Applications will be loaded here via AJAX -->
        <div class="col-12 empty-state">
          <i class="bx bx-store-alt"></i>
          <h5>No applications found</h5>
          <p>You haven't submitted any stall applications yet. Browse the marketplace to find available stalls.</p>
        </div>
      </div>
    </div>
    
    <!-- Divider -->
    <hr class="section-divider">
    
    <!-- Assigned Stalls Section -->
    <div class="assigned-stalls-section">
      <h5 class="section-title">
        <i class="bx bx-store"></i>
        Assigned Stalls
      </h5>
      <div id="assignedStallsContainer" class="row g-4">
        <!-- Assigned stalls will be loaded here via AJAX -->
        <div class="col-12 empty-state">
          <i class="bx bx-store-alt"></i>
          <h5>No assigned stalls</h5>
          <p>You don't have any assigned stalls at the moment.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Offcanvas View Submission/Stall Details -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="viewSubmissionDrawer">
  <div class="offcanvas-header border-bottom bg-light">
    <h5 class="offcanvas-title d-flex align-items-center fw-bold text-primary" id="drawerTitle">
      <i class="bx bx-file-blank me-2 fs-3"></i> VIEW SUBMISSION
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <!-- Tabs outside drawer body -->
  <div class="border-bottom bg-light">
    <ul class="nav nav-pills px-3 py-2" role="tablist" id="submissionTabs">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-stall" data-bs-toggle="pill" data-bs-target="#pane-stall" type="button" role="tab">
          <i class="bx bx-store-alt me-2"></i> Stall
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-requirements" data-bs-toggle="pill" data-bs-target="#pane-requirements" type="button" role="tab">
          <i class="bx bx-paperclip me-2"></i> Proposal Requirements
        </button>
      </li>
    </ul>
  </div>

  <div class="offcanvas-body p-0" style="display: flex; flex-direction: column; height: calc(100vh - 70px);">
    <!-- Tab Content -->
    <div class="tab-content overflow-auto px-4 pt-4" style="flex: 1; min-height: 0;" id="submissionTabContent">
      <!-- Stall Tab -->
      <div class="tab-pane fade show active" id="pane-stall" role="tabpanel">
        <div id="stallContent">
          <!-- Stall information will be loaded here via AJAX -->
          <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Submitted Requirements Tab -->
      <div class="tab-pane fade" id="pane-requirements" role="tabpanel">
        <div id="requirementsContent">
          <!-- Requirements will be loaded here via AJAX -->
          <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Viewing Requirement File -->
<div class="modal fade" id="requirementFileModal" tabindex="-1" aria-labelledby="requirementFileModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 90vw;">
    <div class="modal-content">
      <div class="modal-header border-bottom" style="background-color: #EFEFEA !important;">
        <h5 class="modal-title fw-bold text-primary" id="requirementFileModalLabel">
          <i class="bx bx-file me-2"></i> <span id="modalRequirementName"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="min-height: 60vh; max-height: 80vh; overflow: auto;">
        <div id="modalFileContent" style="display: flex; align-items: center; justify-content: center; min-height: 60vh;">
          <!-- File content will be displayed here -->
        </div>
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
  
  function formatDate(dateString) {
    if (!dateString) return '-';
    try {
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return dateString;
      return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    } catch(e) {
      return dateString;
    }
  }
  
  function getStatusClass(status) {
    const statusMap = {
      'Proposal Received': 'status-proposal-received',
      'Presentation Scheduled': 'status-presentation-scheduled',
      'Pending Submission': 'status-pending-submission',
      'Proposal Rejected': 'status-proposal-rejected',
      'Requirements Received': 'status-requirements-received',
      'Approved': 'status-approved',
      'Withdrawn': 'status-withdrawn'
    };
    return statusMap[status] || 'status-proposal-received';
  }
  
  function loadApplications() {
    $.get("{{ route('tenants.stalls.data') }}", function(response) {
      const container = $('#applicationsContainer');
      container.empty();
      
      if (response.data && response.data.length > 0) {
        response.data.forEach(function(app) {
          const statusClass = getStatusClass(app.appStatus);
          
          let withdrawButton = '';
          if (app.appStatus !== 'Withdrawn') {
            withdrawButton = `
              <button class="btn btn-outline-danger btn-sm btn-withdraw-submission flex-grow-1" data-app-id="${app.applicationID}">
                <i class="bx bx-x me-1"></i> Withdraw Submission
              </button>
            `;
          }
          
          const card = `
            <div class="col-md-6 col-lg-4">
              <div class="card application-card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                      <h5 class="card-title mb-1">${app.stallNo}</h5>
                      <small class="text-muted">${app.formattedStallId}</small>
                    </div>
                    <span class="badge rounded-pill status-badge ${statusClass}">${app.appStatus}</span>
                  </div>
                  
                  <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bx bx-calendar text-primary me-2"></i>
                      <div>
                        <small class="text-muted d-block">Date Applied</small>
                        <span class="fw-semibold">${formatDate(app.dateApplied)}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center">
                      <i class="bx bx-time-five text-warning me-2"></i>
                      <div>
                        <small class="text-muted d-block">Application Deadline</small>
                        <span class="fw-semibold">${app.applicationDeadline ? formatDate(app.applicationDeadline) : 'No deadline set'}</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-view-submission flex-grow-1" data-app-id="${app.applicationID}" data-stall-id="${app.stallID}">
                      <i class="bx bx-show me-1"></i> View Submission
                    </button>
                    ${withdrawButton}
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
            <h5>No applications found</h5>
            <p>You haven't submitted any stall applications yet. Browse the marketplace to find available stalls.</p>
            <a href="{{ route('tenants.marketplace.index') }}" class="btn btn-primary mt-3">
              <i class="bx bx-map me-1"></i> Browse Marketplace
            </a>
          </div>
        `);
      }
    }).fail(function() {
      Swal.fire({
        icon:'error',
        title:'Error',
        text: 'Failed to load applications. Please try again.',
        toast:true,
        position:'top',
        showConfirmButton:false,
        showCloseButton:true,
        timer: 2000,
        timerProgressBar:true
      });
    });
  }
  
  // View Submission - Open offcanvas drawer
  $(document).on('click', '.btn-view-submission', function() {
    const appId = $(this).data('app-id');
    
    // Show loading state
    $('#stallContent').html(`
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `);
    $('#requirementsContent').html(`
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `);
    
    // Reset to first tab
    const tabElement = document.getElementById('tab-stall');
    if (tabElement) {
      const tab = new bootstrap.Tab(tabElement);
      tab.show();
    }
    
    // Update drawer title
    $('#drawerTitle').html('<i class="bx bx-file-blank me-2 fs-3"></i> VIEW SUBMISSION');
    
    // Open drawer
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('viewSubmissionDrawer'));
    offcanvas.show();
    
    // Load submission details via AJAX
    $.get("{{ route('tenants.applications.show', ':id') }}".replace(':id', appId), function(response) {
      if (response.success) {
        const app = response.application;
        const stall = response.stall;
        const document = response.document;
        const proposalReqs = response.proposalRequirements || [];
        const tenancyReqs = response.tenancyRequirements || [];
        
        // Build requirements list with clickable fields that open modal
        let requirementsHtml = '';
        if (proposalReqs.length > 0) {
          requirementsHtml = '<div class="mb-3"><h6 class="fw-bold mb-2">Proposal Requirements:</h6><ul class="list-unstyled mb-0">';
          proposalReqs.forEach(function(req) {
            const uploadedFile = document && document.files && Array.isArray(document.files) 
              ? document.files.find(f => f.requirementName === req.requirement_name)
              : null;
            const hasFile = uploadedFile !== null && uploadedFile !== undefined;
            // Use fileUrl from backend if available, otherwise construct it
            let fileUrl = '';
            if (hasFile) {
              if (uploadedFile.fileUrl) {
                // Use the fileUrl provided by the backend (from Storage::url())
                fileUrl = uploadedFile.fileUrl;
              } else if (uploadedFile.filePath) {
                // Fallback: construct URL if fileUrl is not provided
                const cleanPath = uploadedFile.filePath.replace(/^storage\//, '').replace(/^\/storage\//, '');
                fileUrl = cleanPath ? "{{ url('/storage') }}/" + cleanPath : '';
              }
            }
            const fileName = hasFile ? (uploadedFile.originalName || 'File') : '';
            const fileDate = hasFile ? (uploadedFile.dateUploaded || '') : '';
            
            // Escape single quotes for onclick
            const escapedReqName = req.requirement_name.replace(/'/g, "\\'");
            const escapedFileName = fileName.replace(/'/g, "\\'");
            const escapedFileUrl = fileUrl.replace(/'/g, "\\'");
            
            requirementsHtml += `
              <li class="mb-3">
                <div class="d-flex align-items-center justify-content-between p-3 border rounded ${hasFile ? 'bg-light' : ''}">
                  <div class="d-flex align-items-center flex-grow-1">
                    <i class="bx ${hasFile ? 'bx-check-circle text-success' : 'bx-circle text-muted'} me-2 fs-5"></i>
                    <span class="fw-semibold me-2">${req.requirement_name}</span>
                    ${req.is_active ? '<span class="badge bg-label-danger">Required</span>' : '<span class="badge bg-label-secondary">Optional</span>'}
                  </div>
                  ${hasFile ? `
                    <div class="d-flex gap-2">
                      <button class="btn btn-sm btn-label-primary" onclick="showRequirementFile('${escapedReqName}', '${escapedFileName}', '${escapedFileUrl}')">
                        <i class="bx bx-show me-1"></i> View
                      </button>
                      <a href="${escapedFileUrl}" download="${escapedFileName}" class="btn btn-sm btn-label-secondary">
                        <i class="bx bx-download me-1"></i> Download
                      </a>
                    </div>
                  ` : '<span class="text-muted small">No file uploaded</span>'}
                </div>
              </li>
            `;
          });
          requirementsHtml += '</ul></div>';
        } else {
          requirementsHtml = '<div class="text-center text-muted py-5"><i class="bx bx-paperclip fs-1 mb-3"></i><p>No requirements available.</p></div>';
        }
        
        // Stall Tab Content - Match stall module view drawer exactly
        // Format stall ID like stall module
        function formatStallIdForApplication(marketplaceName, stallId) {
          if (!marketplaceName || !stallId) return '-';
          let prefix = '';
          const marketplaceUpper = marketplaceName.toUpperCase();
          
          // Check for "The Hub by D & G Properties"
          if (marketplaceUpper.includes('THE HUB') || marketplaceUpper.includes('HUB BY D & G')) {
            prefix = 'HUB-';
          }
          // Check for "Bazaar"
          else if (marketplaceUpper.includes('BAZAAR')) {
            prefix = 'BZR-';
          }
          // Default: use first 2-3 letters
          else {
            const words = marketplaceName.split(' ');
            if (words.length > 0) {
              prefix = words[0].substring(0, 3).toUpperCase() + '-';
            } else {
              prefix = marketplaceName.substring(0, 3).toUpperCase() + '-';
            }
          }
          
          return prefix + String(stallId).padStart(4, '0');
        }
        
        let stallContent = `
          <div class="text-center mb-4">
            <div class="bg-gray text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm"
                 style="width:80px; height:80px; font-size:1.5rem;">
              <i class="bx bx-store fs-1"></i>
            </div>
            <h5 class="mt-3 fw-bold">${stall.stallNo || '-'}</h5>
            <span class="badge rounded-pill px-3 py-2 bg-label-danger">Vacant</span>
          </div>

          <div class="card shadow border-0 mb-4">
            <div class="card-body">
              <h6 class="fw-bold text-uppercase text-muted fs-5 mb-3">
                <i class="bx bx-store-alt me-1 text-secondary fs-4"></i> Stall Details
              </h6>
              
              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                    style="width:36px; height:36px;">
                  <i class="bx bx-map text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Marketplace</div>
                  <div>${stall.marketplace || '-'}</div>
                </div>
              </div>

              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                     style="width:36px; height:36px;">
                  <i class="bx bx-ruler text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Size (sq. m.)</div>
                  <div>${stall.size || '-'}</div>
                </div>
              </div>

              <div class="mb-0 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                     style="width:36px; height:36px;">
                  <i class="bx bx-money text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Monthly Rental Fee</div>
                  <div>${stall.rentalFee ? '₱' + parseFloat(stall.rentalFee).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-'}</div>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow border-0 mb-0">
            <div class="card-body">
              <h6 class="fw-bold text-uppercase text-muted fs-5 mb-3">
                <i class="bx bx-calendar me-1 text-secondary fs-4"></i> Key Dates
              </h6>
              <div class="mb-3 d-flex align-items-start">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                     style="width:36px; height:36px;">
                  <i class="bx bx-time text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Application Deadline</div>
                  <div>${stall.applicationDeadline ? formatDate(stall.applicationDeadline) : '-'}</div>
                </div>
              </div>
              <div class="mb-0 d-flex align-items-start">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                     style="width:36px; height:36px;">
                  <i class="bx bx-calendar-check text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Date Applied</div>
                  <div>${app.dateApplied ? formatDate(app.dateApplied) : '-'}</div>
                </div>
              </div>
            </div>
          </div>
        `;
        
        // Requirements Tab Content
        let requirementsContent = '';
        if (proposalReqs.length > 0 || tenancyReqs.length > 0) {
          requirementsContent = `
            <div class="card shadow border-0">
              <div class="card-body">
                <h6 class="fw-bold text-uppercase text-muted fs-5 mb-3">
                  <i class="bx bx-paperclip me-1 text-secondary fs-4"></i> Proposal Requirements
                </h6>
                
                ${requirementsHtml}
                
                ${document && document.revisionComment ? `
                <div class="mt-3">
                  <h6 class="fw-bold mb-2">Revision Comment:</h6>
                  <div class="alert alert-warning mb-0">${document.revisionComment}</div>
                </div>
                ` : ''}
              </div>
            </div>
          `;
        } else {
          requirementsContent = requirementsHtml;
        }
        
        $('#stallContent').html(stallContent);
        $('#requirementsContent').html(requirementsContent);
      } else {
        $('#stallContent').html(`
          <div class="alert alert-danger">
            <i class="bx bx-error-circle me-2"></i> Failed to load submission details.
          </div>
        `);
        $('#requirementsContent').html(`
          <div class="alert alert-danger">
            <i class="bx bx-error-circle me-2"></i> Failed to load submission details.
          </div>
        `);
      }
    }).fail(function(xhr, status, error) {
      console.error('Error loading submission:', status, error, xhr.responseText);
      let errorMsg = 'Error loading submission details. Please try again.';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMsg = xhr.responseJSON.message;
      }
      $('#stallContent').html(`
        <div class="alert alert-danger">
          <i class="bx bx-error-circle me-2"></i> ${errorMsg}
        </div>
      `);
      $('#requirementsContent').html(`
        <div class="alert alert-danger">
          <i class="bx bx-error-circle me-2"></i> ${errorMsg}
        </div>
      `);
    });
  });
  
  // Withdraw Submission
  $(document).on('click', '.btn-withdraw-submission', function() {
    const appId = $(this).data('app-id');
    
    Swal.fire({
      title: 'Withdraw Application?',
      text: 'Are you sure you want to withdraw this application? This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: 'transparent',
      confirmButtonText: 'Yes, Withdraw',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/applications/${appId}/withdraw`,
          type: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            if (response.success === false) {
              Swal.fire({
                icon:'error',
                title:'Error',
                text: response.message || 'Failed to withdraw application. Please try again.',
                toast:true,
                position:'top',
                showConfirmButton:false,
                showCloseButton:true,
                timer: 2000,
                timerProgressBar:true
              });
              return;
            }
            Swal.fire({
              icon:'success',
              title:'Success',
              text: 'Application has been withdrawn successfully.',
              toast:true,
              position:'top',
              showConfirmButton:false,
              showCloseButton:true,
              timer: 2000,
              timerProgressBar:true
            }).then(() => {
              loadApplications();
            });
          },
          error: function(xhr) {
            let errorMessage = 'Failed to withdraw application. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            }
            Swal.fire({
              icon:'error',
              title:'Error',
              text: errorMessage,
              toast:true,
              position:'top',
              showConfirmButton:false,
              showCloseButton:true,
              timer: 2000,
              timerProgressBar:true
            });
          }
        });
      }
    });
  });
  
  function loadAssignedStalls() {
    $.get("{{ route('tenants.stalls.assigned') }}", function(response) {
      const container = $('#assignedStallsContainer');
      container.empty();
      
      if (response.data && response.data.length > 0) {
        response.data.forEach(function(contract) {
          const card = `
            <div class="col-md-6 col-lg-4">
              <div class="card assigned-stall-card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                      <h5 class="card-title mb-1">${contract.stallNo}</h5>
                      <small class="text-muted">${contract.formattedStallId}</small>
                    </div>
                    <span class="badge rounded-pill bg-success">${contract.contractStatus}</span>
                  </div>
                  
                  <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bx bx-map text-primary me-2"></i>
                      <div>
                        <small class="text-muted d-block">Location</small>
                        <span class="fw-semibold">${contract.marketplace || '-'}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="bx bx-ruler text-primary me-2"></i>
                      <div>
                        <small class="text-muted d-block">Size</small>
                        <span class="fw-semibold">${contract.size || '-'} sq. m.</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="bx bx-money text-primary me-2"></i>
                      <div>
                        <small class="text-muted d-block">Monthly Rental</small>
                        <span class="fw-semibold">₱${contract.rentalFee || '-'}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center">
                      <i class="bx bx-calendar-check text-primary me-2"></i>
                      <div>
                        <small class="text-muted d-block">Start Date</small>
                        <span class="fw-semibold">${formatDate(contract.startDate)}</span>
                      </div>
                    </div>
                    ${contract.endDate ? `
                    <div class="d-flex align-items-center mt-2">
                      <i class="bx bx-calendar-x text-warning me-2"></i>
                      <div>
                        <small class="text-muted d-block">End Date</small>
                        <span class="fw-semibold">${formatDate(contract.endDate)}</span>
                      </div>
                    </div>
                    ` : ''}
                  </div>
                  
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-view-stall flex-grow-1" data-stall-id="${contract.stallID}" data-contract-id="${contract.contractID}">
                      <i class="bx bx-show me-1"></i> View Details
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
            <h5>No assigned stalls</h5>
            <p>You don't have any assigned stalls at the moment.</p>
          </div>
        `);
      }
    }).fail(function() {
      Swal.fire({
        icon:'error',
        title:'Error',
        text: 'Failed to load assigned stalls. Please try again.',
        toast:true,
        position:'top',
        showConfirmButton:false,
        showCloseButton:true,
        timer: 2000,
        timerProgressBar:true
      });
    });
  }
  
  // View Stall Details
  $(document).on('click', '.btn-view-stall', function() {
    const stallId = $(this).data('stall-id');
    const contractId = $(this).data('contract-id');
    
    // Show loading state
    $('#stallContent').html(`
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `);
    $('#requirementsContent').html(`
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `);
    
    // Reset to first tab
    const tabElement = document.getElementById('tab-stall');
    if (tabElement) {
      const tab = new bootstrap.Tab(tabElement);
      tab.show();
    }
    
    // Update drawer title
    $('#drawerTitle').html('<i class="bx bx-store me-2 fs-3"></i> STALL DETAILS');
    
    // Open drawer
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('viewSubmissionDrawer'));
    offcanvas.show();
    
    // Load stall details via AJAX
    $.get("{{ url('tenants/stalls') }}/" + stallId, function(response) {
      if (response) {
        let stallContent = `
          <!-- Stall Information -->
          <div class="card shadow border-0 mb-4">
            <div class="card-body">
              <h6 class="fw-bold text-uppercase text-muted fs-5 mb-3">
                <i class="bx bx-store-alt me-1 text-secondary fs-4"></i> Stall Information
              </h6>
              
              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width:36px; height:36px;">
                  <i class="bx bx-hash text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Stall Name</div>
                  <div class="fw-semibold">${response.stallNo}</div>
                </div>
              </div>
              
              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width:36px; height:36px;">
                  <i class="bx bx-map text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Location</div>
                  <div class="fw-semibold">${response.marketplace || '-'}</div>
                </div>
              </div>
              
              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width:36px; height:36px;">
                  <i class="bx bx-ruler text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Size (sq. m.)</div>
                  <div class="fw-semibold">${response.size || '-'}</div>
                </div>
              </div>
              
              <div class="mb-0 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width:36px; height:36px;">
                  <i class="bx bx-money text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Monthly Rental Fee</div>
                  <div class="fw-semibold">${response.rentalFee ? '₱' + parseFloat(response.rentalFee).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-'}</div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Contract Details -->
          <div class="card shadow border-0">
            <div class="card-body">
              <h6 class="fw-bold text-uppercase text-muted fs-5 mb-3">
                <i class="bx bx-file me-1 text-secondary fs-4"></i> Contract Details
              </h6>
              
              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width:36px; height:36px;">
                  <i class="bx bx-info-circle text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Contract Status</div>
                  <div class="fw-semibold">${response.contract.status}</div>
                </div>
              </div>
              
              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width:36px; height:36px;">
                  <i class="bx bx-calendar-check text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Start Date</div>
                  <div class="fw-semibold">${formatDate(response.contract.startDate)}</div>
                </div>
              </div>
              
              ${response.contract.endDate ? `
              <div class="mb-0 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width:36px; height:36px;">
                  <i class="bx bx-calendar-x text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">End Date</div>
                  <div class="fw-semibold">${formatDate(response.contract.endDate)}</div>
                </div>
              </div>
              ` : ''}
            </div>
          </div>
        `;
        
        let requirementsContent = `
          <div class="text-center text-muted py-5">
            <i class="bx bx-paperclip fs-1 mb-3"></i>
            <p>No requirements available for assigned stalls.</p>
          </div>
        `;
        
        $('#stallContent').html(stallContent);
        $('#requirementsContent').html(requirementsContent);
      } else {
        $('#stallContent').html(`
          <div class="alert alert-danger">
            <i class="bx bx-error-circle me-2"></i> Failed to load stall details.
          </div>
        `);
        $('#requirementsContent').html(`
          <div class="alert alert-danger">
            <i class="bx bx-error-circle me-2"></i> Failed to load stall details.
          </div>
        `);
      }
    }).fail(function() {
      $('#stallContent').html(`
        <div class="alert alert-danger">
          <i class="bx bx-error-circle me-2"></i> Error loading stall details. Please try again.
        </div>
      `);
      $('#requirementsContent').html(`
        <div class="alert alert-danger">
          <i class="bx bx-error-circle me-2"></i> Error loading stall details. Please try again.
        </div>
      `);
    });
  });
  
  // Load applications and assigned stalls on page load
  loadApplications();
  loadAssignedStalls();
  
  // Function to show requirement file in modal
  window.showRequirementFile = function(requirementName, fileName, fileUrl) {
    console.log('Showing file:', { requirementName, fileName, fileUrl });
    
    // Set modal title
    $('#modalRequirementName').text(requirementName);
    
    // Clear previous content
    $('#modalFileContent').empty();
    
    if (!fileUrl || fileUrl === '' || fileUrl === '#') {
      $('#modalFileContent').html(`
        <div class="text-center text-muted py-5">
          <i class="bx bx-error-circle fs-1 mb-3"></i>
          <p>File not found or unavailable.</p>
        </div>
      `);
    } else {
      // Check file type
      const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.svg'];
      const pdfExtensions = ['.pdf'];
      const fileExtension = fileName.toLowerCase().substring(fileName.lastIndexOf('.'));
      const isImage = imageExtensions.includes(fileExtension);
      const isPdf = pdfExtensions.includes(fileExtension);
      
      if (isImage) {
        // Display image with error handling
        const img = $('<img>')
          .attr('src', fileUrl)
          .attr('alt', fileName)
          .addClass('img-fluid')
          .css({
            'max-width': '100%',
            'max-height': '80vh',
            'object-fit': 'contain'
          })
          .on('error', function() {
            console.error('Failed to load image:', fileUrl);
            $('#modalFileContent').html(`
              <div class="text-center text-muted py-5">
                <i class="bx bx-error-circle fs-1 mb-3"></i>
                <p class="mb-2 fw-semibold">Failed to load image</p>
                <p class="text-muted small mb-3">${fileName}</p>
                <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                  <i class="bx bx-download me-1"></i> Try Download Instead
                </a>
              </div>
            `);
          });
        $('#modalFileContent').html(img);
      } else if (isPdf) {
        // Display PDF in iframe with error handling
        const iframe = $('<iframe>')
          .attr('src', fileUrl)
          .css({
            'width': '100%',
            'height': '80vh',
            'border': 'none'
          })
          .on('load', function() {
            // Check if iframe loaded successfully
            try {
              const iframeDoc = this.contentDocument || this.contentWindow.document;
              // If we can access the document, it loaded successfully
            } catch (e) {
              // Cross-origin or error loading
              console.error('Iframe load error:', e);
            }
          })
          .on('error', function() {
            console.error('Failed to load PDF:', fileUrl);
            $('#modalFileContent').html(`
              <div class="text-center text-muted py-5">
                <i class="bx bx-error-circle fs-1 mb-3"></i>
                <p class="mb-2 fw-semibold">Failed to load PDF</p>
                <p class="text-muted small mb-3">${fileName}</p>
                <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                  <i class="bx bx-download me-1"></i> Download File
                </a>
              </div>
            `);
          });
        $('#modalFileContent').html(iframe);
      } else {
        // For other file types, show download option
        $('#modalFileContent').html(`
          <div class="text-center py-5">
            <i class="bx bx-file-blank fs-1 text-primary mb-3"></i>
            <p class="mb-2 fw-semibold">${fileName}</p>
            <p class="text-muted small mb-3">This file type cannot be previewed.</p>
            <a href="${fileUrl}" target="_blank" class="btn btn-primary">
              <i class="bx bx-download me-1"></i> Download File
            </a>
          </div>
        `);
      }
    }
    
    // Show modal with higher z-index
    const modalElement = document.getElementById('requirementFileModal');
    
    // Remove any existing modal instances
    const existingModal = bootstrap.Modal.getInstance(modalElement);
    if (existingModal) {
      existingModal.dispose();
    }
    
    const modal = new bootstrap.Modal(modalElement, {
      backdrop: true,
      keyboard: true
    });
    
    // Ensure modal appears above offcanvas - set z-index before showing
    $(modalElement).css('z-index', '1105');
    
    // Handle modal show event to set z-index
    $(modalElement).off('show.bs.modal shown.bs.modal').on('show.bs.modal', function() {
      $(this).css('z-index', '1105');
    });
    
    $(modalElement).on('shown.bs.modal', function() {
      $(this).css('z-index', '1105');
      // Find and update backdrop z-index
      const backdrops = $('.modal-backdrop');
      if (backdrops.length > 0) {
        backdrops.last().css('z-index', '1104');
      }
    });
    
    modal.show();
    
    // Set z-index immediately after showing (multiple attempts to ensure it works)
    setTimeout(function() {
      $(modalElement).css('z-index', '1105');
      const backdrops = $('.modal-backdrop');
      if (backdrops.length > 0) {
        backdrops.last().css('z-index', '1104');
      }
    }, 10);
    
    setTimeout(function() {
      $(modalElement).css('z-index', '1105');
      const backdrops = $('.modal-backdrop');
      if (backdrops.length > 0) {
        backdrops.last().css('z-index', '1104');
      }
    }, 100);
    
    setTimeout(function() {
      $(modalElement).css('z-index', '1105');
      const backdrops = $('.modal-backdrop');
      if (backdrops.length > 0) {
        backdrops.last().css('z-index', '1104');
      }
    }, 300);
  };
});
</script>
@endpush

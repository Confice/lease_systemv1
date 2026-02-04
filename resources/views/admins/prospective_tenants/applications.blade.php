@extends('layouts.admin_app')

@section('title', 'Applications for ' . $stall->stallNo . ' - ' . $stall->marketplace->marketplace)
@section('page-title', 'Applications for ' . $stall->stallNo . ' - ' . $stall->marketplace->marketplace)

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css"/>
<link rel="stylesheet" href="{{ asset('sneat/assets/css/users-page-improvements.css') }}">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
  .requirement-icon {
    font-size: 1.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  /* Requirement View and Download Button Styles */
  .requirement-view-btn,
  .requirement-download-btn {
    background-color: #6B7A56 !important;
    color: #EFEFEA !important;
    border-color: #EFEFEA !important;
  }
  
  .requirement-view-btn:hover,
  .requirement-download-btn:hover {
    background-color: #EFEFEA !important;
    color: #6B7A56 !important;
    border-color: #EFEFEA !important;
  }
  .requirement-icon.has-requirements {
    color: #7F9267;
  }
  .requirement-icon.has-requirements:hover {
    color: #5A6749;
    transform: scale(1.1);
  }
  .requirement-icon.no-requirements {
    color: #dee2e6;
    opacity: 0.5;
  }
  .requirement-icon.no-requirements:hover {
    opacity: 0.8;
    transform: scale(1.1);
  }
  
  /* Tab styling to match tenant side drawers */
  #viewApplicationDrawer .nav-pills .nav-link {
    color: #7F9267;
    border-radius: 0.375rem;
  }
  
  #viewApplicationDrawer .nav-pills .nav-link:hover {
    color: #7F9267;
    background-color: #EFEFEA;
  }
  
  #viewApplicationDrawer .nav-pills .nav-link.active {
    background-color: #7F9267;
    color: white;
  }
  
  #viewApplicationDrawer .nav-pills .nav-link.active:hover {
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
  
  /* Search input placeholder styling */
  #applicationsSearch::placeholder {
    color: rgba(127, 146, 103, 0.6) !important;
  }

  #applicationsSearch:focus {
    background-color: rgba(127, 146, 103, 0.15) !important;
    border-color: rgba(127, 146, 103, 0.4) !important;
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(127, 146, 103, 0.25) !important;
  }
  
  /* Schedule Presentation Modal - Ensure it's clickable */
  #schedulePresentationModal {
    z-index: 1060 !important;
  }
  
  #schedulePresentationModal.show,
  #schedulePresentationModal.modal.show {
    z-index: 1060 !important;
  }
  
  #schedulePresentationModal .modal-dialog,
  #schedulePresentationModal .modal-content {
    pointer-events: auto !important;
  }
  
  body:has(#schedulePresentationModal.show) .modal-backdrop,
  .modal-backdrop.show:has(+ #schedulePresentationModal),
  .modal-backdrop:last-of-type:has(+ #schedulePresentationModal) {
    z-index: 1059 !important;
  }
  
  /* Schedule Presentation Modal - Date and Time Input Text Color */
  #schedulePresentationModal #presentationDate,
  #schedulePresentationModal #presentationTime {
    color: #000000 !important;
  }
  
  #schedulePresentationModal #presentationDate::placeholder,
  #schedulePresentationModal #presentationTime::placeholder {
    color: #6c757d !important;
  }
  
  /* View Notice Modal - ensure above backdrop and clickable */
  #viewNoticeModal {
    z-index: 1060 !important;
  }
  #viewNoticeModal.show,
  #viewNoticeModal.modal.show {
    z-index: 1060 !important;
  }
  #viewNoticeModal .modal-dialog,
  #viewNoticeModal .modal-content {
    pointer-events: auto !important;
  }
  /* When View Notice is open, keep its backdrop below the modal */
  body:has(#viewNoticeModal.show) .modal-backdrop {
    z-index: 1040 !important;
  }
</style>
@endpush

@section('content')
<div class="mb-3">
    <a href="{{ route('admins.prospective-tenants.index') }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back me-1"></i> Back to Prospective Tenants
    </a>
</div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <!-- Search Bar -->
    <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
        <div class="position-relative w-100">
            <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 1; color: #7F9267;">
                <i class="bx bx-search fs-5"></i>
            </span>
            <input type="text" id="applicationsSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search" aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;">
        </div>
    </div>
    <!-- /Search Bar -->
    
    <!-- Add New Record Button -->
    <div class="d-flex align-items-center">
        <button type="button" id="btnAddApplication" class="btn btn-primary">
            <i class="bx bx-plus me-1 fs-5"></i> Add New Record
        </button>
    </div>
  </div>

  <div class="card-body">
    <!-- Action Buttons Group (aligned with DataTables length selector) -->
    <div class="d-flex align-items-center gap-2" id="actionButtonsGroup" style="display: none !important;">
        <!-- Archive Button (hidden by default, shown when rows are selected) -->
        <button id="btnArchiveSelected" class="btn btn-danger d-none" style="display: none !important;">
            <i class="bx bx-archive me-1"></i> Archive
        </button>

        <!-- Export Dropdown -->
        <div class="dropdown">
            <button class="btn btn-label-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bx bx-export me-1"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" id="exportCsv">
                    <i class="fa-solid fa-file-text me-1"></i> CSV
                </a></li>
                <li><a class="dropdown-item" href="#" id="exportPdf">
                    <i class="fa-solid fa-file-pdf me-1"></i> PDF
                </a></li>
            </ul>
        </div>
    </div>
    <div class="table-responsive">
      <table id="applicationsTable" class="table table-hover mb-0">
        <thead>
          <tr>
            <th></th>
            <th class="text-center">
              <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Select All">
            </th>
            <th>#</th>
            <th>Name of Prospect</th>
            <th class="text-center">Requirement</th>
            <th>Status</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Data will be loaded via AJAX -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Offcanvas View Application Details -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="viewApplicationDrawer">
  <div class="offcanvas-header border-bottom bg-light">
    <h5 class="offcanvas-title d-flex align-items-center fw-bold text-primary" id="drawerTitle">
      <i class="bx bx-file-blank me-2 fs-3"></i> VIEW SUBMISSION
    </h5>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="offcanvas" aria-label="Back">
        <i class="bx bx-arrow-back me-1"></i> Back
      </button>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
  </div>

  <!-- Tabs outside drawer body -->
  <div class="border-bottom bg-light">
    <ul class="nav nav-pills px-3 py-2" role="tablist" id="applicationTabs">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-stall" data-bs-toggle="pill" data-bs-target="#pane-stall" type="button" role="tab">
          <i class="bx bx-store-alt me-2"></i> Stall
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-user" data-bs-toggle="pill" data-bs-target="#pane-user" type="button" role="tab">
          <i class="bx bx-user me-2"></i> User
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-requirements" data-bs-toggle="pill" data-bs-target="#pane-requirements" type="button" role="tab">
          <i class="bx bx-paperclip me-2"></i> Submitted Requirements
        </button>
      </li>
    </ul>
  </div>

  <div class="offcanvas-body p-0" style="display: flex; flex-direction: column; height: calc(100vh - 70px);">
    <!-- Tab Content -->
    <div class="tab-content overflow-auto px-4 pt-4" style="flex: 1; min-height: 0;" id="applicationTabContent">
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

      <!-- User Tab -->
      <div class="tab-pane fade" id="pane-user" role="tabpanel">
        <div id="userContent">
          <!-- User information will be loaded here via AJAX -->
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

<!-- Modal for View Notice -->
<div class="modal fade" id="viewNoticeModal" tabindex="-1" aria-labelledby="viewNoticeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-bottom" style="background-color: #EFEFEA !important;">
        <h5 class="modal-title fw-bold text-primary" id="viewNoticeModalLabel">
          <i class="bx bx-file me-2"></i> View Notice
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewNoticeModalBody">
        <p class="text-muted mb-0">Loading...</p>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-label-primary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Scheduling Presentation -->
<div class="modal fade" id="schedulePresentationModal" tabindex="-1" aria-labelledby="schedulePresentationModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-bottom" style="background-color: #EFEFEA !important;">
        <h5 class="modal-title fw-bold text-primary" id="schedulePresentationModalLabel">
          <i class="bx bx-calendar me-2"></i> Schedule Presentation
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="schedulePresentationForm">
        <div class="modal-body">
          <input type="hidden" id="scheduleApplicationId" name="application_id">
          
          <div class="mb-3">
            <label for="presentationDate" class="form-label">Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="presentationDate" name="presentation_date" required>
            <div class="invalid-feedback"></div>
          </div>
          
          <div class="mb-3">
            <label for="presentationTime" class="form-label">Time <span class="text-danger">*</span></label>
            <input type="time" class="form-control" id="presentationTime" name="presentation_time" required>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-label-primary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-check me-1"></i> Schedule
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  
  const stallId = {{ $stall->stallID }};
  const dataUrl = "{{ route('admins.prospective-tenants.applications.data', $stall->stallID) }}";
  
  var table = $('#applicationsTable').DataTable({
    ajax: dataUrl,
    columns: [
      {data:null, className:'control', orderable:false, render:()=>''},
      {data:null, orderable:false, className:'text-center', render:function(d){
        return `<input type="checkbox" class="form-check-input row-checkbox" data-id="${d.applicationID}">`;
      }},
      {data:'number', orderable:true},
      {data:'name', orderable:true},
      {data:null, orderable:false, className:'text-center', render:function(d){
        if (d.hasRequirements) {
          return `<i class="bx bx-paperclip requirement-icon has-requirements" title="Requirements submitted (${d.requirementCount})" data-app-id="${d.applicationID}" style="cursor: pointer;"></i>`;
        } else {
          return `<i class="bx bx-paperclip requirement-icon no-requirements" title="No requirements submitted" data-app-id="${d.applicationID}" style="cursor: pointer;"></i>`;
        }
      }},
      {data:'status', orderable:true, render:function(d){
        let cls = 'bg-label-primary';
        if (d === 'Proposal Received') cls = 'bg-label-info';
        if (d === 'Presentation Scheduled') cls = 'bg-label-warning';
        if (d === 'Pending Submission') cls = 'bg-label-secondary';
        if (d === 'Proposal Rejected') cls = 'bg-label-danger';
        if (d === 'Requirements Received') cls = 'bg-label-success';
        if (d === 'Approved') cls = 'bg-label-success';
        if (d === 'Withdrawn') cls = 'bg-label-secondary';
        const textColor = d === 'Withdrawn' ? ' style="color: #000000 !important;"' : '';
        return `<span class="badge rounded-pill ${cls}"${textColor}>${d}</span>`;
      }},
      {data:null, orderable:false, className:'text-center', render:function(d){
        const status = d.status || '';
        let menuItems = '';
        
        // Build menu items based on status
        if (status === 'Proposal Received') {
          menuItems = `
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-schedule-presentation" data-id="${d.applicationID}">
                <i class="bx bx-calendar me-1"></i> Schedule Presentation
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a href="javascript:;" class="dropdown-item text-danger btn-delete-proposal" data-id="${d.applicationID}">
                <i class="bx bx-trash me-1"></i> Delete Proposal
              </a>
            </li>
          `;
        } else if (status === 'Presentation Scheduled') {
          menuItems = `
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-approve" data-id="${d.applicationID}">
                <i class="bx bx-check me-1"></i> Approve
              </a>
            </li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-reject" data-id="${d.applicationID}">
                <i class="bx bx-x me-1"></i> Reject
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-reschedule" data-id="${d.applicationID}">
                <i class="bx bx-time me-1"></i> Reschedule
              </a>
            </li>
          `;
        } else if (status === 'Pending Submission') {
          menuItems = `
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-send-reminder" data-id="${d.applicationID}">
                <i class="bx bx-bell me-1"></i> Send Reminder
              </a>
            </li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-withdraw" data-id="${d.applicationID}">
                <i class="bx bx-x me-1"></i> Withdraw
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-view-notice" data-id="${d.applicationID}">
                <i class="bx bx-file me-1"></i> View Notice
              </a>
            </li>
          `;
        } else if (status === 'Proposal Rejected') {
          menuItems = `
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-view-notice" data-id="${d.applicationID}">
                <i class="bx bx-file me-1"></i> View Notice
              </a>
            </li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-reopen" data-id="${d.applicationID}">
                <i class="bx bx-refresh me-1"></i> Reopen Submission
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a href="javascript:;" class="dropdown-item text-danger btn-delete-proposal" data-id="${d.applicationID}">
                <i class="bx bx-trash me-1"></i> Delete Proposal
              </a>
            </li>
          `;
        } else if (status === 'Requirements Received') {
          menuItems = `
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-mark-tenant" data-id="${d.applicationID}">
                <i class="bx bx-user-check me-1"></i> Mark as Tenant
              </a>
            </li>
          `;
        } else if (status === 'Approved') {
          menuItems = `
            <li>
              <a href="javascript:;" class="dropdown-item text-warning btn-remove-tenant" data-id="${d.applicationID}">
                <i class="bx bx-user-x me-1"></i> Remove tenant
              </a>
            </li>
          `;
        } else if (status === 'Withdrawn') {
          menuItems = `
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-reopen" data-id="${d.applicationID}">
                <i class="bx bx-refresh me-1"></i> Reopen Submission
              </a>
            </li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-view-notice" data-id="${d.applicationID}">
                <i class="bx bx-file me-1"></i> View Notice
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a href="javascript:;" class="dropdown-item text-danger btn-delete-proposal" data-id="${d.applicationID}">
                <i class="bx bx-trash me-1"></i> Delete Proposal
              </a>
            </li>
          `;
        }
        
        return `
          <div class="dropdown">
            <button class="btn btn-sm btn-light btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              ${menuItems}
            </ul>
          </div>
        `;
      }}
    ],
    order:[[2,'asc']], // Sort by # column (index 2 after checkbox columns)
    pageLength:10,
    responsive:true,
    dom: 'lrtip', // l = length, r = processing, t = table, i = info, p = pagination (no search - using card search)
    language: {
      lengthMenu: "Show _MENU_ entries"
    },
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
  });

  // Replace native select with Bootstrap dropdown (like stalls/users modules)
  function replaceLengthSelectWithDropdown() {
    const $lengthWrapper = $('.dataTables_length');
    const $nativeSelect = $lengthWrapper.find('select');
    const $label = $lengthWrapper.find('label');
    
    if ($lengthWrapper.length > 0 && !$lengthWrapper.data('custom-replaced')) {
      const currentValue = $nativeSelect.val();
      
      // Get the label text (e.g., "Show" from "Show _MENU_ entries")
      let labelText = '';
      if ($label.length > 0) {
        // Get text nodes from label, excluding the select element
        const labelClone = $label.clone();
        labelClone.find('select').remove();
        labelText = labelClone.text().trim();
        // Extract just "Show" if it contains "Show" and "entries"
        if (labelText.includes('Show') && labelText.includes('entries')) {
          labelText = 'Show';
        } else if (labelText.includes('Show')) {
          labelText = 'Show';
        }
      }
      
      // Create custom dropdown HTML
      const $customDropdown = $(`
        <div class="dropdown d-inline-block">
          <button class="btn btn-label-primary dropdown-toggle entries-dropdown-toggle" type="button" data-bs-toggle="dropdown">
            ${currentValue === '-1' ? 'All' : currentValue}
          </button>
          <ul class="dropdown-menu entries-dropdown-menu">
            <li><a class="dropdown-item" href="#" data-value="10">10</a></li>
            <li><a class="dropdown-item" href="#" data-value="25">25</a></li>
            <li><a class="dropdown-item" href="#" data-value="50">50</a></li>
            <li><a class="dropdown-item" href="#" data-value="100">100</a></li>
            <li><a class="dropdown-item" href="#" data-value="-1">All</a></li>
          </ul>
        </div>
      `);
      
      // Get the action buttons group
      const $actionButtonsGroup = $('#actionButtonsGroup');
      
      // Hide the native select
      $nativeSelect.hide();
      
      // Update label to show "Show" text before dropdown
      if ($label.length > 0) {
        // Clear label content and rebuild with "Show" text + dropdown + "entries"
        $label.empty();
        if (labelText) {
          $label.append(document.createTextNode(labelText + ' '));
        } else {
          $label.append(document.createTextNode('Show '));
        }
        $label.append($customDropdown);
        $label.append(document.createTextNode(' entries'));
        
        // Ensure label is properly styled
        $label.css({
          'display': 'flex',
          'align-items': 'center',
          'gap': '0.5rem',
          'font-weight': '500',
          'color': '#201F23',
          'margin-bottom': '0'
        });
      } else {
        // If no label, just add dropdown after select
        $nativeSelect.after($customDropdown);
      }
      
      // Find the DataTables wrapper and the row that contains the length selector
      const $dataTablesWrapper = $lengthWrapper.closest('.dataTables_wrapper');
      if ($dataTablesWrapper.length > 0) {
        // Find the row that contains the length selector (usually the first row in the wrapper)
        const $lengthRow = $lengthWrapper.closest('.row, div').first();
        
        // If no row found, use the direct parent
        const $lengthParent = $lengthRow.length > 0 ? $lengthRow : $lengthWrapper.parent();
        
        // Make the parent a flex container with space-between
        $lengthParent.css({
          'display': 'flex',
          'justify-content': 'space-between',
          'align-items': 'center'
        });
        
        // Move action buttons group to the right side of the same row
        if ($actionButtonsGroup.length > 0) {
          $actionButtonsGroup.removeAttr('style').show();
          $lengthParent.append($actionButtonsGroup);
          
          // Setup export dropdown after buttons are moved
          setTimeout(function() {
            setupExportDropdown();
          }, 50);
        }
      }
      
      // Handle dropdown clicks
      $customDropdown.find('.dropdown-item').on('click', function(e) {
        e.preventDefault();
        const value = parseInt($(this).data('value'));
        const displayValue = value === -1 ? 'All' : value;
        $customDropdown.find('.dropdown-toggle').html(displayValue);
        table.page.len(value).draw();
      });
      
      // Mark as replaced
      $lengthWrapper.data('custom-replaced', true);
    }
  }

  // Replace dropdown after table initialization
  replaceLengthSelectWithDropdown();
  
  // Update dropdown when table redraws
  table.on('draw', function() {
    replaceLengthSelectWithDropdown();
  });
  
  // Store selected IDs across page changes
  let selectedApplicationIds = new Set();

  // Bind search bar to DataTable
  $('#applicationsSearch').on('keyup', function(){
    const query = $(this).val();
    table.search(query).draw();
  });

  // Clear search when input is cleared
  $('#applicationsSearch').on('input', function(){
    if ($(this).val() === '') {
      table.search('').draw();
    }
  });

  // Select All Checkbox Functionality
  $('#selectAllCheckbox').on('click', function() {
    const isChecked = $(this).is(':checked');
    $('.row-checkbox').each(function() {
      const applicationId = $(this).data('id');
      if (isChecked) {
        selectedApplicationIds.add(applicationId);
      } else {
        selectedApplicationIds.delete(applicationId);
      }
      $(this).prop('checked', isChecked);
    });
    updateArchiveButtonVisibility();
  });

  // Individual checkbox change
  $(document).on('change', '.row-checkbox', function() {
    const applicationId = $(this).data('id');
    if ($(this).is(':checked')) {
      selectedApplicationIds.add(applicationId);
    } else {
      selectedApplicationIds.delete(applicationId);
    }
    
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    updateArchiveButtonVisibility();
  });

  // Restore checkbox state after table redraw
  table.on('draw', function() {
    $('.row-checkbox').each(function() {
      const applicationId = $(this).data('id');
      $(this).prop('checked', selectedApplicationIds.has(applicationId));
    });
    
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    updateArchiveButtonVisibility();
  });

  // Update Archive button visibility
  function updateArchiveButtonVisibility() {
    if (selectedApplicationIds.size > 0) {
      $('#btnArchiveSelected').removeClass('d-none').show();
    } else {
      $('#btnArchiveSelected').addClass('d-none').hide();
    }
  }

  // Setup Export Dropdown
  function setupExportDropdown() {
    const $exportDropdown = $('#actionButtonsGroup .dropdown:has(.btn-label-primary.dropdown-toggle)');
    if ($exportDropdown.length) {
      const $exportButton = $exportDropdown.find('.btn-label-primary.dropdown-toggle');
      const $exportMenu = $exportDropdown.find('.dropdown-menu');
      
      // Style dropdown items to be black text (no hover color change)
      $exportMenu.find('.dropdown-item').css({
        'color': '#212529',
        'text-decoration': 'none'
      }).on('mouseenter', function() {
        $(this).css({
          'background-color': '#f8f9fa',
          'color': '#212529'
        });
      }).on('mouseleave', function() {
        $(this).css({
          'background-color': 'transparent',
          'color': '#212529'
        });
      });
      
      function setExportDropdownWidth() {
        const buttonWidth = $exportButton.outerWidth();
        if (buttonWidth) {
          $exportMenu.css({
            'width': buttonWidth + 'px',
            'min-width': buttonWidth + 'px',
            'max-width': buttonWidth + 'px'
          });
        }
      }
      
      // Set width initially and on window resize
      setExportDropdownWidth();
      $(window).on('resize', setExportDropdownWidth);
      
      // Update width when dropdown is shown (in case button width changed)
      $exportDropdown.on('shown.bs.dropdown', function() {
        setExportDropdownWidth();
      });
    }
  }

  // Archive Selected Functionality
  $('#btnArchiveSelected').on('click', function() {
    const selectedIds = Array.from(selectedApplicationIds);

    if (selectedIds.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Selection',
        text: 'Please select at least one application to archive.',
        toast: true,
        position: 'top',
        timer: 2000
      });
      return;
    }

    Swal.fire({
      title: 'Archive Applications?',
      text: `Are you sure you want to archive ${selectedIds.length} application(s)? This action cannot be undone.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: 'transparent',
      confirmButtonText: 'Yes, Archive',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        // TODO: Implement archive functionality
        Swal.fire({
          icon: 'success',
          title: 'Archived',
          text: 'Selected applications have been archived.',
          toast: true,
          position: 'top',
          timer: 2000
        });
        
        // Clear selections
        selectedApplicationIds.clear();
        $('.row-checkbox').prop('checked', false);
        $('#selectAllCheckbox').prop('checked', false);
        updateArchiveButtonVisibility();
        
        // Reload table
        table.ajax.reload();
      }
    });
  });

  // Export CSV
  $('#exportCsv').on('click', function(e) {
    e.preventDefault();
    // TODO: Implement CSV export
    Swal.fire({
      icon: 'info',
      title: 'Export CSV',
      text: 'CSV export functionality will be implemented.',
      toast: true,
      position: 'top',
      timer: 2000
    });
  });

  // Export PDF
  $('#exportPdf').on('click', function(e) {
    e.preventDefault();
    // TODO: Implement PDF export
    Swal.fire({
      icon: 'info',
      title: 'Export PDF',
      text: 'PDF export functionality will be implemented.',
      toast: true,
      position: 'top',
      timer: 2000
    });
  });

  // Add New Application button (placeholder)
  $('#btnAddApplication').on('click', function() {
    Swal.fire({
      icon: 'info',
      title: 'Add New Application',
      text: 'Add new application functionality will be implemented.',
      toast: true,
      position: 'top',
      timer: 2000
    });
  });

  // Format date helper
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

  // View application details (click on requirement icon or view button)
  function viewApplicationDetails(applicationId) {
    // Show loading state
    $('#stallContent').html(`
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `);
    $('#userContent').html(`
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
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('viewApplicationDrawer'));
    offcanvas.show();
    
    // Load application details via AJAX
    const detailsUrl = "{{ route('admins.prospective-tenants.application.details', ':id') }}".replace(':id', applicationId);
    $.get(detailsUrl, function(response) {
      if (response.success) {
        const app = response.application;
        const stall = response.stall;
        const user = response.user;
        const document = response.document;
        const proposalReqs = response.proposalRequirements || [];
        const tenancyReqs = response.tenancyRequirements || [];
        
        // Build requirements list with clickable fields that open modal
        let requirementsHtml = '';
        if (proposalReqs.length > 0) {
          requirementsHtml = '<ul class="list-unstyled mb-0">';
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
                      <button class="btn btn-sm requirement-view-btn" onclick="showRequirementFile('${escapedReqName}', '${escapedFileName}', '${escapedFileUrl}')">
                        <i class="bx bx-show me-1"></i> View
                      </button>
                      <a href="${escapedFileUrl}" download="${escapedFileName}" class="btn btn-sm requirement-download-btn">
                        <i class="bx bx-download me-1"></i> Download
                      </a>
                    </div>
                  ` : '<span class="text-muted small">No file uploaded</span>'}
                </div>
              </li>
            `;
          });
          requirementsHtml += '</ul>';
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
                  <i class="bx bx-hash text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Stall ID</div>
                  <div>${formatStallIdForApplication(stall.marketplace, stall.stallID)}</div>
                </div>
              </div>

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

              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                     style="width:36px; height:36px;">
                  <i class="bx bx-money text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Monthly Rental Fee</div>
                  <div>${stall.rentalFee ? '₱' + parseFloat(stall.rentalFee).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-'}</div>
                </div>
              </div>

              <div class="mb-0 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                     style="width:36px; height:36px;">
                  <i class="bx bx-user text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Rent By</div>
                  <div>-</div>
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
                  <div class="text-muted small">${user ? (() => {
                    const firstName = user.firstName || '';
                    const middleName = user.middleName || '';
                    const lastName = user.lastName || '';
                    let initials = '';
                    if (firstName) initials += firstName.charAt(0).toUpperCase();
                    if (middleName) initials += middleName.charAt(0).toUpperCase();
                    const initialsWithDots = initials ? initials.split('').join('.') + '.' : '';
                    return initialsWithDots && lastName ? `${initialsWithDots} ${lastName} applied on` : (lastName ? `${lastName} applied on` : 'Date Applied');
                  })() : 'Date Applied'}</div>
                  <div>${app.dateApplied ? formatDate(app.dateApplied) : '-'}</div>
                </div>
              </div>
            </div>
          </div>
        `;
        
        // User Tab Content - Match User module view drawer exactly
        let userContent = '';
        if (user) {
          const fullName = `${user.firstName || ''} ${user.middleName || ''} ${user.lastName || ''}`.trim();
          
          // Format User ID like User module
          function formatUserId(id) {
            if (!id) return '-';
            return 'USER-' + String(id).padStart(4, '0');
          }
          
          // Get user initials for profile circle
          let initials = '';
          if (fullName) {
            let parts = fullName.split(" ").filter(Boolean);
            if (parts.length > 0) {
              initials = parts[0][0];
              if (parts.length > 1) {
                initials += parts[parts.length - 1][0];
              }
            }
          }
          
          // Status badge styling
          let statusClass = 'bg-label-primary';
          if (user.userStatus === 'Pending') statusClass = 'bg-label-warning';
          if (user.userStatus === 'Deactivated') statusClass = 'bg-label-danger';
          
          userContent = `
            <div class="text-center mb-4">
              <!-- Profile -->
              <div class="bg-gray text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm"
                   style="width:80px; height:80px; font-size:1.5rem;">
                ${initials ? initials.toUpperCase() : '<i class="bx bx-user fs-1"></i>'}
              </div>

              <h5 class="mt-3 fw-bold">${fullName || '-'}</h5>
              <span class="badge rounded-pill px-3 py-2 ${statusClass}">${user.userStatus || '-'}</span>
            </div>

            <!-- Account Details -->
            <div class="card shadow border-0 mb-4">
              <div class="card-body">
                <h6 class="fw-bold text-uppercase text-muted fs-5 mb-3">
                  <i class="bx bx-id-card me-1 text-secondary fs-4"></i> Account Details
                </h6>
                
                <div class="mb-3 d-flex align-items-center">
                  <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                      style="width:36px; height:36px;">
                    <i class="bx bx-hash text-white fs-5"></i>
                  </div>
                  <div>
                    <div class="text-muted small">User ID</div>
                    <div>${formatUserId(user.id)}</div>
                  </div>
                </div>

                <div class="mb-3 d-flex align-items-center">
                  <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                      style="width:36px; height:36px;">
                    <i class="bx bx-user-pin text-white fs-5"></i>
                  </div>
                  <div>
                    <div class="text-muted small">Role</div>
                    <div>${user.role || '-'}</div>
                  </div>
                </div>

                <div class="mb-3 d-flex align-items-center">
                  <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                       style="width:36px; height:36px;">
                    <i class="bx bx-envelope text-white fs-5"></i>
                  </div>
                  <div>
                    <div class="text-muted small">Email</div>
                    <div>${user.email || '-'}</div>
                  </div>
                </div>

                <div class="mb-3 d-flex align-items-center">
                  <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                       style="width:36px; height:36px;">
                    <i class="bx bx-phone text-white fs-5"></i>
                  </div>
                  <div>
                    <div class="text-muted small">Contact</div>
                    <div>${user.contactNo || '-'}</div>
                  </div>
                </div>

                <div class="mb-3 d-flex align-items-start">
                  <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                       style="width:36px; height:36px;">
                    <i class="bx bx-home text-white fs-5"></i>
                  </div>
                  <div>
                    <div class="text-muted small">Home Address</div>
                    <div>${user.homeAddress || '-'}</div>
                  </div>
                </div>

                <div class="mb-3 d-flex align-items-center">
                  <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                       style="width:36px; height:36px;">
                    <i class="bx bx-calendar text-white fs-5"></i>
                  </div>
                  <div>
                    <div class="text-muted small">Birthdate</div>
                    <div>${user.birthDate ? (() => {
                      try {
                        const date = new Date(user.birthDate);
                        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                      } catch(e) {
                        return user.birthDate || '-';
                      }
                    })() : '-'}</div>
                  </div>
                </div>

                ${user.userStatus === 'Deactivated' ? `
                <div class="mb-3 d-flex align-items-center">
                  <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                      style="width:36px; height:36px;">
                    <i class="bx bx-comment text-white fs-5"></i>
                  </div>
                  <div>
                    <div class="text-muted small">Deactivation Reason</div>
                    <div class="fw-medium">${user.customReason || '-'}</div>
                  </div>
                </div>
                ` : ''}

              </div>
            </div>

            <!-- System Info -->
            <div class="card shadow border-0">
              <div class="card-body">
                <h6 class="fw-bold text-uppercase text-muted fs-5 mb-3">
                  <i class="bx bx-cog me-1 text-secondary fs-4"></i> System Info
                </h6>

                <div class="d-flex align-items-center">
                  <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                       style="width:36px; height:36px;">
                    <i class="bx bx-time text-white fs-5"></i>
                  </div>
                  <div>
                    <div class="text-muted small">Created</div>
                    <div>${user.created_at ? (() => {
                      try {
                        const date = new Date(user.created_at);
                        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                      } catch(e) {
                        return user.created_at || '-';
                      }
                    })() : '-'}</div>
                  </div>
                </div>
              </div>
            </div>
          `;
        } else {
          userContent = `
            <div class="text-center text-muted py-5">
              <i class="bx bx-info-circle fs-1 mb-3"></i>
              <p>No user information available.</p>
            </div>
          `;
        }
        
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
        $('#userContent').html(userContent);
        $('#requirementsContent').html(requirementsContent);
      } else {
        $('#stallContent').html(`
          <div class="alert alert-danger">
            <i class="bx bx-error-circle me-2"></i> Failed to load application details.
          </div>
        `);
        $('#userContent').html(`
          <div class="alert alert-danger">
            <i class="bx bx-error-circle me-2"></i> Failed to load application details.
          </div>
        `);
        $('#requirementsContent').html(`
          <div class="alert alert-danger">
            <i class="bx bx-error-circle me-2"></i> Failed to load application details.
          </div>
        `);
      }
    }).fail(function(xhr, status, error) {
      console.error('Error loading application details:', status, error, xhr.responseText);
      let errorMsg = 'Error loading application details. Please try again.';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMsg = xhr.responseJSON.message;
      }
      $('#stallContent').html(`
        <div class="alert alert-danger">
          <i class="bx bx-error-circle me-2"></i> ${errorMsg}
        </div>
      `);
      $('#userContent').html(`
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
  }
  
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

  // Click on requirement icon
  $(document).on('click', '.requirement-icon', function() {
    const applicationId = $(this).data('app-id');
    if (applicationId) {
      viewApplicationDetails(applicationId);
    }
  });

  // View application button
  $(document).on('click', '.btn-view', function() {
    const applicationId = $(this).data('id');
    viewApplicationDetails(applicationId);
  });

  // Schedule Presentation
  $(document).on('click', '.btn-schedule-presentation', function() {
    const applicationId = $(this).data('id');
    
    // Function to show the modal
    function showScheduleModal() {
      // Set minimum date to today
      const today = new Date().toISOString().split('T')[0];
      $('#presentationDate').attr('min', today);
      $('#presentationDate').val('');
      $('#presentationTime').val('');
      $('#scheduleApplicationId').val(applicationId);
      
      // Reset form validation
      $('#schedulePresentationForm')[0].reset();
      $('#schedulePresentationForm').removeClass('was-validated');
      $('.invalid-feedback').text('');
      $('.is-invalid').removeClass('is-invalid');
      
      // Get modal element
      const modalElement = document.getElementById('schedulePresentationModal');
      
      // Remove any existing modal instances
      const existingModal = bootstrap.Modal.getInstance(modalElement);
      if (existingModal) {
        existingModal.dispose();
      }
      
      const modal = new bootstrap.Modal(modalElement, {
        backdrop: true,
        keyboard: true
      });
      
      // Ensure modal has proper z-index and is clickable
      $(modalElement).css({
        'z-index': '1060',
        'pointer-events': 'auto'
      });
      
      // Show modal
      modal.show();
      
      // Set z-index after modal is shown (with delay to ensure it's applied)
      setTimeout(function() {
        $(modalElement).css({
          'z-index': '1060',
          'pointer-events': 'auto'
        });
        $(modalElement).find('.modal-dialog, .modal-content').css('pointer-events', 'auto');
        
        // Ensure backdrop is behind modal
        $('.modal-backdrop').last().css('z-index', '1059');
      }, 100);
      
      // Also set on shown event
      $(modalElement).off('shown.bs.modal').on('shown.bs.modal', function() {
        $(this).css({
          'z-index': '1060',
          'pointer-events': 'auto'
        });
        $(this).find('.modal-dialog, .modal-content').css('pointer-events', 'auto');
        
        // Ensure backdrop is behind modal
        $('.modal-backdrop').last().css('z-index', '1059');
      });
    }
    
    // Close any open offcanvas drawers first
    const openOffcanvas = document.querySelector('.offcanvas.show');
    if (openOffcanvas) {
      const offcanvasInstance = bootstrap.Offcanvas.getInstance(openOffcanvas);
      if (offcanvasInstance) {
        // Wait for offcanvas to close before showing modal
        $(openOffcanvas).one('hidden.bs.offcanvas', function() {
          setTimeout(showScheduleModal, 100);
        });
        offcanvasInstance.hide();
      } else {
        showScheduleModal();
      }
    } else {
      showScheduleModal();
    }
  });
  
  // Handle schedule presentation form submission
  $('#schedulePresentationForm').on('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const applicationId = $('#scheduleApplicationId').val();
    const presentationDate = $('#presentationDate').val();
    const presentationTime = $('#presentationTime').val();
    
    // Validate date is not in the past
    const selectedDate = new Date(presentationDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate < today) {
      $('#presentationDate').addClass('is-invalid');
      $('#presentationDate').next('.invalid-feedback').text('Date cannot be in the past.');
      return;
    }
    
    // Validate form
    if (!form.checkValidity()) {
      e.stopPropagation();
      form.classList.add('was-validated');
      return;
    }
    
    // Disable submit button
    const submitBtn = $(form).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Scheduling...');
    
    // Submit via AJAX
    $.ajax({
      url: "{{ route('admins.prospective-tenants.schedule-presentation', ':id') }}".replace(':id', applicationId),
      method: 'POST',
      data: {
        presentation_date: presentationDate,
        presentation_time: presentationTime,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        if (response.success) {
          // Close modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('schedulePresentationModal'));
          modal.hide();
          
          // Show success message
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: response.message || 'Presentation scheduled successfully.',
            toast: true,
            position: 'top',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 3000,
            timerProgressBar: true
          });
          
          // Reload table to reflect status change
          table.ajax.reload();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.message || 'Failed to schedule presentation.',
            toast: true,
            position: 'top',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 3000,
            timerProgressBar: true
          });
        }
      },
      error: function(xhr) {
        let errorMsg = 'Failed to schedule presentation. Please try again.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
          // Handle validation errors
          const errors = xhr.responseJSON.errors;
          if (errors.presentation_date) {
            $('#presentationDate').addClass('is-invalid');
            $('#presentationDate').next('.invalid-feedback').text(errors.presentation_date[0]);
          }
          if (errors.presentation_time) {
            $('#presentationTime').addClass('is-invalid');
            $('#presentationTime').next('.invalid-feedback').text(errors.presentation_time[0]);
          }
          errorMsg = 'Please correct the errors and try again.';
        }
        
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: errorMsg,
          toast: true,
          position: 'top',
          showConfirmButton: false,
          showCloseButton: true,
          timer: 3000,
          timerProgressBar: true
        });
      },
      complete: function() {
        submitBtn.prop('disabled', false).html('<i class="bx bx-check me-1"></i> Schedule');
      }
    });
  });
  
  // Validate date on change
  $('#presentationDate').on('change', function() {
    const selectedDate = new Date($(this).val());
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate < today) {
      $(this).addClass('is-invalid');
      $(this).next('.invalid-feedback').text('Date cannot be in the past.');
    } else {
      $(this).removeClass('is-invalid');
      $(this).next('.invalid-feedback').text('');
    }
  });

  // Approve
  $(document).on('click', '.btn-approve', function() {
    const applicationId = $(this).data('id');
    Swal.fire({
      title: 'Approve Application?',
      text: 'This will mark the application as Approved.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Approve',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#198754'
    }).then((result) => {
      if (!result.isConfirmed) return;
      $.ajax({
        url: "{{ route('admins.prospective-tenants.approve', ':id') }}".replace(':id', applicationId),
        method: 'POST',
        data: { _token: $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
          Swal.fire({
            icon: 'success',
            title: 'Approved',
            text: response.message || 'Application approved.',
            toast: true,
            position: 'top',
            timer: 3000,
            showConfirmButton: false
          });
          table.ajax.reload();
        },
        error: function(xhr) {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: xhr.responseJSON?.message || 'Failed to approve application.',
            toast: true,
            position: 'top',
            timer: 3000,
            showConfirmButton: false
          });
        }
      });
    });
  });

  // Reject
  $(document).on('click', '.btn-reject', function() {
    const applicationId = $(this).data('id');
    Swal.fire({
      title: 'Reject Application?',
      input: 'text',
      inputLabel: 'Reason (optional)',
      inputPlaceholder: 'Add a short reason',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Reject',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#dc3545'
    }).then((result) => {
      if (!result.isConfirmed) return;
      $.ajax({
        url: "{{ route('admins.prospective-tenants.reject', ':id') }}".replace(':id', applicationId),
        method: 'POST',
        data: {
          reason: result.value || '',
          _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          Swal.fire({
            icon: 'success',
            title: 'Rejected',
            text: response.message || 'Application rejected.',
            toast: true,
            position: 'top',
            timer: 3000,
            showConfirmButton: false
          });
          table.ajax.reload();
        },
        error: function(xhr) {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: xhr.responseJSON?.message || 'Failed to reject application.',
            toast: true,
            position: 'top',
            timer: 3000,
            showConfirmButton: false
          });
        }
      });
    });
  });

  // Reschedule
  $(document).on('click', '.btn-reschedule', function() {
    const applicationId = $(this).data('id');
    Swal.fire({
      icon: 'info',
      title: 'Reschedule Presentation',
      text: 'Reschedule presentation functionality will be implemented.',
      toast: true,
      position: 'top',
      timer: 2000
    });
  });

  // Send Reminder
  $(document).on('click', '.btn-send-reminder', function() {
    const applicationId = $(this).data('id');
    Swal.fire({
      icon: 'info',
      title: 'Send Reminder',
      text: 'Send reminder functionality will be implemented.',
      toast: true,
      position: 'top',
      timer: 2000
    });
  });

  // Withdraw (admin side)
  $(document).on('click', '.btn-withdraw', function() {
    const applicationId = $(this).data('id');
    Swal.fire({
      icon: 'info',
      title: 'Withdraw Application',
      text: 'Withdraw application functionality will be implemented.',
      toast: true,
      position: 'top',
      timer: 2000
    });
  });

  // View Notice - fetch application details and show notice (noticeType, noticeDate, remarks) in modal
  $(document).on('click', '.btn-view-notice', function() {
    const applicationId = $(this).data('id');
    const detailsUrl = "{{ route('admins.prospective-tenants.application.details', ':id') }}".replace(':id', applicationId);
    const $modal = $('#viewNoticeModal');
    const $body = $('#viewNoticeModalBody');
    $body.html('<p class="text-muted mb-0">Loading...</p>');
    // Move modal to body so it is not trapped by stacking context (fixes greyed-out unclickable modal)
    if ($modal.parent().length && !$modal.parent().is('body')) {
      $modal.appendTo('body');
    }
    const viewNoticeModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('viewNoticeModal'));
    viewNoticeModal.show();
    $.ajax({
      url: detailsUrl,
      method: 'GET',
      success: function(response) {
        if (!response.success || !response.application) {
          $body.html('<p class="text-danger mb-0">Could not load notice.</p>');
          return;
        }
        const app = response.application;
        const noticeType = app.noticeType || '';
        const noticeDate = app.noticeDate ? new Date(app.noticeDate).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' }) : '';
        const remarks = app.remarks || '';
        if (!noticeType && !noticeDate && !remarks) {
          $body.html('<p class="text-muted mb-0">No notice on file for this application.</p>');
          return;
        }
        let html = '';
        if (noticeType) {
          html += `<p class="mb-2"><strong>Notice type:</strong> ${escapeHtml(noticeType)}</p>`;
        }
        if (noticeDate) {
          html += `<p class="mb-2"><strong>Date:</strong> ${escapeHtml(noticeDate)}</p>`;
        }
        if (remarks) {
          html += `<p class="mb-0"><strong>Details / Reason:</strong></p><p class="mt-1 mb-0">${escapeHtml(remarks)}</p>`;
        }
        $body.html(html || '<p class="text-muted mb-0">No notice on file.</p>');
      },
      error: function() {
        $body.html('<p class="text-danger mb-0">Failed to load notice. Please try again.</p>');
      }
    });
  });
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // Mark as Tenant
  $(document).on('click', '.btn-mark-tenant', function() {
    const applicationId = $(this).data('id');
    Swal.fire({
      icon: 'info',
      title: 'Mark as Tenant',
      text: 'Mark as tenant functionality will be implemented.',
      toast: true,
      position: 'top',
      timer: 2000
    });
  });

  // Reopen Submission - allow withdrawn application to be resubmitted
  $(document).on('click', '.btn-reopen', function() {
    const applicationId = $(this).data('id');
    const reopenUrl = "{{ route('admins.prospective-tenants.reopen', ':id') }}".replace(':id', applicationId);
    Swal.fire({
      title: 'Reopen submission?',
      text: 'This will change the status to "Proposal Received" so the prospect can submit again.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#7F9267',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, reopen'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: reopenUrl,
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Reopened',
              text: response.message || 'Application reopened.',
              toast: true,
              position: 'top',
              showConfirmButton: false,
              timer: 2500
            }).then(function() {
              table.ajax.reload(null, false);
            });
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to reopen application.'
            });
          }
        });
      }
    });
  });

  // Delete Proposal - remove rejected/withdrawn application from the table
  $(document).on('click', '.btn-delete-proposal', function() {
    const applicationId = $(this).data('id');
    const deleteUrl = "{{ route('admins.prospective-tenants.application.delete', ':id') }}".replace(':id', applicationId);
    Swal.fire({
      title: 'Delete proposal?',
      text: 'This will permanently remove this application from the list. This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: deleteUrl,
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Deleted',
              text: response.message || 'Proposal removed.',
              toast: true,
              position: 'top',
              showConfirmButton: false,
              timer: 2500
            }).then(function() {
              table.ajax.reload(null, false);
            });
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete proposal.'
            });
          }
        });
      }
    });
  });

  // Remove tenant - delete Approved application from table and archive linked contract
  $(document).on('click', '.btn-remove-tenant', function() {
    const applicationId = $(this).data('id');
    const removeUrl = "{{ route('admins.prospective-tenants.application.remove-tenant', ':id') }}".replace(':id', applicationId);
    Swal.fire({
      title: 'Remove tenant?',
      text: 'This will remove this application from the list and archive the lease for this stall if one exists. This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#f0ad4e',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, remove'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: removeUrl,
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Tenant removed',
              text: response.message || 'Removed from the list.',
              toast: true,
              position: 'top',
              showConfirmButton: false,
              timer: 2500
            }).then(function() {
              table.ajax.reload(null, false);
            });
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to remove tenant.'
            });
          }
        });
      }
    });
  });

  // Prevent table scrollbar when dropdown menu opens
  $(document).on('show.bs.dropdown', '#applicationsTable .dropdown', function(e) {
    // Ensure table-responsive doesn't create scrollbars
    const $tableResponsive = $(this).closest('.table-responsive');
    if ($tableResponsive.length) {
      $tableResponsive.css({
        'overflow-x': 'auto !important',
        'overflow-y': 'visible !important'
      });
    }
    
    // Also check parent card-body
    const $cardBody = $(this).closest('.card-body');
    if ($cardBody.length) {
      $cardBody.css({
        'overflow': 'visible !important'
      });
    }
  });
  
  // Reset overflow when dropdown closes
  $(document).on('hidden.bs.dropdown', '#applicationsTable .dropdown', function(e) {
    const $tableResponsive = $(this).closest('.table-responsive');
    if ($tableResponsive.length) {
      $tableResponsive.css({
        'overflow-x': 'auto !important',
        'overflow-y': 'visible !important'
      });
    }
    
    // Also reset parent card-body
    const $cardBody = $(this).closest('.card-body');
    if ($cardBody.length) {
      $cardBody.css({
        'overflow': ''
      });
    }
  });
});
</script>
@endpush


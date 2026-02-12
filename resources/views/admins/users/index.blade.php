@extends('layouts.admin_app')

@section('title','Users')
@section('page-title', 'User Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <!-- Search Bar -->
    <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
        <div class="position-relative w-100">
            <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 1; color: #7F9267;">
                <i class="bx bx-search fs-5"></i>
            </span>
            <input type="text" id="usersSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search" aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;">
        </div>
    </div>
    <!-- /Search Bar -->
    
    <!-- Add New User Button -->
    <div class="d-flex align-items-center">
        <button id="btnAddUser" class="btn btn-primary">
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
                <li><a class="dropdown-item" href="{{ route('admins.export.csv') }}">
                    <i class="fa-solid fa-file-text me-1"></i> CSV
                </a></li>
                <li><a class="dropdown-item" href="#" id="exportPdf">
                    <i class="fa-solid fa-file-pdf me-1"></i> PDF
                </a></li>
            </ul>
        </div>
    </div>
    <div class="table-responsive">
      <table id="usersTable" class="table table-hover mb-0">
        <thead>
          <tr>
            <th></th>
            <th class="text-center">
              <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Select All">
            </th>
            <th>#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<!-- Offcanvas View User -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="viewUserDrawer">
  <div class="offcanvas-header border-bottom bg-light">
    <h5 class="offcanvas-title d-flex align-items-center fw-bold text-primary">
      <i class="bx bx-user-circle me-2 fs-3"></i> USER INFORMATION
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <div class="text-center mb-4">
      <!-- Profile -->
      <div id="profileCircle" 
           class="bg-gray text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm"
           style="width:80px; height:80px; font-size:1.5rem;">
      </div>

      <h5 id="viewName" class="mt-3 fw-bold"></h5>
      <span id="viewStatus" class="badge rounded-pill px-3 py-2"></span>
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
            <div id="viewId"></div>
          </div>
        </div>

        <div class="mb-3 d-flex align-items-center">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
              style="width:36px; height:36px;">
            <i class="bx bx-user-pin text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Role</div>
            <div id="viewRole"></div>
          </div>
        </div>

        <div class="mb-3 d-flex align-items-center">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
               style="width:36px; height:36px;">
            <i class="bx bx-envelope text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Email</div>
            <div id="viewEmail"></div>
          </div>
        </div>

        <div class="mb-3 d-flex align-items-center">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
               style="width:36px; height:36px;">
            <i class="bx bx-phone text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Contact</div>
            <div id="viewContact"></div>
          </div>
        </div>

        <div class="mb-3 d-flex align-items-start">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
               style="width:36px; height:36px;">
            <i class="bx bx-home text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Home Address</div>
            <div id="viewAddress"></div>
          </div>
        </div>

        <div class="mb-3 d-flex align-items-center">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
               style="width:36px; height:36px;">
            <i class="bx bx-calendar text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Birthdate</div>
            <div id="viewBirthDate"></div>
          </div>
        </div>

        <div class="mb-3 d-flex align-items-center d-none" id="viewCustomReasonContainer">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
              style="width:36px; height:36px;">
            <i class="bx bx-comment text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Deactivation Reason</div>
            <div id="viewCustomReason" class="fw-medium">-</div>
          </div>
        </div>

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
            <div id="viewCreated"></div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Offcanvas Add User -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addUserDrawer">
  <div class="offcanvas-header border-bottom bg-light">
    <h5 class="offcanvas-title d-flex align-items-center fw-bold text-primary">
      <i class="bx bx-user-plus me-2 fs-4"></i> ADD NEW USER
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <form id="addUserForm" novalidate>
      @csrf
      <div class="row g-3 mt-1">
        <div class="col-12">
          <label class="form-label">Role <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-user-pin"></i></span>
            <select name="role" class="form-select">
              <option value="">Select Role</option>
              <option value="Lease Manager">Lease Manager</option>
              <option value="Tenant">Tenant</option>
            </select>
          </div>
          <div class="invalid-feedback d-block" data-error="role"></div>
        </div>

        <div class="col-12">
          <label class="form-label">First Name <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-user"></i></span>
            <input type="text" name="firstName" class="form-control" placeholder="Enter first name">
          </div>
          <div class="invalid-feedback d-block" data-error="firstName"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Middle Name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-user"></i></span>
            <input type="text" name="middleName" class="form-control" placeholder="Enter middle name">
          </div>
          <div class="invalid-feedback d-block" data-error="middleName"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Last Name <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-user"></i></span>
            <input type="text" name="lastName" class="form-control" placeholder="Enter last name">
          </div>
          <div class="invalid-feedback d-block" data-error="lastName"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Email <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
            <input type="email" name="email" class="form-control" placeholder="example@email.com">
          </div>
          <div class="invalid-feedback d-block" data-error="email"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Contact Number <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-phone"></i></span>
            <input type="text" name="contactNo" class="form-control" placeholder="09XXXXXXXXX">
          </div>
          <div class="invalid-feedback d-block" data-error="contactNo"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Home Address <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-home"></i></span>
            <textarea name="homeAddress" class="form-control" rows="2" placeholder="Enter home address"></textarea>
          </div>
          <div class="invalid-feedback d-block" data-error="homeAddress"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Birthdate <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
            <input type="date" name="birthDate" class="form-control">
          </div>
          <div class="invalid-feedback d-block" data-error="birthDate"></div>
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-label-primary" data-bs-dismiss="offcanvas">
          <i class="bx bx-x me-1"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="bx bx-save me-1"></i> Save User
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Offcanvas Edit User -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editUserDrawer">
  <div class="offcanvas-header border-bottom bg-light">
    <h5 class="offcanvas-title d-flex align-items-center fw-bold text-primary">
      <i class="bx bx-edit me-2 fs-3"></i> EDIT USER
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <form id="editUserForm" novalidate>
      @csrf
      @method('PUT')
      <input type="hidden" name="id" id="editUserId">

      <div class="row g-3 mt-1">
        <div class="col-12">
          <label class="form-label">Status</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-badge-check"></i></span>
            <select name="userStatus" id="editStatus" class="form-select">
              <option value="">Select Status</option>
              <option value="Active">Active</option>
              <option value="Pending">Pending</option>
              <option value="Deactivated">Deactivated</option>
            </select>
          </div>
          <div class="invalid-feedback d-block" data-error="userStatus"></div>
        </div>

        <!-- (Hidden initially) -->
        <div class="col-12 d-none" id="customReasonContainer">
          <label class="form-label">Deactivation Reason <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-comment"></i></span>
            <textarea name="customReason" id="editCustomReason" class="form-control" rows="3" placeholder="Enter reason for deactivating this account" required></textarea>
          </div>
          <div class="invalid-feedback d-block" data-error="customReason"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Role <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-user-pin"></i></span>
            <select name="role" id="editRole" class="form-select">
              <option value="">Select Role</option>
              <option value="Lease Manager">Lease Manager</option>
              <option value="Tenant">Tenant</option>
            </select>
          </div>
          <div class="invalid-feedback d-block" data-error="role"></div>
        </div>

        <div class="col-12">
          <label class="form-label">First Name <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-user"></i></span>
            <input type="text" name="firstName" id="editFirstName" class="form-control">
          </div>
          <div class="invalid-feedback d-block" data-error="firstName"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Middle Name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-user"></i></span>
            <input type="text" name="middleName" id="editMiddleName" class="form-control">
          </div>
          <div class="invalid-feedback d-block" data-error="middleName"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Last Name <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-user"></i></span>
            <input type="text" name="lastName" id="editLastName" class="form-control">
          </div>
          <div class="invalid-feedback d-block" data-error="lastName"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Email <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
            <input type="email" name="email" id="editEmail" class="form-control">
          </div>
          <div class="invalid-feedback d-block" data-error="email"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Contact Number <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-phone"></i></span>
            <input type="text" name="contactNo" id="editContact" class="form-control">
          </div>
          <div class="invalid-feedback d-block" data-error="contactNo"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Home Address <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-home"></i></span>
            <textarea name="homeAddress" id="editAddress" class="form-control" rows="2"></textarea>
          </div>
          <div class="invalid-feedback d-block" data-error="homeAddress"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Birthdate <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
            <input type="date" name="birthDate" id="editBirthDate" class="form-control">
          </div>
          <div class="invalid-feedback d-block" data-error="birthDate"></div>
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-label-primary" data-bs-dismiss="offcanvas">
          <i class="bx bx-x me-1"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="bx bx-save me-1"></i> Update User
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>
  /* Search input placeholder styling */
  #usersSearch::placeholder {
    color: rgba(127, 146, 103, 0.6) !important;
  }

  #usersSearch:focus {
    background-color: rgba(127, 146, 103, 0.15) !important;
    border-color: rgba(127, 146, 103, 0.4) !important;
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(127, 146, 103, 0.25) !important;
  }
</style>
@endpush

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css"/>
<link rel="stylesheet" href="{{ asset('sneat/assets/css/users-page-improvements.css') }}">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  var table = $('#usersTable').DataTable({
    ajax: "{{ route('admins.data') }}",
    columns: [
      {data:null, className:'control', orderable:false, render:()=>''},
      {data:null, orderable:false, className:'text-center', render:function(d){
        return `<input type="checkbox" class="form-check-input row-checkbox" data-id="${d.id}">`;
      }},
      {data:'id', orderable:true, render:function(data, type, row, meta){
        // For display: show sequential row number accounting for pagination
        if (type === 'display' || type === 'type') {
          const pageInfo = table.page.info();
          return pageInfo.start + meta.row + 1;
        }
        // For sorting: use the actual ID value
        return data;
      }},
      {data:null, orderable:true, render:d=>`${d.firstName ?? ''} ${d.middleName ?? ''} ${d.lastName ?? ''}`},
      {data:'email', orderable:true},
      {data:'role', orderable:true},
      {data:'userStatus', orderable:true, defaultContent:'Pending', render:function(d){
        let cls='bg-label-primary';
        if(d=='Pending') cls='bg-label-warning';
        if(d=='Deactivated') cls='bg-label-danger';
        return `<span class="badge rounded-pill ${cls}">${d}</span>`;
      }},
      {data:null, orderable:false, className:'text-center', render:function(d){
        let resetBtn = '';
        if (d.userStatus === 'Active') {
          resetBtn = `
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-reset-password" data-id="${d.id}">
                <i class="bx bx-key me-1"></i> Reset Password
              </a>
            </li>`;
        }

        return `
          <div class="dropdown">
            <button class="btn btn-sm btn-light btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a href="javascript:;" class="dropdown-item text-secondary btn-view" data-id="${d.id}">
                  <i class="bx bx-show me-1"></i> View
                </a>
              </li>
              <li>
                <a href="javascript:;" class="dropdown-item text-secondary btn-edit" data-id="${d.id}">
                  <i class="bx bx-edit me-1"></i> Edit
                </a>
              </li>
              ${resetBtn}
              <li><hr class="dropdown-divider"></li>
              <li>
                <a href="javascript:;" class="dropdown-item text-danger btn-delete" data-id="${d.id}">
                  <i class="bx bx-trash me-1"></i> Delete
                </a>
              </li>
            </ul>
          </div>`;
      }}
    ],
    order:[[2,'asc']], // Sort by # column (which uses ID for sorting) - column index 2
    pageLength:10,
    responsive:true,
    dom: 'lrtip', // l = length, r = processing, t = table, i = info, p = pagination (no search - using card search)
    language: {
      lengthMenu: "Show _MENU_ entries"
    },
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
  });

  // Bind search bar to DataTable
  $('#usersSearch').on('keyup', function(){
    const query = $(this).val();
    table.search(query).draw();
  });

  // Clear search when input is cleared
  $('#usersSearch').on('input', function(){
    if ($(this).val() === '') {
      table.search('').draw();
    }
  });

  // Replace native select with Bootstrap dropdown and align action buttons
  function replaceLengthSelectWithDropdown() {
    const $lengthWrapper = $('.dataTables_length');
    const $nativeSelect = $lengthWrapper.find('select');
    
    if ($lengthWrapper.length > 0 && !$lengthWrapper.data('custom-replaced')) {
      const currentValue = $nativeSelect.val();
      
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
      
      // Replace native select with custom dropdown
      $nativeSelect.hide().after($customDropdown);
      
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

  // Store selected IDs across page changes
  let selectedUserIds = new Set();

  // Select All Checkbox Functionality
  $('#selectAllCheckbox').on('click', function() {
    const isChecked = $(this).is(':checked');
    $('.row-checkbox').each(function() {
      const userId = $(this).data('id');
      if (isChecked) {
        selectedUserIds.add(userId);
      } else {
        selectedUserIds.delete(userId);
      }
      $(this).prop('checked', isChecked);
    });
    updateArchiveButtonVisibility();
  });

  // Individual checkbox change
  $(document).on('change', '.row-checkbox', function() {
    const userId = $(this).data('id');
    if ($(this).is(':checked')) {
      selectedUserIds.add(userId);
    } else {
      selectedUserIds.delete(userId);
    }
    
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    updateArchiveButtonVisibility();
  });

  // Restore checkbox state after table redraw
  table.on('draw', function() {
    $('.row-checkbox').each(function() {
      const userId = $(this).data('id');
      $(this).prop('checked', selectedUserIds.has(userId));
    });
    
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    updateArchiveButtonVisibility();
  });

  // Update Archive button visibility
  function updateArchiveButtonVisibility() {
    if (selectedUserIds.size > 0) {
      $('#btnArchiveSelected').removeClass('d-none').show();
    } else {
      $('#btnArchiveSelected').addClass('d-none').hide();
    }
  }

  // Archive Selected Functionality
  $('#btnArchiveSelected').on('click', function() {
    const selectedIds = Array.from(selectedUserIds);

    if (selectedIds.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Selection',
        text: 'Please select at least one user to archive.',
      });
      return;
    }

    Swal.fire({
      title: 'Archive Users?',
      text: `Are you sure you want to archive ${selectedIds.length} user(s)?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#7F9267',
      cancelButtonColor: 'transparent',
      cancelButtonText: 'Cancel',
      confirmButtonText: 'Yes, archive them!'
    }).then((result) => {
      if (result.isConfirmed) {
        // Send archive request
        $.ajax({
          url: '{{ route("admins.users.archive-multiple") }}',
          type: 'POST',
          data: {
            ids: selectedIds,
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Archived!',
              text: `${selectedIds.length} user(s) have been archived.`,
              timer: 2000,
              showConfirmButton: false,
              animation: false,
              showClass: {
                popup: ''
              },
              hideClass: {
                popup: ''
              }
            });
            // Clear selections
            selectedUserIds.clear();
            // Reload table
            table.ajax.reload();
            // Uncheck all
            $('#selectAllCheckbox').prop('checked', false);
            updateArchiveButtonVisibility();
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: xhr.responseJSON?.message || 'Failed to archive users. Please try again.',
              animation: false,
              showClass: {
                popup: ''
              },
              hideClass: {
                popup: ''
              }
            });
          }
        });
      }
    });
  });

  // PDF export
  $('#exportPdf').on('click', function(e){
        e.preventDefault();
        window.open("{{ route('admins.print') }}", '_blank');
  });

  // Date formatter
  function formatDate(dateStr) {
    if (!dateStr) return '-';
    let date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  }

  // User ID formatter - format as USER-000X
  function formatUserId(id) {
    if (!id) return '-';
    return 'USER-' + String(id).padStart(4, '0');
  }

  // View user details
  $(document).on('click', '.btn-view', function () {
    let id = $(this).data('id');
    $.get(`/users/${id}`, function (user) {
      let fullName = `${user.firstName ?? ''} ${user.middleName ?? ''} ${user.lastName ?? ''}`.trim();

      $('#viewName').text(fullName);
      $('#viewId').text(formatUserId(user.id));
      $('#viewEmail').text(user.email);
      $('#viewRole').text(user.role);
      $('#viewContact').text(user.contactNo ?? '-');
      $('#viewAddress').text(user.homeAddress ?? '-');
      $('#viewBirthDate').text(formatDate(user.birthDate));
      $('#viewCreated').text(formatDate(user.created_at));

      if(user.userStatus === 'Deactivated') {
          $('#viewCustomReason').text(user.customReason || '-');
          $('#viewCustomReasonContainer').removeClass('d-none');
      } else {
          $('#viewCustomReasonContainer').addClass('d-none');
      }

      let statusClass = 'bg-label-primary'; 
      if (user.userStatus === 'Pending') statusClass = 'bg-label-warning';
      if (user.userStatus === 'Deactivated') statusClass = 'bg-label-danger';

      $('#viewStatus')
        .attr('class', 'badge rounded-pill px-3 py-2 ' + statusClass)
        .text(user.userStatus ?? '-');

      let profileCircle = $('#profileCircle');
      profileCircle.empty();
      if (fullName) {
        let parts = fullName.split(" ").filter(Boolean);
        let initials = parts[0][0];
        if (parts.length > 1) {
          initials += parts[parts.length - 1][0];
        }
        profileCircle.text(initials.toUpperCase());
      } else {
        profileCircle.html('<i class="bx bx-user fs-1"></i>');
      }

      new bootstrap.Offcanvas('#viewUserDrawer').show();
    });
  });

  // Open Add Drawer
  $('#btnAddUser').on('click', function(){
    $('#addUserForm')[0].reset();
    $('[data-error]').text('');
    $('#addUserForm').find('.is-invalid').removeClass('is-invalid');
    $('#addUserForm').find('.input-group').removeClass('error-state');
    new bootstrap.Offcanvas('#addUserDrawer').show();
  });

  // Submit Add Form
  $('#addUserForm').on('submit', function(e){
    e.preventDefault();
    $('[data-error]').text('');
    $('#addUserForm').find('.is-invalid').removeClass('is-invalid');
    $('#addUserForm').find('.input-group').removeClass('error-state');

    let $form = $(this);
    let $btn = $form.find('button[type="submit"]').prop('disabled', true);

    $.post("{{ route('admins.store') }}", $form.serialize())
      .done(function(){
        bootstrap.Offcanvas.getInstance($('#addUserDrawer')).hide();
        table.ajax.reload();
        Swal.fire({
          icon: 'success',
          title: 'User added successfully!',
          toast: true,
          position: 'top',
          showConfirmButton: false,
          showCloseButton: true,
          timer: 2000,
          timerProgressBar:true,
          animation: false,
          showClass: {
            popup: ''
          },
          hideClass: {
            popup: ''
          }
        });
      })
      .fail(function(xhr){
        if(xhr.status === 422){
          let errors = xhr.responseJSON.errors || {};
          $.each(errors, function(field, messages){
            $('[data-error="'+field+'"]').text(messages[0]);
            $('[name="'+field+'"]').addClass('is-invalid');
            // Add error-state class to parent input-group
            $('[name="'+field+'"]').closest('.input-group').addClass('error-state');
          });
        } else {
          let errorMessage = xhr.responseJSON?.message || 'Something went wrong while adding user.';
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: errorMessage,
            toast: true,
            position: 'top',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 2000,
            timerProgressBar: true,
            animation: false,
            showClass: {
              popup: ''
            },
            hideClass: {
              popup: ''
            }
          });
        }
      })
      .always(function(){
        $btn.prop('disabled', false);
      });
  });

  // Open Edit Drawer
  // Show Custom Reason textbox if status is Deactivated
  $('#editStatus').on('change', function() {
    let status = $(this).val();
    if(status === 'Deactivated') {
      $('#customReasonContainer').removeClass('d-none');
    } else {
      $('#customReasonContainer').addClass('d-none');
      $('#editCustomReason').val('');
    }
  });

  // Pre-fill reason if editing existing deactivated/locked user
  $(document).on('click', '.btn-edit', function() {
    let id = $(this).data('id');
    // Clear any previous validation errors
    $('#editUserForm').find('.is-invalid').removeClass('is-invalid');
    $('#editUserForm').find('.input-group').removeClass('error-state');
    $('[data-error]').text('');
    
    $.get(`/users/${id}`, function(user){
      $('#editUserId').val(user.id);
      $('#editRole').val(user.role);
      $('#editFirstName').val(user.firstName);
      $('#editMiddleName').val(user.middleName);
      $('#editLastName').val(user.lastName);
      $('#editEmail').val(user.email);
      $('#editContact').val(user.contactNo ? user.contactNo.replace(/-/g,'') : '');
      $('#editAddress').val(user.homeAddress);
      $('#editBirthDate').val(user.birthDate ? user.birthDate.split('T')[0] : '');
      $('#editStatus').val(user.userStatus); 
      let allowedStatuses = [];

      if (user.userStatus === 'Active') {
          allowedStatuses = ['Active', 'Deactivated'];
      } else if (user.userStatus === 'Pending') {
          allowedStatuses = ['Pending', 'Deactivated'];
      } else if (user.userStatus === 'Deactivated') {
          allowedStatuses = ['Active', 'Deactivated'];
      }

      let $statusSelect = $('#editStatus');
      $statusSelect.find('option').each(function(){
          let val = $(this).val();
          if (val === "" || allowedStatuses.includes(val)) {
              $(this).show();
          } else {
              $(this).hide();
          }
      });

      // Show custom reason field if status is Deactivated
      if (user.userStatus === 'Deactivated') {
          $('#customReasonContainer').removeClass('d-none');
          $('#editCustomReason').val(user.customReason || '');
      } else {
          $('#customReasonContainer').addClass('d-none');
          $('#editCustomReason').val('');
      }

      $('[data-error]').text('');
      $('#editUserForm').find('.is-invalid').removeClass('is-invalid');
      $('#editUserForm').find('.input-group').removeClass('error-state');
      new bootstrap.Offcanvas('#editUserDrawer').show();
    });
  });

  // Submit Edit Form
  $('#editUserForm').on('submit', function(e){
    e.preventDefault();
    $('[data-error]').text('');
    $('#editUserForm').find('.is-invalid').removeClass('is-invalid');
    $('#editUserForm').find('.input-group').removeClass('error-state');

    let id = $('#editUserId').val();
    let $form = $(this);
    let $btn = $form.find('button[type="submit"]').prop('disabled', true);

    $.ajax({
      url: `/users/${id}`,
      method: 'PUT',
      data: $form.serialize(),
      success: function(){
        bootstrap.Offcanvas.getInstance($('#editUserDrawer')).hide();
        table.ajax.reload();
        Swal.fire({
          icon: 'success',
          title: 'User updated successfully!',
          toast: true,
          position: 'top',
          showConfirmButton: false,
          showCloseButton: true,
          timer: 2000,
          timerProgressBar: true,
          animation: false,
          showClass: {
            popup: ''
          },
          hideClass: {
            popup: ''
          }
        });
      },
      error: function(xhr){
        if(xhr.status === 422){
          let errors = xhr.responseJSON.errors || {};
          $.each(errors, function(field, messages){
            $('[data-error="'+field+'"]').text(messages[0]);
            $('#editUserForm [name="'+field+'"]').addClass('is-invalid');
            // Add error-state class to parent input-group
            $('#editUserForm [name="'+field+'"]').closest('.input-group').addClass('error-state');
          });
        } else {
          Swal.fire({
            icon:'error',
            title:'Error updating user!',
            toast: true,
            position: 'top',
            timer: 2000,
            showConfirmButton: false,
            showCloseButton: true,
            timerProgressBar: true,
            animation: false,
            showClass: {
              popup: ''
            },
            hideClass: {
              popup: ''
            }
          });
        }
      },
      complete: function(){
        $btn.prop('disabled', false);
      }
    });
  });

  // Delete User
  $(document).on('click', '.btn-delete', function(){
    let id = $(this).data('id');

    Swal.fire({
      title: 'Are you sure?',
      text: "This action cannot be undone!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: 'transparent',
      cancelButtonText: 'Cancel',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if(result.isConfirmed){
        $.ajax({
          url: `/users/${id}`,
          method: 'DELETE',
          data: {_token: $('meta[name="csrf-token"]').attr('content')},
          success: function(){
            table.ajax.reload();
            Swal.fire({
              icon: 'success',
              title: 'User deleted!',
              toast: true,
              position: 'top',
              timer: 2000,
              showConfirmButton: false,
              showCloseButton: true,
              timerProgressBar: true,
              animation: false,
              showClass: {
                popup: ''
              },
              hideClass: {
                popup: ''
              }
            });
          },
          error: function(xhr){
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete user. Please try again or contact support.';
            Swal.fire({
              icon:'error',
              title:'Error deleting user',
              text: msg,
              confirmButtonText: 'OK',
              showClass: { popup: '' },
              hideClass: { popup: '' }
            });
          }
        });
      }
    });
  });

  // Reset Password
  $(document).on('click', '.btn-reset-password', function(){
    let id = $(this).data('id');

    Swal.fire({
      title: 'Send Reset Link?',
      text: "This will email the user a password reset link.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#7F9267',
      cancelButtonColor: 'transparent',
      confirmButtonText: 'Yes, send it',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if(result.isConfirmed){
        $.ajax({
          url: `/users/${id}/reset-password`,
          type: 'POST',
          data: {_token: $('meta[name="csrf-token"]').attr('content')},
          success: function(res){
            Swal.fire({
              icon: 'success',
              title: 'Link Sent!',
              text: res.message,
              toast: true,
              position: 'top',
              showConfirmButton: false,
              timer: 4000,
              animation: false,
              showClass: {
                popup: ''
              },
              hideClass: {
                popup: ''
              }
            });
          },
          error: function(xhr){
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: xhr.responseJSON?.message || 'Something went wrong',
              toast: true,
              position: 'top',
              showConfirmButton: false,
              timer: 4000,
              animation: false,
              showClass: {
                popup: ''
              },
              hideClass: {
                popup: ''
              }
            });
          }
        });
      }
    });
  });

  // Format contact number as user types (09XXXXXXXXX format)
  function formatContactNumber(input) {
    let value = input.value.replace(/\D/g, ''); // Remove non-digits
    
    // Limit to 11 digits
    if (value.length > 11) {
      value = value.substring(0, 11);
    }
    
    // Ensure it starts with 09
    if (value.length > 0 && !value.startsWith('09')) {
      if (value.startsWith('9')) {
        value = '0' + value;
      } else if (value.startsWith('0') && value.length > 1 && value[1] !== '9') {
        value = '09' + value.substring(1);
      } else if (!value.startsWith('0')) {
        value = '09' + value;
      }
    }
    
    input.value = value;
    
    // Validate format
    const $input = $(input);
    const isValid = /^09\d{9}$/.test(value);
    
    if (value.length > 0 && !isValid) {
      $input.addClass('is-invalid');
      $input.closest('.input-group').addClass('error-state');
      $('[data-error="'+$input.attr('name')+'"]').text('Contact number must be in format 09XXXXXXXXX (11 digits starting with 09)');
    } else {
      $input.removeClass('is-invalid');
      $input.closest('.input-group').removeClass('error-state');
      $('[data-error="'+$input.attr('name')+'"]').text('');
    }
  }
  
  // Format name fields - capitalize first letter, trim spaces
  function formatNameField(input) {
    let value = input.value.trim();
    
    // Capitalize first letter of each word
    if (value.length > 0) {
      value = value.split(/\s+/).map(word => {
        if (word.length > 0) {
          return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        }
        return word;
      }).join(' ');
    }
    
    input.value = value;
  }
  
  // Validate email format in real-time
  function validateEmail(input) {
    const $input = $(input);
    const email = input.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email.length > 0 && !emailRegex.test(email)) {
      $input.addClass('is-invalid');
      $input.closest('.input-group').addClass('error-state');
      $('[data-error="'+$input.attr('name')+'"]').text('Please enter a valid email address');
    } else {
      $input.removeClass('is-invalid');
      $input.closest('.input-group').removeClass('error-state');
      $('[data-error="'+$input.attr('name')+'"]').text('');
    }
  }
  
  // Validate birthdate - must be 18+ years old
  function validateBirthdate(input) {
    const $input = $(input);
    const birthdate = input.value;
    
    if (birthdate) {
      const birthDate = new Date(birthdate);
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDiff = today.getMonth() - birthDate.getMonth();
      
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      
      if (age < 18) {
        $input.addClass('is-invalid');
        $input.closest('.input-group').addClass('error-state');
        $('[data-error="'+$input.attr('name')+'"]').text('User must be 18 years old or above');
      } else {
        $input.removeClass('is-invalid');
        $input.closest('.input-group').removeClass('error-state');
        $('[data-error="'+$input.attr('name')+'"]').text('');
      }
    }
  }
  
  // Trim address field
  function trimAddress(input) {
    input.value = input.value.trim();
  }
  
  // Set max date for birthdate (18 years ago from today)
  function setBirthdateMaxDate() {
    const today = new Date();
    const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
    const maxDateStr = maxDate.toISOString().split('T')[0];
    
    $('#addUserForm [name="birthDate"], #editUserForm [name="birthDate"]').attr('max', maxDateStr);
  }
  
  // Initialize max date for birthdate
  setBirthdateMaxDate();
  
  // Contact number formatting and validation
  $('#addUserForm, #editUserForm').on('input', '[name="contactNo"]', function() {
    formatContactNumber(this);
  });
  
  // Name fields formatting
  $('#addUserForm, #editUserForm').on('blur', '[name="firstName"], [name="middleName"], [name="lastName"]', function() {
    formatNameField(this);
  });
  
  // Email validation
  $('#addUserForm, #editUserForm').on('input blur', '[name="email"]', function() {
    validateEmail(this);
  });
  
  // Birthdate validation
  $('#addUserForm, #editUserForm').on('change', '[name="birthDate"]', function() {
    validateBirthdate(this);
  });
  
  // Address trimming
  $('#addUserForm, #editUserForm').on('blur', '[name="homeAddress"]', function() {
    trimAddress(this);
  });
  
  // Clear validation errors when user types/changes input (for other fields)
  $('#addUserForm, #editUserForm').on('input change', '.form-control, .form-select', function(){
    const $input = $(this);
    const fieldName = $input.attr('name');
    
    // Skip fields that have their own handlers
    if (['contactNo', 'email', 'birthDate'].includes(fieldName)) {
      return;
    }
    
    $input.removeClass('is-invalid');
    $input.closest('.input-group').removeClass('error-state');
    $('[data-error="'+fieldName+'"]').text('');
  });

  // Match Export dropdown width to button width and style dropdown items
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
  
  // setupExportDropdown will be called after buttons are moved in replaceLengthSelectWithDropdown
  
  // Prevent table scrollbar when dropdown menu opens
  $(document).on('show.bs.dropdown', '#usersTable .dropdown', function(e) {
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
  $(document).on('hidden.bs.dropdown', '#usersTable .dropdown', function(e) {
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

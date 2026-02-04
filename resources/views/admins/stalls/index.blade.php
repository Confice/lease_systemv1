@extends('layouts.admin_app')

@section('title','Stalls')
@section('page-title', 'Stall Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <!-- Search Bar -->
    <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
        <div class="position-relative w-100">
            <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 1; color: #7F9267;">
                <i class="bx bx-search fs-5"></i>
            </span>
            <input type="text" id="stallsSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search" aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;">
        </div>
    </div>
    <!-- /Search Bar -->
    <a href="{{ route('admins.marketplace.index') }}" class="btn btn-label-primary">
        <i class="bx bx-map me-1"></i> Go to Marketplace Map
    </a>
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
                <li><a class="dropdown-item" href="{{ route('admins.stalls.export.csv') }}">
                    <i class="fa-solid fa-file-text me-1"></i> CSV
                </a></li>
                <li><a class="dropdown-item" href="#" id="exportPdf">
                    <i class="fa-solid fa-file-pdf me-1"></i> PDF
                </a></li>
            </ul>
        </div>

        <!-- List of Requirements Button -->
        <button type="button" id="btnListRequirements" class="btn btn-label-primary">
            <i class="bx bx-clipboard me-1"></i> List of Requirements
        </button>
    </div>
    <div class="table-responsive">
      <table id="stallsTable" class="table table-hover mb-0">
        <thead>
          <tr>
            <th></th>
            <th class="text-center">
              <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Select All">
            </th>
            <th>#</th>
            <th>Stall</th>
            <th>Stall Name</th>
            <th>Marketplace</th>
            <th>Rent By</th>
            <th>Contract</th>
            <th>Status</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<!-- Offcanvas View Stall -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="viewStallDrawer">
  <div class="offcanvas-header border-bottom bg-light">
    <h5 class="offcanvas-title d-flex align-items-center fw-bold text-primary" id="viewDrawerTitle">
      <i class="bx bx-store-alt me-2 fs-3"></i> STALL INFORMATION
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <!-- Tabs outside drawer body -->
  <div class="border-bottom bg-light">
    <ul class="nav nav-pills px-3 py-2" role="tablist" id="viewStallTabs">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="view-tab-stall" data-bs-toggle="pill" data-bs-target="#view-pane-stall" type="button" role="tab">
          <i class="bx bx-store me-2"></i> Stall
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="view-tab-user" data-bs-toggle="pill" data-bs-target="#view-pane-user" type="button" role="tab">
          <i class="bx bx-user me-2"></i> User
        </button>
      </li>
      
    </ul>
  </div>

  <div class="offcanvas-body p-0" style="display: flex; flex-direction: column; height: calc(100vh - 70px);">
    <div class="tab-content overflow-auto px-4 pt-4" style="flex: 1; min-height: 0;">
      <!-- Stall Information Tab -->
      <div class="tab-pane fade show active" id="view-pane-stall" role="tabpanel">
    <div class="text-center mb-4">
      <div id="profileCircle" 
           class="bg-gray text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm"
           style="width:80px; height:80px; font-size:1.5rem;">
              <i class="bx bx-store fs-1"></i>
      </div>
      <h5 id="viewName" class="mt-3 fw-bold"></h5>
      <span id="viewStatus" class="badge rounded-pill px-3 py-2"></span>
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
            <div id="viewId"></div>
          </div>
        </div>

        <div class="mb-3 d-flex align-items-center">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
              style="width:36px; height:36px;">
            <i class="bx bx-map text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Marketplace</div>
            <div id="viewMarketplace"></div>
          </div>
        </div>

        <div class="mb-3 d-flex align-items-center">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
               style="width:36px; height:36px;">
            <i class="bx bx-ruler text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Size (sq. m.)</div>
            <div id="viewSize"></div>
          </div>
        </div>

        <div class="mb-3 d-flex align-items-center">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
               style="width:36px; height:36px;">
            <i class="bx bx-money text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Monthly Rental Fee</div>
            <div id="viewRentalFee"></div>
          </div>
        </div>

        <div class="mb-3 d-flex align-items-center">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
               style="width:36px; height:36px;">
            <i class="bx bx-user text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Rented By</div>
            <div id="viewRentedBy"></div>
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
            <div id="viewApplicationDeadline"></div>
          </div>
        </div>
        <div class="mb-0 d-flex align-items-start">
          <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
               style="width:36px; height:36px;">
            <i class="bx bx-calendar-check text-white fs-5"></i>
          </div>
          <div>
            <div class="text-muted small">Date of Stall Assignment</div>
            <div id="viewAssignmentDate"></div>
          </div>
        </div>
      </div>
    </div>
      </div>

      <!-- User Information Tab -->
      <div class="tab-pane fade" id="view-pane-user" role="tabpanel">
        <div id="viewUserContent">
          <!-- User information will be populated here -->
        </div>
      </div>

      <!-- Requirements Tab -->
      <div class="tab-pane fade" id="view-pane-requirements" role="tabpanel">
        <div id="viewRequirementsContent">
          <!-- Requirements will be populated here -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Offcanvas Add Stall -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addStallDrawer">
  <div class="offcanvas-header border-bottom bg-light">
    <h5 class="offcanvas-title d-flex align-items-center fw-bold text-primary">
      <i class="bx bx-store-alt me-2 fs-4"></i> ADD NEW STALL
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <form id="addStallForm" novalidate>
      @csrf
      <div class="row g-3 mt-1">
        <div class="col-12">
          <label class="form-label">Stall Name <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-store"></i></span>
            <input type="text" name="stallNo" class="form-control" placeholder="Enter stall name/number" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
          </div>
          <div class="invalid-feedback d-block" data-error="stallNo"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Marketplace <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-map"></i></span>
            <select name="marketplaceID" class="form-select">
              <option value="">Select Marketplace</option>
              @foreach($marketplaces as $marketplace)
                <option value="{{ $marketplace->marketplaceID }}">{{ $marketplace->marketplace }}</option>
              @endforeach
            </select>
          </div>
          <div class="invalid-feedback d-block" data-error="marketplaceID"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Size (sq. m.)</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-ruler"></i></span>
            <input type="text" name="size" class="form-control" placeholder="e.g., 2m x 3m">
          </div>
          <div class="invalid-feedback d-block" data-error="size"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Monthly Rental Fee <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-money"></i></span>
            <input type="number" step="0.01" name="rentalFee" class="form-control" placeholder="0.00">
          </div>
          <div class="invalid-feedback d-block" data-error="rentalFee"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Application Deadline</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-time"></i></span>
            <input type="date" name="applicationDeadline" class="form-control" min="">
          </div>
          <div class="invalid-feedback d-block" data-error="applicationDeadline"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Stall Status <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-badge-check"></i></span>
            <select name="stallStatus" class="form-select">
              <option value="">Select Status</option>
              <option value="Vacant">Vacant</option>
              <option value="Occupied">Occupied</option>
            </select>
          </div>
          <div class="invalid-feedback d-block" data-error="stallStatus"></div>
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-label-primary" data-bs-dismiss="offcanvas">
          <i class="bx bx-x me-1"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="bx bx-save me-1"></i> Save Stall
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Offcanvas Edit Stall -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editStallDrawer">
  <div class="offcanvas-header border-bottom bg-light">
    <h5 class="offcanvas-title d-flex align-items-center fw-bold text-primary">
      <i class="bx bx-edit me-2 fs-3"></i> EDIT STALL
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <form id="editStallForm" novalidate>
      @csrf
      @method('PUT')
      <input type="hidden" name="id" id="editStallId">

      <div class="row g-3 mt-1">
        <!-- Stall Name field (shown only for Occupied stalls) -->
        <div class="col-12 d-none" id="editStallNoContainer">
          <label class="form-label">Stall Name <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-store"></i></span>
            <input type="text" name="stallNo" id="editStallNo" class="form-control" placeholder="Enter stall name/number" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
          </div>
          <div class="invalid-feedback d-block" data-error="stallNo"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Marketplace <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-map"></i></span>
            <select name="marketplaceID" id="editMarketplaceID" class="form-select">
              <option value="">Select Marketplace</option>
              @foreach($marketplaces as $marketplace)
                <option value="{{ $marketplace->marketplaceID }}">{{ $marketplace->marketplace }}</option>
              @endforeach
            </select>
          </div>
          <div class="invalid-feedback d-block" data-error="marketplaceID"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Size (sq. m.)</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-ruler"></i></span>
            <input type="text" name="size" id="editSize" class="form-control" placeholder="e.g., 2m x 3m">
          </div>
          <div class="invalid-feedback d-block" data-error="size"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Monthly Rental Fee <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-money"></i></span>
            <input type="number" step="0.01" name="rentalFee" id="editRentalFee" class="form-control" placeholder="0.00">
          </div>
          <div class="invalid-feedback d-block" data-error="rentalFee"></div>
        </div>

        <!-- Application Deadline field (shown only for Vacant stalls) -->
        <div class="col-12 d-none" id="editApplicationDeadlineContainer">
          <label class="form-label">Application Deadline</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-time"></i></span>
            <input type="date" name="applicationDeadline" id="editApplicationDeadline" class="form-control" min="">
          </div>
          <div class="invalid-feedback d-block" data-error="applicationDeadline"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Stall Status <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-badge-check"></i></span>
            <select name="stallStatus" id="editStallStatus" class="form-select">
              <option value="">Select Status</option>
              <option value="Vacant">Vacant</option>
              <option value="Occupied">Occupied</option>
            </select>
          </div>
          <div class="invalid-feedback d-block" data-error="stallStatus"></div>
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-label-primary" data-bs-dismiss="offcanvas">
          <i class="bx bx-x me-1"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="bx bx-save me-1"></i> Update Stall
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Assign Tenant -->
<div class="modal fade" id="assignTenantModal" tabindex="-1" aria-labelledby="assignTenantModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <h5 class="modal-title fw-bold text-primary" id="assignTenantModalLabel">
          <i class="bx bx-user-plus me-2"></i> Assign <span id="assignStallNameDisplay"></span> to...
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-4" id="assignTenantSubtitle">
          Select a tenant from the list to assign this stall.
        </p>
        
        <form id="assignTenantForm" novalidate>
          @csrf
          <input type="hidden" name="stallID" id="assignStallId">
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Tenant Name <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bx bx-user"></i></span>
              <select name="userID" id="assignTenantSelect" class="form-select" required>
                <option value="">Select a tenant...</option>
              </select>
            </div>
            <div class="invalid-feedback d-block" data-error="userID"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-label-primary" data-bs-dismiss="modal">
          <i class="bx bx-x me-1"></i> Cancel
        </button>
        <button type="button" class="btn btn-primary" id="btnConfirmAssign">
          <i class="bx bx-check me-1"></i> Assign
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal List of Requirements -->
<div class="modal fade requirements-modal" id="requirementsModal" tabindex="-1" aria-labelledby="requirementsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <h5 class="modal-title fw-bold text-primary" id="requirementsModalLabel">
          <i class="bx bx-clipboard me-2"></i> List of Requirements
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3">Manage the list of requirements for stall applications and tenancy.</p>
        
        <!-- Proposal Requirements Table -->
        <div class="mb-4">
          <h6 class="fw-bold text-primary mb-3">
            <i class="bx bx-clipboard me-2"></i>Proposal Requirements
          </h6>
          <div class="table-responsive">
            <table class="table table-hover mb-0" id="proposalRequirementsTable" style="border: none;">
              <thead>
                <tr>
                  <th style="width: 50px; border: none;">#</th>
                  <th style="border: none;">Requirement Name</th>
                  <th style="width: 100px; border: none;" class="text-center">Required</th>
                  <th style="width: 120px; border: none;" class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody id="proposalRequirementsList">
                <!-- Proposal requirements will be loaded here -->
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    <i class="bx bx-loader-alt bx-spin fs-1 mb-2"></i>
                    <p class="mb-0">Loading requirements...</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Tenancy Requirements Table -->
        <div class="mb-4">
          <h6 class="fw-bold text-primary mb-3">
            <i class="bx bx-clipboard me-2"></i>Tenancy Requirements
          </h6>
          <div class="table-responsive">
            <table class="table table-hover mb-0" id="tenancyRequirementsTable" style="border: none;">
              <thead>
                <tr>
                  <th style="width: 50px; border: none;">#</th>
                  <th style="border: none;">Requirement Name</th>
                  <th style="width: 100px; border: none;" class="text-center">Required</th>
                  <th style="width: 120px; border: none;" class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody id="tenancyRequirementsList">
                <!-- Tenancy requirements will be loaded here -->
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    <i class="bx bx-loader-alt bx-spin fs-1 mb-2"></i>
                    <p class="mb-0">Loading requirements...</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-label-primary" data-bs-dismiss="modal">
          <i class="bx bx-x me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Add/Edit Requirement -->
<div class="modal fade" id="requirementFormModal" tabindex="-1" aria-labelledby="requirementFormModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <h5 class="modal-title fw-bold text-primary" id="requirementFormModalLabel">
          <i class="bx bx-plus me-2"></i> Add Requirement
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="requirementForm" novalidate>
          @csrf
          <input type="hidden" name="requirement_id" id="requirementId">
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Requirement Name <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bx bx-file"></i></span>
              <input type="text" name="requirement_name" id="requirementName" class="form-control" placeholder="e.g., Government ID, Business Permit" required>
            </div>
            <div class="invalid-feedback d-block" data-error="requirement_name"></div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Document Type <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bx bx-category"></i></span>
              <select name="document_type" id="documentType" class="form-select" required>
                <option value="">Select Document Type</option>
                <option value="Proposal">Proposal</option>
                <option value="Tenancy">Tenancy</option>
              </select>
            </div>
            <div class="invalid-feedback d-block" data-error="document_type"></div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" id="requirementDescription" class="form-control" rows="3" placeholder="Optional description for this requirement"></textarea>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" name="is_active" id="requirementIsActive" checked>
              <label class="form-check-label fw-semibold" for="requirementIsActive">
                Required
              </label>
            </div>
            <small class="text-muted">Uncheck to make this requirement optional</small>
          </div>
        </form>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-label-primary" data-bs-dismiss="modal">
          <i class="bx bx-x me-1"></i> Cancel
        </button>
        <button type="button" class="btn btn-primary" id="btnSaveRequirement">
          <i class="bx bx-save me-1"></i> Save
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  /* Tab styling for View drawer - System green palette (#7F9267) */
  #viewStallDrawer .nav-pills .nav-link {
    color: #7F9267;
    border-radius: 0.375rem;
  }
  
  #viewStallDrawer .nav-pills .nav-link:hover {
    color: #7F9267;
    background-color: #EFEFEA;
  }
  
  #viewStallDrawer .nav-pills .nav-link.active {
    background-color: #7F9267;
    color: white;
  }
  
  #viewStallDrawer .nav-pills .nav-link.active:hover {
    background-color: #6B7A56;
    color: white;
  }

  /* Search input placeholder styling */
  #stallsSearch::placeholder {
    color: rgba(127, 146, 103, 0.6) !important;
  }

  #stallsSearch:focus {
    background-color: rgba(127, 146, 103, 0.15) !important;
    border-color: rgba(127, 146, 103, 0.4) !important;
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(127, 146, 103, 0.25) !important;
  }

  #requirementsModal .text-muted {
    color: #201F23 !important;
  }

  /* Ensure Assign to Tenant is always clickable (not greyed by sibling/global styles) */
  #stallsTable .dropdown-menu .assign-tenant-action {
    pointer-events: auto !important;
    opacity: 1 !important;
    cursor: pointer !important;
  }
  #stallsTable .dropdown-menu .assign-tenant-action:hover {
    background-color: #EFEFEA !important;
    color: #7F9267 !important;
  }

  /* Assign Tenant modal: above layout and backdrop; dialog + controls must be clickable */
  #assignTenantModal.modal {
    z-index: 10600 !important;
    pointer-events: auto !important;
  }
  #assignTenantModal.modal .modal-dialog {
    pointer-events: auto !important;
  }
  #assignTenantModal.modal .modal-content {
    pointer-events: auto !important;
  }
  #assignTenantModal .modal-header *,
  #assignTenantModal .modal-body *,
  #assignTenantModal .modal-footer * {
    pointer-events: auto !important;
  }
  body .modal-backdrop {
    z-index: 10599 !important;
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
  
  // Set minimum date to today for Application Deadline fields (future dates only)
  const today = new Date().toISOString().split('T')[0];
  $('input[name="applicationDeadline"]').attr('min', today);
  
  var table = $('#stallsTable').DataTable({
    ajax: "{{ route('admins.stalls.data') }}",
    columns: [
      {data:null, className:'control', orderable:false, render:()=>''},
      {data:null, orderable:false, className:'text-center', render:function(d){
        return `<input type="checkbox" class="form-check-input row-checkbox" data-id="${d.stallID}">`;
      }},
      {data:'stallID', orderable:true, render:function(data, type, row, meta){
        // For display: show sequential row number accounting for pagination
        if (type === 'display' || type === 'type') {
          const pageInfo = table.page.info();
          return pageInfo.start + meta.row + 1;
        }
        // For sorting: use the actual ID value
        return data;
      }},
      {data:'formatted_stall_id', orderable:true, render:function(d){ return d || '-'; }},
      {data:'stallNo', orderable:true, render:function(d){ return d ? d.toUpperCase() : ''; }},
      {data:'marketplace', orderable:true},
      {data:'rentBy', orderable:true, render:function(d, type, row){
        // Only show Rent By if stall is Occupied
        if (row.stallStatus === 'Occupied') {
          return d || '-';
        }
        return '-';
      }},
      {data:null, className:'text-center', render:function(d){
        if(d.stallStatus === 'Occupied' && d.contractID) {
          return `<i class="bx bx-file text-primary" style="cursor: pointer; font-size: 1.5rem;" title="Click to view contract"></i>`;
        } else if(d.stallStatus === 'Vacant') {
          return `<i class="bx bx-file text-muted" style="opacity: 0.3; font-size: 1.5rem;"></i>`;
        } else {
          return `<i class="bx bx-file text-muted" style="opacity: 0.3; font-size: 1.5rem;"></i>`;
        }
      }},
      {data:'stallStatus', orderable:true, defaultContent:'Vacant', render:function(d){
        let cls='bg-label-primary';
        if(d=='Vacant') cls='bg-label-danger';
        if(d=='Occupied') cls='bg-label-primary';
        return `<span class="badge rounded-pill ${cls}">${d}</span>`;
      }},
      {data:null, orderable:false, className:'text-center', render:function(d){
        let actionButtons = '';
        if(d.stallStatus === 'Occupied') {
          actionButtons = `
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-view" data-id="${d.stallID}">
                <i class="bx bx-show me-1"></i> View
              </a>
            </li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-edit" data-id="${d.stallID}">
                <i class="bx bx-edit me-1"></i> Edit
              </a>
            </li>
            `;
        } else if(d.stallStatus === 'Vacant') {
          // Check if application deadline is set
          const hasApplicationDeadline = d.applicationDeadline && d.applicationDeadline !== null && d.applicationDeadline !== '';
          const applicationUrl = hasApplicationDeadline ? `/tenants/prospective/${d.stallID}/applications` : '#';
          const applicationClass = hasApplicationDeadline ? 'dropdown-item text-secondary' : 'dropdown-item text-secondary disabled';
          const applicationStyle = hasApplicationDeadline ? '' : 'pointer-events: none; opacity: 0.5; cursor: not-allowed;';
          
          actionButtons = `
            <li>
              <a href="${applicationUrl}" class="${applicationClass}" style="${applicationStyle}" ${!hasApplicationDeadline ? 'onclick="return false;"' : ''}>
                <i class="bx bx-right-arrow-alt me-1"></i> Go to Application
              </a>
            </li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-assign assign-tenant-action" data-id="${d.stallID}">
                <i class="bx bx-user-plus me-1"></i> Assign to Tenant
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-view" data-id="${d.stallID}">
                <i class="bx bx-show me-1"></i> View
              </a>
            </li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-edit" data-id="${d.stallID}">
                <i class="bx bx-edit me-1"></i> Edit
              </a>
            </li>
            `;
        } else {
          // Reserved status
          actionButtons = `
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-view" data-id="${d.stallID}">
                <i class="bx bx-show me-1"></i> View
              </a>
            </li>
            <li>
              <a href="javascript:;" class="dropdown-item text-secondary btn-edit" data-id="${d.stallID}">
                <i class="bx bx-edit me-1"></i> Edit
              </a>
            </li>
            `;
        }
        
        return `
          <div class="dropdown">
            <button class="btn btn-sm btn-light btn-icon" data-bs-toggle="dropdown">
              <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <ul class="dropdown-menu">
              ${actionButtons}
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
  $('#stallsSearch').on('keyup', function(){
    const query = $(this).val();
    table.search(query).draw();
  });

  // Clear search when input is cleared
  $('#stallsSearch').on('input', function(){
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
  let selectedStallIds = new Set();

  // Select All Checkbox Functionality
  $('#selectAllCheckbox').on('click', function() {
    const isChecked = $(this).is(':checked');
    $('.row-checkbox').each(function() {
      const stallId = $(this).data('id');
      if (isChecked) {
        selectedStallIds.add(stallId);
      } else {
        selectedStallIds.delete(stallId);
      }
      $(this).prop('checked', isChecked);
    });
    updateArchiveButtonVisibility();
  });

  // Individual checkbox change
  $(document).on('change', '.row-checkbox', function() {
    const stallId = $(this).data('id');
    if ($(this).is(':checked')) {
      selectedStallIds.add(stallId);
    } else {
      selectedStallIds.delete(stallId);
    }
    
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    updateArchiveButtonVisibility();
  });

  // Restore checkbox state after table redraw
  table.on('draw', function() {
    $('.row-checkbox').each(function() {
      const stallId = $(this).data('id');
      $(this).prop('checked', selectedStallIds.has(stallId));
    });
    
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    updateArchiveButtonVisibility();
  });

  // Update Archive button visibility
  function updateArchiveButtonVisibility() {
    if (selectedStallIds.size > 0) {
      $('#btnArchiveSelected').removeClass('d-none').show();
    } else {
      $('#btnArchiveSelected').addClass('d-none').hide();
    }
  }

  // Archive Selected Functionality
  $('#btnArchiveSelected').on('click', function() {
    const selectedIds = Array.from(selectedStallIds);

    if (selectedIds.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Selection',
        text: 'Please select at least one stall to archive.',
      });
      return;
    }

    Swal.fire({
      title: 'Archive Stalls?',
      text: `Are you sure you want to archive ${selectedIds.length} stall(s)?`,
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
          url: '{{ route("admins.stalls.archive-multiple") }}',
          type: 'POST',
          data: {
            ids: selectedIds,
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            Swal.fire({
              icon:'success',
              title:'Success',
              text: `${selectedIds.length} stall(s) have been archived.`,
              toast:true,
              position:'top',
              showConfirmButton:false,
              showCloseButton:true,
              timer: 2000,
              timerProgressBar:true
            });
            // Clear selections
            selectedStallIds.clear();
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
              text: xhr.responseJSON?.message || 'Failed to archive stalls. Please try again.',
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
        window.open("{{ route('admins.stalls.print') }}", '_blank');
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

  // Stall ID formatter - format as STALL-000X
  function formatStallId(id) {
    if (!id) return '-';
    return 'STALL-' + String(id).padStart(4, '0');
  }

  // Click handler for contract icon
  $(document).on('click', '.bx-file.text-primary', function () {
    let rowData = table.row($(this).closest('tr')).data();
    if (rowData && rowData.contractID) {
      // TODO: Open contract view modal or redirect to contract page
      Swal.fire({
        icon: 'info',
        title: 'Contract',
        text: `Opening contract #${rowData.contractID}`,
        timer: 2000,
        showConfirmButton: false,
        animation: false,
        showClass: { popup: '' },
        hideClass: { popup: '' }
      });
    }
  });

  // View stall details
  $(document).on('click', '.btn-view', function () {
    let id = $(this).data('id');
    let rowData = table.row($(this).closest('tr')).data();
    
    // If vacant stall, open drawer with only Stall tab (hide tabs)
    const $tabs = $('#viewStallTabs');
    const isVacant = rowData && rowData.stallStatus === 'Vacant';
    if (isVacant) {
      $tabs.addClass('d-none');
      $('#view-pane-user').addClass('d-none');
    } else {
      $tabs.removeClass('d-none');
      $('#view-pane-user').removeClass('d-none');
    }
    
    $.get(`/stalls/${id}`, function (stall) {
      // Populate Stall Information Tab
      $('#viewName').text(stall.stallNo);
      $('#viewId').text(formatStallIdForAssign(stall.marketplace, stall.stallID));
      $('#viewMarketplace').text(stall.marketplace ?? '-');
      $('#viewRentalFee').text(stall.rentalFee ? '₱' + parseFloat(stall.rentalFee).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-');
      $('#viewSize').text(stall.size ?? '-');
      $('#viewApplicationDeadline').text(stall.applicationDeadline ? formatDate(stall.applicationDeadline) : '-');
      $('#viewRentedBy').text(stall.contract && stall.contract.user ? stall.contract.user : '-');
      $('#viewAssignmentDate').text(stall.contract && stall.contract.startDate ? formatDate(stall.contract.startDate) : '-');

      let statusClass = 'bg-label-primary'; 
      if (stall.stallStatus === 'Vacant') statusClass = 'bg-label-danger';
      if (stall.stallStatus === 'Occupied') statusClass = 'bg-label-primary';

      $('#viewStatus')
        .attr('class', 'badge rounded-pill px-3 py-2 ' + statusClass)
        .text(stall.stallStatus ?? '-');

      // Update drawer title based on status
      if (stall.stallStatus === 'Occupied') {
        $('#viewDrawerTitle').html('<i class="bx bx-store-alt me-2 fs-3"></i> STALL INFORMATION');
      } else {
        $('#viewDrawerTitle').html('<i class="bx bx-store-alt me-2 fs-3"></i> STALL INFORMATION');
      }

      // Populate User Information Tab (only for occupied stalls) - Match User module exactly
      let userHtml = '';
      if (stall.stallStatus === 'Occupied' && stall.user) {
        // Build full name with middle name like User module
        let fullName = `${stall.user.firstName ?? ''} ${stall.user.middleName ?? ''} ${stall.user.lastName ?? ''}`.trim();
        
        userHtml = `
          <div class="text-center mb-4">
            <div id="profileCircle" 
                 class="bg-gray text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm"
                 style="width:80px; height:80px; font-size:1.5rem;">
            </div>
            <h5 class="mt-3 fw-bold">${fullName}</h5>
            <span class="badge rounded-pill px-3 py-2 ${stall.user.userStatus === 'Active' ? 'bg-label-primary' : 'bg-label-secondary'}">${stall.user.userStatus}</span>
          </div>

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
                  <div>USER-${String(stall.user.userID).padStart(4, '0')}</div>
                </div>
              </div>

              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                    style="width:36px; height:36px;">
                  <i class="bx bx-envelope text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Email</div>
                  <div>${stall.user.email || '-'}</div>
                </div>
              </div>

              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                    style="width:36px; height:36px;">
                  <i class="bx bx-phone text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Contact</div>
                  <div>${stall.user.contactNo || '-'}</div>
                </div>
              </div>

              <div class="mb-3 d-flex align-items-start">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                    style="width:36px; height:36px;">
                  <i class="bx bx-home text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Home Address</div>
                  <div>${stall.user.homeAddress || '-'}</div>
                </div>
              </div>

              <div class="mb-3 d-flex align-items-center">
                <div class="bg-gray rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                    style="width:36px; height:36px;">
                  <i class="bx bx-calendar text-white fs-5"></i>
                </div>
                <div>
                  <div class="text-muted small">Birthdate</div>
                  <div>${stall.user.birthDate ? formatDate(stall.user.birthDate) : '-'}</div>
                </div>
              </div>
            </div>
          </div>

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
                  <div>${stall.user.created_at ? formatDate(stall.user.created_at) : '-'}</div>
                </div>
              </div>
            </div>
          </div>
        `;
      } else {
        userHtml = '<div class="text-center text-muted py-5"><i class="bx bx-info-circle fs-1 mb-3"></i><p>No user information available for vacant stalls.</p></div>';
      }
      $('#viewUserContent').html(userHtml);
      
      // Set profile circle initials like User module
      if (stall.stallStatus === 'Occupied' && stall.user) {
        let fullName = `${stall.user.firstName ?? ''} ${stall.user.middleName ?? ''} ${stall.user.lastName ?? ''}`.trim();
        let profileCircle = $('#viewUserContent #profileCircle');
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
      }

      // Populate Requirements Tab (only for occupied stalls)
      let requirementsHtml = '';
      if (stall.stallStatus === 'Occupied' && stall.requirements && stall.requirements.length > 0) {
        requirementsHtml = '<div class="card shadow border-0 mb-3"><div class="card-body"><h6 class="fw-bold text-uppercase text-muted fs-5 mb-3"><i class="bx bx-paperclip me-1 text-secondary fs-4"></i> Submitted Requirements</h6>';
        
        stall.requirements.forEach((req, index) => {
          const statusClass = req.docStatus === 'Approved' ? 'bg-label-success' : 
                             req.docStatus === 'Rejected' ? 'bg-label-danger' : 
                             req.docStatus === 'Needs Revision' ? 'bg-label-warning' : 'bg-label-secondary';
          
          requirementsHtml += `
            <div class="mb-4 pb-4 ${index < stall.requirements.length - 1 ? 'border-bottom' : ''}">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h6 class="mb-1">${req.documentType} Document</h6>
                  <span class="badge rounded-pill ${statusClass}">${req.docStatus}</span>
                </div>
                <small class="text-muted">${req.created_at ? formatDate(req.created_at) : '-'}</small>
              </div>
              
              ${req.files && req.files.length > 0 ? `
                <div class="mt-3">
                  <strong class="text-muted small">Uploaded Files:</strong>
                  <ul class="list-unstyled mt-2">
                    ${req.files.map(file => `
                      <li class="mb-2">
                        <i class="bx bx-file me-2"></i>
                        <a href="${file.filePath ? '/storage/' + file.filePath : '#'}" target="_blank" class="text-decoration-none">
                          ${file.originalName || 'File'}
                        </a>
                        ${file.dateUploaded ? `<small class="text-muted ms-2">(${formatDate(file.dateUploaded)})</small>` : ''}
                      </li>
                    `).join('')}
                  </ul>
                </div>
              ` : '<p class="text-muted small mt-2">No files uploaded</p>'}
              
              ${req.revisionComment ? `
                <div class="mt-3 p-3 bg-light rounded">
                  <strong class="text-muted small">Revision Comment:</strong>
                  <p class="mb-0 mt-1">${req.revisionComment}</p>
                </div>
              ` : ''}
            </div>
          `;
        });
        
        requirementsHtml += '</div></div>';
      } else {
        requirementsHtml = '<div class="text-center text-muted py-5"><i class="bx bx-paperclip fs-1 mb-3"></i><p>No requirements submitted yet.</p></div>';
      }
      $('#viewRequirementsContent').html(requirementsHtml);

      // Reset to first tab
      $('#view-tab-stall').tab('show');
      $('#viewDrawerTitle').html('<i class="bx bx-store-alt me-2 fs-3"></i> STALL INFORMATION');

      new bootstrap.Offcanvas('#viewStallDrawer').show();
    });
  });

  // Update drawer title when switching tabs
  $('#view-tab-stall').on('shown.bs.tab', function () {
    $('#viewDrawerTitle').html('<i class="bx bx-store-alt me-2 fs-3"></i> STALL INFORMATION');
  });
  $('#view-tab-user').on('shown.bs.tab', function () {
    $('#viewDrawerTitle').html('<i class="bx bx-user me-2 fs-3"></i> USER INFORMATION');
  });
  $('#view-tab-requirements').on('shown.bs.tab', function () {
    $('#viewDrawerTitle').html('<i class="bx bx-paperclip me-2 fs-3"></i> SUBMITTED REQUIREMENTS');
  });

  // Add stall actions removed (admin cannot add new map places)

  // Open Edit Drawer
  $(document).on('click', '.btn-edit', function() {
    let id = $(this).data('id');
    // Clear any previous validation errors
    $('#editStallForm').find('.is-invalid').removeClass('is-invalid');
    $('#editStallForm').find('.input-group').removeClass('error-state');
    $('[data-error]').text('');
    
    $.get(`/stalls/${id}`, function(stall){
      $('#editStallId').val(stall.stallID);
      $('#editStallNo').val(stall.stallNo || '');
      $('#editMarketplaceID').val(stall.marketplaceID);
      $('#editSize').val(stall.size || '');
      $('#editRentalFee').val(stall.rentalFee || '');
      $('#editStallStatus').val(stall.stallStatus || '');
      
      // Ensure status field is always enabled
      $('#editStallStatus').prop('disabled', false);
      $('#editStallStatus').removeAttr('title');
      
      // Show/hide fields based on status
      if (stall.stallStatus === 'Occupied') {
        // Show Stall Name field for Occupied stalls
        $('#editStallNoContainer').removeClass('d-none');
        $('#editApplicationDeadlineContainer').addClass('d-none');
      } else if (stall.stallStatus === 'Vacant') {
        // Show Application Deadline field for Vacant stalls
        $('#editStallNoContainer').addClass('d-none');
        $('#editApplicationDeadlineContainer').removeClass('d-none');
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        $('#editApplicationDeadline').attr('min', today);
        $('#editApplicationDeadline').val(stall.applicationDeadline || '');
      } else {
        // Hide both for other statuses
        $('#editStallNoContainer').addClass('d-none');
        $('#editApplicationDeadlineContainer').addClass('d-none');
      }

      $('[data-error]').text('');
      $('#editStallForm').find('.is-invalid').removeClass('is-invalid');
      $('#editStallForm').find('.input-group').removeClass('error-state');
      new bootstrap.Offcanvas('#editStallDrawer').show();
    });
  });


  // Submit Edit Form
  $('#editStallForm').on('submit', function(e){
    e.preventDefault();
    $('[data-error]').text('');
    $('#editStallForm').find('.is-invalid').removeClass('is-invalid');
    $('#editStallForm').find('.input-group').removeClass('error-state');

    let id = $('#editStallId').val();
    let $form = $(this);
    let $btn = $form.find('button[type="submit"]').prop('disabled', true);

    $.ajax({
      url: `/stalls/${id}`,
      method: 'PUT',
      data: $form.serialize(),
      success: function(){
        bootstrap.Offcanvas.getInstance($('#editStallDrawer')).hide();
        table.ajax.reload();
        Swal.fire({
          icon: 'success',
          title: 'Stall updated successfully!',
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
            $('#editStallForm [name="'+field+'"]').addClass('is-invalid');
            // Add error-state class to parent input-group
            $('#editStallForm [name="'+field+'"]').closest('.input-group').addClass('error-state');
          });
        } else {
          Swal.fire({
            icon:'error',
          title:'Error updating stall!',
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


  // Format Stall ID based on marketplace (HUB-000X or BAZ-000X)
  function formatStallIdForAssign(marketplaceName, stallId) {
    if (!marketplaceName || !stallId) return '';
    
    let prefix = '';
    const marketplaceUpper = marketplaceName.toUpperCase();
    
    // Check for "The Hub by D & G Properties"
    if (marketplaceUpper.includes('THE HUB') || marketplaceUpper.includes('HUB BY D & G')) {
      prefix = 'HUB-';
    }
    // Check for "Your One-Stop Bazaar"
    else if (marketplaceUpper.includes('ONE-STOP BAZAAR') || marketplaceUpper.includes('YOUR ONE-STOP')) {
      prefix = 'BAZ-';
    }
    // Default: use first 3 letters if no match
    else {
      prefix = marketplaceName.substring(0, 3).toUpperCase() + '-';
    }
    
    return prefix + String(stallId).padStart(4, '0');
  }

  // Assign to Tenant - Open Modal
  $(document).on('click', '.btn-assign', function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).data('id');
    if (!id) return;

    // Close the actions dropdown so it does not sit on top of the modal
    var dropdownEl = $(this).closest('.dropdown').find('[data-bs-toggle="dropdown"]')[0];
    if (dropdownEl && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
      var dropdownInstance = bootstrap.Dropdown.getInstance(dropdownEl);
      if (dropdownInstance) dropdownInstance.hide();
    }

    // Get stall details
    $.get('/stalls/' + id)
      .done(function(stall) {
        $('#assignStallId').val(id);
        $('#assignStallNameDisplay').text(stall.stallNo || ('Stall #' + id));

        // Load tenants (already sorted alphabetically by backend)
        $.get('/stalls/tenants/list')
          .done(function(response) {
            var $select = $('#assignTenantSelect');
            $select.empty().append('<option value="">Select a tenant...</option>');
            if (response.tenants && response.tenants.length > 0) {
              response.tenants.forEach(function(tenant) {
                $select.append('<option value="' + tenant.userID + '">' + (tenant.fullName || '') + '</option>');
              });
            } else {
              $select.append('<option value="">No active tenants available</option>');
            }
          })
          .fail(function() {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Failed to load tenants. Please try again.',
              toast: true,
              position: 'top',
              timer: 2000,
              showConfirmButton: false
            });
          });

        $('#assignTenantForm')[0].reset();
        $('#assignTenantForm').find('.is-invalid').removeClass('is-invalid');
        $('[data-error]').text('');
        var $modal = $('#assignTenantModal');
        var modalEl = $modal[0];
        // Move modal to end of body so it is not clipped by parent overflow/stacking context
        $modal.appendTo('body');
        var modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
        $modal.off('shown.bs.modal hidden.bs.modal')
          .on('shown.bs.modal', function() {
            $(this).css({ 'z-index': 10600, 'pointer-events': 'auto' });
            $(this).find('.modal-dialog, .modal-content').css('pointer-events', 'auto');
            $('.modal-backdrop').last().css('z-index', 10599);
          })
          .on('hidden.bs.modal', function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
          });
        modalInstance.show();
      })
      .fail(function() {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Could not load stall details. Please try again.',
          toast: true,
          position: 'top',
          timer: 2000,
          showConfirmButton: false
        });
      });
  });

  // Close Assign Tenant modal when Cancel or X is clicked (delegated)
  $(document).on('click', '#assignTenantModal [data-bs-dismiss="modal"], #assignTenantModal .btn-close', function() {
    var modalEl = document.getElementById('assignTenantModal');
    if (modalEl) {
      var inst = bootstrap.Modal.getInstance(modalEl);
      if (inst) inst.hide();
    }
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
  });

  // Confirm Assign Tenant (delegated so it works after modal is moved to body)
  $(document).on('click', '#btnConfirmAssign', function(){
    const $form = $('#assignTenantForm');
    const stallID = $('#assignStallId').val();
    const userID = $('#assignTenantSelect').val();
    const stallName = $('#assignStallNameDisplay').text();
    const tenantName = $('#assignTenantSelect option:selected').text();
    
    if (!userID) {
      $('#assignTenantSelect').addClass('is-invalid');
      $('[data-error="userID"]').text('Please select a tenant.');
      return;
    }
    
    // Disable button during request
    const $btn = $(this).prop('disabled', true);
    
    $.ajax({
      url: '/stalls/assign-tenant',
      method: 'POST',
      data: {
        stallID: stallID,
        userID: userID,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        var assignModalEl = document.getElementById('assignTenantModal');
        if (assignModalEl) {
          var assignInst = bootstrap.Modal.getInstance(assignModalEl);
          if (assignInst) assignInst.hide();
        }
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        table.ajax.reload();
        
        // Show notification using existing system
        Swal.fire({
          icon: 'success',
          title: 'Tenant Assigned!',
          text: `${stallName} has been assigned to ${tenantName}.`,
          toast: true,
          position: 'top',
          showConfirmButton: false,
          showCloseButton: true,
          timer: 3000,
          timerProgressBar: true
        });
      },
      error: function(xhr) {
        if(xhr.status === 422){
          let errors = xhr.responseJSON.errors || {};
          $.each(errors, function(field, messages){
            $('[data-error="'+field+'"]').text(messages[0]);
            $('#assignTenantForm [name="'+field+'"]').addClass('is-invalid');
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: xhr.responseJSON?.message || 'Failed to assign tenant. Please try again.',
            toast: true,
            position: 'top',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 3000,
            timerProgressBar: true,
            animation: false,
            showClass: { popup: '' },
            hideClass: { popup: '' }
          });
        }
      },
      complete: function() {
        $btn.prop('disabled', false);
      }
    });
  });

  // Delete User
  // Stall deletion removed

  // Clear validation errors when user types/changes input
  $('#addStallForm, #editStallForm').on('input change', '.form-control, .form-select', function(){
    const $input = $(this);
    $input.removeClass('is-invalid');
    $input.closest('.input-group').removeClass('error-state');
    $('[data-error="'+$input.attr('name')+'"]').text('');
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

  // ============================================
  // Requirements Management
  // ============================================
  
  // Open Requirements Modal
  $('#btnListRequirements').on('click', function() {
    loadRequirements();
    new bootstrap.Modal('#requirementsModal').show();
  });

  // Load Requirements List
  function loadRequirements() {
    $('#proposalRequirementsList').html(`
      <tr>
        <td colspan="4" class="text-center text-muted py-4">
          <i class="bx bx-loader-alt bx-spin fs-1 mb-2"></i>
          <p class="mb-0">Loading requirements...</p>
        </td>
      </tr>
    `);
    $('#tenancyRequirementsList').html(`
      <tr>
        <td colspan="4" class="text-center text-muted py-4">
          <i class="bx bx-loader-alt bx-spin fs-1 mb-2"></i>
          <p class="mb-0">Loading requirements...</p>
        </td>
      </tr>
    `);

    $.get("{{ route('admins.stalls.requirements.index') }}")
      .done(function(response) {
        if (response && response.requirements) {
          renderRequirementsList(response.requirements || []);
        } else {
          renderRequirementsList([]);
        }
      })
      .fail(function(xhr) {
        console.error('Requirements load error:', xhr);
        let errorMsg = 'Failed to load requirements. Please try again.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        } else if (xhr.status === 404) {
          errorMsg = 'Requirements endpoint not found.';
        } else if (xhr.status === 500) {
          errorMsg = 'Server error. Please check if the requirements table exists.';
        }
        $('#proposalRequirementsList').html(`
          <tr>
            <td colspan="4" class="text-center">
              <div class="alert alert-danger mb-0">
                <i class="bx bx-error-circle me-2"></i>
                ${errorMsg}
              </div>
            </td>
          </tr>
        `);
        $('#tenancyRequirementsList').html(`
          <tr>
            <td colspan="4" class="text-center">
              <div class="alert alert-danger mb-0">
                <i class="bx bx-error-circle me-2"></i>
                ${errorMsg}
              </div>
            </td>
          </tr>
        `);
      });
  }

  // Render Requirements List as Table
  function renderRequirementsList(requirements) {
    // Separate by document type
    let proposalReqs = requirements.filter(r => r.document_type === 'Proposal');
    let tenancyReqs = requirements.filter(r => r.document_type === 'Tenancy');
    
    // Sort by sort_order (default)
    proposalReqs = proposalReqs.sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));
    tenancyReqs = tenancyReqs.sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));
    
    // Render Proposal Requirements
    renderRequirementsTable(proposalReqs, 'proposalRequirementsList', 'Proposal');
    
    // Render Tenancy Requirements
    renderRequirementsTable(tenancyReqs, 'tenancyRequirementsList', 'Tenancy');
  }
  
  // Render Requirements Table for a specific document type
  function renderRequirementsTable(requirements, listId, documentType) {
    const $list = $('#' + listId);
    
    if (requirements.length === 0) {
      $list.html(`
        <tr>
          <td colspan="4" class="text-center text-muted py-5">
            <i class="bx bx-clipboard fs-1 mb-3"></i>
            <p class="mb-0">No requirements added yet. Hover below to add a new requirement.</p>
          </td>
        </tr>
        <tr class="requirement-row-hover" data-document-type="${documentType}">
          <td colspan="4" class="text-center py-2" style="border: none;">
            <button type="button" class="btn btn-sm btn-primary btn-add-inline" data-position="0" data-document-type="${documentType}" style="opacity: 0; transition: opacity 0.2s;">
              <i class="bx bx-plus me-1"></i> Add Requirement
            </button>
          </td>
        </tr>
      `);
      setupRequirementRowHover();
      return;
    }

    let html = '';
    requirements.forEach((req, index) => {
      const isRequired = req.is_active !== false; // Default to true if not set
      html += `
        <tr class="requirement-row" data-id="${req.id}" data-document-type="${req.document_type || documentType}">
          <td style="border: none;">${index + 1}</td>
          <td style="border: none;">
            <div class="fw-semibold requirement-name-display">${req.requirement_name || 'Unnamed Requirement'}</div>
          </td>
          <td style="border: none;" class="text-center">
            <input type="checkbox" class="form-check-input requirement-checkbox" data-id="${req.id}" ${isRequired ? 'checked' : ''}>
          </td>
          <td style="border: none;" class="text-center">
            <div class="d-flex gap-2 justify-content-center">
              <button type="button" class="btn btn-sm btn-label-primary btn-edit-requirement" data-id="${req.id}">
                <i class="bx bx-edit"></i>
              </button>
              <button type="button" class="btn btn-sm btn-danger btn-delete-requirement" data-id="${req.id}">
                <i class="bx bx-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    });
    
    // Add only ONE hover row at the end for adding new requirements
    html += `
      <tr class="requirement-row-hover" data-document-type="${documentType}">
        <td colspan="4" class="text-center py-2" style="border: none;">
          <button type="button" class="btn btn-sm btn-primary btn-add-inline" data-position="${requirements.length}" data-document-type="${documentType}" style="opacity: 0; transition: opacity 0.2s;">
            <i class="bx bx-plus me-1"></i> Add Requirement
          </button>
        </td>
      </tr>
    `;

    $list.html(html);
    
    // Setup hover functionality
    setupRequirementRowHover();
  }

  // Setup hover functionality for add buttons
  function setupRequirementRowHover() {
    $('.requirement-row-hover').on('mouseenter', function() {
      $(this).find('.btn-add-inline').css('opacity', '1');
    }).on('mouseleave', function() {
      $(this).find('.btn-add-inline').css('opacity', '0');
    });
  }

  // Open Add Requirement Form (inline add button)
  $(document).on('click', '.btn-add-inline', function() {
    const $hoverRow = $(this).closest('.requirement-row-hover');
    const position = $(this).data('position') || 0;
    const documentType = $(this).data('document-type') || 'Proposal';
    
    // Create inline form row that looks like a regular row
    const inlineFormRow = `
      <tr class="requirement-form-row">
        <td style="border: none;"></td>
        <td style="border: none;">
          <input type="text" class="form-control form-control-sm d-inline-block w-auto inline-requirement-name" placeholder="Requirement Name" required style="min-width: 200px;">
          <small class="text-danger d-block mt-1 inline-error-name" style="display: none;"></small>
        </td>
        <td style="border: none;" class="text-center">
          <input type="checkbox" class="form-check-input inline-requirement-active" checked>
        </td>
        <td style="border: none;" class="text-center">
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-sm btn-primary btn-save-inline">
              <i class="bx bx-check"></i>
            </button>
            <button type="button" class="btn btn-sm btn-label-secondary btn-cancel-inline">
              <i class="bx bx-x"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
    
    // Insert the form row before the hover row
    $hoverRow.before(inlineFormRow);
    $hoverRow.hide();
    
    // Focus on the requirement name input
    const $nameInput = $('.requirement-form-row').find('.inline-requirement-name');
    $nameInput.focus();
    
    // Handle Enter key to save
    $nameInput.on('keydown', function(e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        $('.btn-save-inline').click();
      }
    });
    
    // Handle Escape key to cancel
    $(document).on('keydown', function(e) {
      if (e.key === 'Escape' && $('.requirement-form-row').length > 0) {
        $('.btn-cancel-inline').click();
      }
    });
  });
  
  // Cancel inline add
  $(document).on('click', '.btn-cancel-inline', function() {
    const $formRow = $(this).closest('.requirement-form-row');
    const $nextHoverRow = $formRow.next('.requirement-row-hover');
    $formRow.remove();
    if ($nextHoverRow.length) {
      $nextHoverRow.show();
    } else {
      // If no next hover row, create one
      $formRow.after(`
        <tr class="requirement-row-hover">
          <td colspan="4" class="text-center py-1 position-relative" style="border: none; height: 30px;">
            <button type="button" class="btn btn-sm btn-primary btn-add-inline" data-position="0" style="opacity: 0; transition: opacity 0.2s;">
              <i class="bx bx-plus me-1"></i> Add Requirement
            </button>
          </td>
        </tr>
      `);
      setupRequirementRowHover();
    }
  });
  
  // Save inline requirement
  $(document).on('click', '.btn-save-inline', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const $formRow = $(this).closest('.requirement-form-row');
    const requirementName = $formRow.find('.inline-requirement-name').val().trim();
    const $hoverRow = $formRow.next('.requirement-row-hover');
    const documentType = $hoverRow.data('document-type') || $hoverRow.find('.btn-add-inline').data('document-type') || 'Proposal';
    const isActive = $formRow.find('.inline-requirement-active').is(':checked');
    
    // Clear previous errors
    $formRow.find('.inline-requirement-name').removeClass('is-invalid');
    $formRow.find('.inline-error-name').hide().text('');
    
    // Basic validation
    if (!requirementName) {
      $formRow.find('.inline-requirement-name').addClass('is-invalid');
      $formRow.find('.inline-error-name').text('Requirement name is required.').show();
      return;
    }
    
    const $btn = $(this).prop('disabled', true);
    
    $.ajax({
      url: "{{ route('admins.stalls.requirements.store') }}",
      method: 'POST',
      data: {
        requirement_name: requirementName,
        document_type: documentType,
        description: null,
        is_active: isActive ? 1 : 0,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        // Remove the form row immediately
        $formRow.remove();
        $hoverRow.show();
        
        // Reload requirements list to get the actual ID and proper formatting
        loadRequirements();
        Swal.fire({
          icon: 'success',
          title: 'Added!',
          text: `"${requirementName}" added successfully.`,
          toast: true,
          position: 'top',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
      },
      error: function(xhr) {
        console.error('Save error:', xhr);
        $btn.prop('disabled', false);
        if (xhr.status === 422) {
          const errors = xhr.responseJSON?.errors || {};
          if (errors.requirement_name) {
            $formRow.find('.inline-requirement-name').addClass('is-invalid');
            $formRow.find('.inline-error-name').text(errors.requirement_name[0]).show();
          }
          if (errors.document_type) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: errors.document_type[0],
              toast: true,
              position: 'top',
              timer: 2000,
              timerProgressBar: true
            });
          }
        } else {
          let errorMsg = xhr.responseJSON?.message || 'Failed to save requirement.';
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMsg,
            toast: true,
            position: 'top',
            timer: 2000,
            timerProgressBar: true
          });
        }
      }
    });
  });

  // Open Edit Requirement Form (inline editing)
  $(document).on('click', '.btn-edit-requirement', function() {
    const $row = $(this).closest('.requirement-row');
    const id = $(this).data('id');
    const documentType = $row.data('document-type') || 'Proposal';
    const currentName = $row.find('.requirement-name-display').text().trim();
    
    // Replace the name display with an input field
    const $nameCell = $row.find('td').eq(1);
    const $nameDisplay = $row.find('.requirement-name-display');
    
    // Create inline edit form
    const editForm = `
      <input type="text" class="form-control form-control-sm d-inline-block w-auto inline-edit-requirement-name" value="${currentName}" required style="min-width: 200px;">
      <small class="text-danger d-block mt-1 inline-error-edit-name" style="display: none;"></small>
    `;
    
    $nameDisplay.replaceWith(editForm);
    
    // Hide edit button, show save/cancel buttons
    const $actionsCell = $row.find('td').eq(3);
    $actionsCell.html(`
      <div class="d-flex gap-2 justify-content-center">
        <button type="button" class="btn btn-sm btn-primary btn-save-edit" data-id="${id}">
          <i class="bx bx-check"></i>
        </button>
        <button type="button" class="btn btn-sm btn-label-secondary btn-cancel-edit" data-id="${id}">
          <i class="bx bx-x"></i>
        </button>
      </div>
    `);
    
    // Focus on the input
    $row.find('.inline-edit-requirement-name').focus().select();
    
    // Handle Enter key to save
    $row.find('.inline-edit-requirement-name').on('keydown', function(e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        $row.find('.btn-save-edit').click();
      }
      if (e.key === 'Escape') {
        $row.find('.btn-cancel-edit').click();
      }
    });
  });
  
  // Cancel inline edit
  $(document).on('click', '.btn-cancel-edit', function() {
    const $row = $(this).closest('.requirement-row');
    const id = $(this).data('id');
    const $nameCell = $row.find('td').eq(1);
    const $input = $row.find('.inline-edit-requirement-name');
    const originalName = $input.val().trim();
    
    // Restore original display
    $nameCell.html(`<div class="fw-semibold requirement-name-display">${originalName}</div>`);
    
    // Restore edit/delete buttons
    const $actionsCell = $row.find('td').eq(3);
    const isRequired = $row.find('.requirement-checkbox').is(':checked');
    $actionsCell.html(`
      <div class="d-flex gap-2 justify-content-center">
        <button type="button" class="btn btn-sm btn-label-primary btn-edit-requirement" data-id="${id}">
          <i class="bx bx-edit"></i>
        </button>
        <button type="button" class="btn btn-sm btn-danger btn-delete-requirement" data-id="${id}">
          <i class="bx bx-trash"></i>
        </button>
      </div>
    `);
  });
  
  // Save inline edit
  $(document).on('click', '.btn-save-edit', function() {
    const $row = $(this).closest('.requirement-row');
    const id = $(this).data('id');
    const $input = $row.find('.inline-edit-requirement-name');
    const newName = $input.val().trim();
    const documentType = $row.data('document-type') || 'Proposal';
    const isActive = $row.find('.requirement-checkbox').is(':checked');
    
    // Clear previous errors
    $row.find('.inline-edit-requirement-name').removeClass('is-invalid');
    $row.find('.inline-error-edit-name').hide().text('');
    
    // Basic validation
    if (!newName) {
      $row.find('.inline-edit-requirement-name').addClass('is-invalid');
      $row.find('.inline-error-edit-name').text('Requirement name is required.').show();
      return;
    }
    
    const $btn = $(this).prop('disabled', true);
    
    $.ajax({
      url: `{{ route('admins.stalls.requirements.index') }}/${id}`,
      method: 'PUT',
      data: {
        requirement_name: newName,
        document_type: documentType,
        is_active: isActive ? 1 : 0,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        // Update the display
        $row.find('td').eq(1).html(`<div class="fw-semibold requirement-name-display">${newName}</div>`);
        
        // Restore edit/delete buttons
        $row.find('td').eq(3).html(`
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-sm btn-label-primary btn-edit-requirement" data-id="${id}">
              <i class="bx bx-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger btn-delete-requirement" data-id="${id}">
              <i class="bx bx-trash"></i>
            </button>
          </div>
        `);
        
        Swal.fire({
          icon: 'success',
          title: 'Updated!',
          text: `"${newName}" updated successfully.`,
          toast: true,
          position: 'top',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
      },
      error: function(xhr) {
        console.error('Edit error:', xhr);
        $btn.prop('disabled', false);
        if (xhr.status === 422) {
          const errors = xhr.responseJSON?.errors || {};
          if (errors.requirement_name) {
            $row.find('.inline-edit-requirement-name').addClass('is-invalid');
            $row.find('.inline-error-edit-name').text(errors.requirement_name[0]).show();
          }
        } else {
          let errorMsg = xhr.responseJSON?.message || 'Failed to update requirement.';
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMsg,
            toast: true,
            position: 'top',
            timer: 2000
          });
        }
      }
    });
  });

  // Save Requirement (Add/Edit)
  $('#btnSaveRequirement').on('click', function() {
    const $form = $('#requirementForm');
    const requirementId = $('#requirementId').val();
    
    // Basic validation
    if (!$('#requirementName').val().trim()) {
      $('#requirementName').addClass('is-invalid');
      $('[data-error="requirement_name"]').text('Requirement name is required.');
      return;
    }
    
    if (!$('#documentType').val()) {
      $('#documentType').addClass('is-invalid');
      $('[data-error="document_type"]').text('Document type is required.');
      return;
    }

    const formData = {
      requirement_name: $('#requirementName').val().trim(),
      document_type: $('#documentType').val(),
      description: $('#requirementDescription').val() || null,
      is_active: $('#requirementIsActive').is(':checked'),
      _token: $('meta[name="csrf-token"]').attr('content')
    };

    const $btn = $(this).prop('disabled', true);
    const url = requirementId 
      ? `{{ route('admins.stalls.requirements.index') }}/${requirementId}`
      : "{{ route('admins.stalls.requirements.store') }}";
    const method = requirementId ? 'PUT' : 'POST';

    $.ajax({
      url: url,
      method: method,
      data: formData,
      success: function(response) {
        bootstrap.Modal.getInstance('#requirementFormModal').hide();
        loadRequirements();
        Swal.fire({
          icon: 'success',
          title: requirementId ? 'Updated!' : 'Added!',
          text: requirementId ? 'Requirement updated successfully.' : 'Requirement added successfully.',
          toast: true,
          position: 'top',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
      },
      error: function(xhr) {
        if (xhr.status === 422) {
          const errors = xhr.responseJSON.errors || {};
          $.each(errors, function(field, messages) {
            $('[data-error="' + field + '"]').text(messages[0]);
            $('[name="' + field + '"]').addClass('is-invalid');
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: xhr.responseJSON?.message || 'Failed to save requirement.',
            toast: true,
            position: 'top',
            timer: 2000,
            timerProgressBar: true
          });
        }
      },
      complete: function() {
        $btn.prop('disabled', false);
      }
    });
  });

  // Handle Required checkbox change
  $(document).on('change', '.requirement-checkbox', function() {
    const $checkbox = $(this);
    const id = $checkbox.data('id');
    
    // Skip if it's a temporary ID (not yet saved) or if disabled
    if (!id || id.toString().startsWith('temp-') || $checkbox.prop('disabled')) {
      $checkbox.prop('checked', !$checkbox.is(':checked'));
      return;
    }
    
    const isRequired = $checkbox.is(':checked');
    const $row = $checkbox.closest('.requirement-row');
    
    // Get document type from data attribute
    const documentType = $row.data('document-type') || 'Proposal';
    const requirementName = $row.find('.fw-semibold').text().trim();
    
    if (!requirementName) {
      $checkbox.prop('checked', !isRequired);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Could not find requirement name.',
        toast: true,
        position: 'top',
        timer: 2000,
        timerProgressBar: true
      });
      return;
    }
    
    $.ajax({
      url: `{{ route('admins.stalls.requirements.index') }}/${id}`,
      method: 'PUT',
      data: {
        requirement_name: requirementName,
        document_type: documentType,
        is_active: isRequired ? 1 : 0,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        // Visual feedback
        Swal.fire({
          icon: 'success',
          title: 'Updated!',
          text: `"${requirementName}" marked as ${isRequired ? 'Required' : 'Optional'}.`,
          toast: true,
          position: 'top',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
      },
      error: function(xhr) {
        console.error('Update error:', xhr);
        // Revert checkbox on error
        $checkbox.prop('checked', !isRequired);
        let errorMsg = 'Failed to update requirement status.';
        if (xhr.responseJSON) {
          if (xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
          } else if (xhr.responseJSON.errors) {
            const errors = Object.values(xhr.responseJSON.errors).flat();
            errorMsg = errors[0] || errorMsg;
          }
        }
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: errorMsg,
          toast: true,
          position: 'top',
          timer: 2000,
          timerProgressBar: true
        });
      }
    });
  });

  // Delete Requirement
  $(document).on('click', '.btn-delete-requirement', function() {
    const id = $(this).data('id');
    const $row = $(this).closest('.requirement-row');
    const requirementName = $row.find('.fw-semibold').text();

    Swal.fire({
      title: 'Delete Requirement?',
      text: `Are you sure you want to delete "${requirementName}"?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: 'transparent',
      cancelButtonText: 'Cancel',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `{{ route('admins.stalls.requirements.index') }}/${id}`,
          method: 'DELETE',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function() {
            loadRequirements();
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: `"${requirementName}" deleted successfully.`,
              toast: true,
              position: 'top',
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true
            });
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: xhr.responseJSON?.message || 'Failed to delete requirement.',
              toast: true,
              position: 'top',
              timer: 2000,
              timerProgressBar: true
            });
          }
        });
      }
    });
  });

  // Clear validation errors on input
  $('#requirementForm').on('input change', '.form-control, .form-select', function() {
    $(this).removeClass('is-invalid');
    $('[data-error="' + $(this).attr('name') + '"]').text('');
  });
});
</script>

<style>
  #proposalRequirementsTable,
  #tenancyRequirementsTable {
    border: none !important;
    border-collapse: separate;
    border-spacing: 0;
  }
  
  #proposalRequirementsTable thead th,
  #tenancyRequirementsTable thead th {
    border: none !important;
    border-bottom: 1px solid #dee2e6;
    background-color: #EFEFEA !important;
  }
  
  #proposalRequirementsTable tbody td,
  #tenancyRequirementsTable tbody td {
    border: none !important;
  }
  
  #proposalRequirementsTable tbody tr.requirement-row:hover,
  #tenancyRequirementsTable tbody tr.requirement-row:hover {
    background-color: #f8f9fa;
  }
  
  #proposalRequirementsTable tbody tr.requirement-row-hover:hover,
  #tenancyRequirementsTable tbody tr.requirement-row-hover:hover {
    background-color: transparent !important;
  }
  
  #requirementsModal .modal-header {
    background-color: #EFEFEA !important;
  }
  
</style>
@endpush


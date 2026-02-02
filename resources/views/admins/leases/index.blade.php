@extends('layouts.admin_app')

@section('title', 'Leases')
@section('page-title', 'Lease Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
  .status-badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
  }
  .status-active {
    background-color: rgba(25, 135, 84, 0.15);
    color: #198754;
  }
  .status-terminated {
    background-color: rgba(108, 117, 125, 0.15);
    color: #6c757d;
  }
  .status-expiring {
    background-color: rgba(255, 193, 7, 0.15);
    color: #cc9a00;
  }
  .days-warning {
    color: #dc3545;
    font-weight: bold;
  }
  .days-ok {
    color: #198754;
  }

  /* Table width and overflow control */
  .table-responsive {
    overflow-x: visible;
    -webkit-overflow-scrolling: touch;
  }

  #leasesTable {
    width: 100% !important;
    table-layout: auto;
  }

  /* Column width optimizations */
  #leasesTable th:nth-child(1),
  #leasesTable td:nth-child(1) {
    width: 40px;
    min-width: 40px;
  }

  #leasesTable th:nth-child(2),
  #leasesTable td:nth-child(2) {
    width: 80px;
    min-width: 80px;
  }

  #leasesTable th:nth-child(3),
  #leasesTable td:nth-child(3) {
    width: 120px;
    min-width: 120px;
  }

  #leasesTable th:nth-child(4),
  #leasesTable td:nth-child(4) {
    width: 100px;
    min-width: 100px;
  }

  #leasesTable th:nth-child(5),
  #leasesTable td:nth-child(5) {
    width: 120px;
    min-width: 120px;
  }

  #leasesTable th:nth-child(6),
  #leasesTable td:nth-child(6) {
    width: 100px;
    min-width: 100px;
  }

  #leasesTable th:nth-child(7),
  #leasesTable td:nth-child(7) {
    width: 100px;
    min-width: 100px;
  }

  #leasesTable th:nth-child(8),
  #leasesTable td:nth-child(8) {
    width: 100px;
    min-width: 100px;
  }

  #leasesTable th:nth-child(9),
  #leasesTable td:nth-child(9) {
    width: 100px;
    min-width: 100px;
  }

  #leasesTable th:nth-child(10),
  #leasesTable td:nth-child(10) {
    width: 100px;
    min-width: 100px;
  }

  #leasesTable th:nth-child(11),
  #leasesTable td:nth-child(11) {
    width: 120px;
    min-width: 120px;
    white-space: nowrap;
  }

  /* Hide less important columns on smaller screens */
  @media (max-width: 1200px) {
    #leasesTable th:nth-child(9),
    #leasesTable td:nth-child(9) {
      display: none;
    }
  }

  @media (max-width: 992px) {
    #leasesTable th:nth-child(7),
    #leasesTable td:nth-child(7),
    #leasesTable th:nth-child(8),
    #leasesTable td:nth-child(8) {
      display: none;
    }
  }

  /* Mobile responsive styles */
  @media (max-width: 768px) {
    #leasesTable thead {
      display: none;
    }
    
    #leasesTable tbody tr {
      display: block;
      margin-bottom: 1rem;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 1rem;
      background-color: #fff;
    }
    
    #leasesTable tbody td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0;
      border: none;
      border-bottom: 1px solid #f0f0f0;
      width: 100% !important;
    }
    
    #leasesTable tbody td:last-child {
      border-bottom: none;
    }
    
    #leasesTable tbody td::before {
      content: attr(data-label);
      font-weight: bold;
      color: #7F9267;
      margin-right: 1rem;
      flex-shrink: 0;
    }
    
    .card-header {
      flex-direction: column;
      align-items: stretch !important;
    }
    
    .card-header > div {
      width: 100%;
      margin-bottom: 0.5rem;
    }
    
    #statusTabs .status-tab {
      width: 100%;
    }
  }
</style>
@endpush

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <!-- Search Bar -->
    <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
      <div class="position-relative w-100">
        <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 1; color: #7F9267;">
          <i class="bx bx-search fs-5"></i>
        </span>
        <input type="text" id="leasesSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search by contract ID, tenant, or stall..." aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;">
      </div>
    </div>
    <!-- /Search Bar -->
  </div>

  <div class="card-body">
    <div class="d-flex flex-wrap gap-2 mb-3" id="statusTabs">
      <button type="button" class="btn btn-outline-primary status-tab active" data-status="Active">
        Active <span class="badge bg-primary ms-1">{{ $statusCounts['Active'] ?? 0 }}</span>
      </button>
      <button type="button" class="btn btn-outline-warning status-tab" data-status="Expiring">
        Expiring <span class="badge bg-warning text-dark ms-1">{{ $statusCounts['Expiring'] ?? 0 }}</span>
      </button>
      <button type="button" class="btn btn-outline-secondary status-tab" data-status="Terminated">
        Terminated <span class="badge bg-secondary ms-1">{{ $statusCounts['Terminated'] ?? 0 }}</span>
      </button>
    </div>

    <!-- Action Buttons Group (aligned with DataTables length selector) -->
    <div class="d-flex align-items-center gap-2" id="actionButtonsGroup" style="display: none !important;">
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
      <table id="leasesTable" class="table table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Contract ID</th>
            <th>Tenant</th>
            <th>Stall</th>
            <th>Marketplace</th>
            <th>Monthly Rent</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Days Remaining</th>
            <th>Status</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<!-- View Contract Details Modal -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="viewContractDrawer">
  <div class="offcanvas-header border-bottom bg-light">
    <h5 class="offcanvas-title d-flex align-items-center fw-bold text-primary">
      <i class="bx bx-file me-2 fs-3"></i> CONTRACT DETAILS
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div id="contractDetailsContent">
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Renew Contract Modal -->
<div class="modal fade" id="renewContractModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Renew Contract</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="renewContractForm">
        <div class="modal-body">
          <input type="hidden" id="renewContractId" name="contractId">
          <div class="mb-3">
            <label class="form-label">Renew for (months) <span class="text-danger">*</span></label>
            <select name="months" id="renewMonths" class="form-select" required>
              <option value="1">1 Month</option>
              <option value="2">2 Months</option>
              <option value="3">3 Months</option>
              <option value="6">6 Months</option>
              <option value="12">12 Months</option>
            </select>
            <small class="text-muted">Monthly bills will be generated for the renewal period.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Renew Contract</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Terminate Contract Modal -->
<div class="modal fade" id="terminateContractModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Terminate Contract</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="terminateContractForm">
        <div class="modal-body">
          <input type="hidden" id="terminateContractId" name="contractId">
          <div class="mb-3">
            <label class="form-label">Reason for Termination <span class="text-danger">*</span></label>
            <textarea name="reason" id="terminateReason" class="form-control" rows="4" required placeholder="Enter reason for terminating this contract..."></textarea>
            <small class="text-muted">This will mark the contract as terminated and set the stall status to Vacant.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Terminate Contract</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  const statusParam = new URLSearchParams(window.location.search).get('status');
  if (statusParam) {
    const $tab = $(`#statusTabs .status-tab[data-status="${statusParam}"]`);
    if ($tab.length) {
      $('#statusTabs .status-tab').removeClass('active');
      $tab.addClass('active');
    }
  }
  
  var table = $('#leasesTable').DataTable({
    ajax: {
      url: "{{ route('admins.leases.data') }}",
      data: function(d) {
        d.status = $('#statusTabs .status-tab.active').data('status');
        d.search = $('#leasesSearch').val();
      }
    },
    columns: [
      {data: null, orderable: false, render: function(data, type, row, meta) {
        const num = meta.row + 1;
        if (type === 'display') {
          return `<span data-label="#">${num}</span>`;
        }
        return num;
      }},
      {data: 'contractID', render: function(data, type) {
        if (type === 'display') {
          return `<span data-label="Contract ID">${data}</span>`;
        }
        return data;
      }},
      {data: 'tenantName', render: function(data, type) {
        if (type === 'display') {
          return `<span data-label="Tenant">${data}</span>`;
        }
        return data;
      }},
      {data: 'formattedStallId', render: function(data, type) {
        if (type === 'display') {
          return `<span data-label="Stall">${data}</span>`;
        }
        return data;
      }},
      {data: 'marketplace', render: function(data, type) {
        if (type === 'display') {
          return `<span data-label="Marketplace">${data}</span>`;
        }
        return data;
      }},
      {data: 'rentalFee', render: function(data, type) {
        const amount = '₱' + data;
        if (type === 'display') {
          return `<span data-label="Monthly Rent">${amount}</span>`;
        }
        return amount;
      }},
      {data: 'startDate', render: function(data, type) {
        if (type === 'display') {
          return `<span data-label="Start Date">${data}</span>`;
        }
        return data;
      }},
      {data: 'endDate', render: function(data, type) {
        if (type === 'display') {
          return `<span data-label="End Date">${data}</span>`;
        }
        return data;
      }},
      {data: 'daysRemaining', render: function(data, type) {
        let content = '—';
        if (data !== null) {
          const daysClass = data <= 30 ? 'days-warning' : 'days-ok';
          content = `<span class="${daysClass}">${data} days</span>`;
        }
        if (type === 'display') {
          return `<span data-label="Days Remaining">${content}</span>`;
        }
        return content;
      }},
      {data: 'contractStatus', render: function(data, type) {
        const statusClass = 'status-' + data.toLowerCase();
        const badge = `<span class="badge status-badge ${statusClass}">${data}</span>`;
        if (type === 'display') {
          return `<span data-label="Status">${badge}</span>`;
        }
        return badge;
      }},
      {data: null, orderable: false, className: 'text-center', render: function(data, type, row) {
        let html = '<div class="d-flex gap-1 justify-content-center flex-wrap">';
        html += `<button class="btn btn-sm btn-outline-primary view-contract" data-id="${data.contractID}" title="View Details"><i class="bx bx-show"></i></button>`;
        if (data.canRenew) {
          html += `<a href="/admins/leases/${data.contractID}/renew" class="btn btn-sm btn-outline-success" title="Renew"><i class="bx bx-refresh"></i></a>`;
        }
        if (data.canTerminate) {
          html += `<a href="/admins/leases/${data.contractID}/terminate" class="btn btn-sm btn-outline-danger" title="Terminate"><i class="bx bx-x"></i></a>`;
        }
        html += `<button class="btn btn-sm btn-outline-warning archive-contract" data-id="${data.contractID}" title="Archive"><i class="bx bx-archive"></i></button>`;
        html += '</div>';
        
        if (type === 'display') {
          return `<span data-label="Actions">${html}</span>`;
        }
        return html;
      }}
    ],
    order: [[6, 'desc']],
    pageLength: 10,
    responsive: true,
    scrollX: false,
    autoWidth: false,
    dom: 'lrtip',
    language: { lengthMenu: "Show _MENU_ entries" },
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    columnDefs: [
      { targets: -1, className: 'text-nowrap', responsivePriority: 1 },
      { targets: 9, responsivePriority: 2 },
      { targets: 2, responsivePriority: 3 },
      { targets: 1, responsivePriority: 4 },
      { targets: 3, responsivePriority: 5 },
      { targets: 4, responsivePriority: 6 },
      { targets: 5, responsivePriority: 7 },
      { targets: 6, responsivePriority: 8 },
      { targets: 7, responsivePriority: 9 },
      { targets: 8, responsivePriority: 10 }
    ],
  });

  // Replace native select with Bootstrap dropdown and align action buttons
  function replaceLengthSelectWithDropdown() {
    const $lengthWrapper = $('.dataTables_length');
    const $nativeSelect = $lengthWrapper.find('select');
    
    if ($lengthWrapper.length > 0 && !$lengthWrapper.data('custom-replaced')) {
      const currentValue = $nativeSelect.val();
      
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
      
      const $actionButtonsGroup = $('#actionButtonsGroup');
      
      $nativeSelect.hide().after($customDropdown);
      
      const $dataTablesWrapper = $lengthWrapper.closest('.dataTables_wrapper');
      if ($dataTablesWrapper.length > 0) {
        const $lengthRow = $lengthWrapper.closest('.row, div').first();
        const $lengthParent = $lengthRow.length > 0 ? $lengthRow : $lengthWrapper.parent();
        
        $lengthParent.css({
          'display': 'flex',
          'justify-content': 'space-between',
          'align-items': 'center'
        });
        
        if ($actionButtonsGroup.length > 0) {
          $actionButtonsGroup.removeAttr('style').show();
          $lengthParent.append($actionButtonsGroup);
        }
      }
      
      $customDropdown.find('.dropdown-item').on('click', function(e) {
        e.preventDefault();
        const value = parseInt($(this).data('value'));
        const displayValue = value === -1 ? 'All' : value;
        $customDropdown.find('.dropdown-toggle').html(displayValue);
        table.page.len(value).draw();
      });
      
      $lengthWrapper.data('custom-replaced', true);
    }
  }

  replaceLengthSelectWithDropdown();
  table.on('draw', replaceLengthSelectWithDropdown);

  // Search
  $('#leasesSearch').on('keyup', function() {
    table.ajax.reload();
  });

  // Status tabs
  $('#statusTabs').on('click', '.status-tab', function() {
    $('#statusTabs .status-tab').removeClass('active');
    $(this).addClass('active');
    table.ajax.reload();
  });

  // View contract details
  $(document).on('click', '.view-contract', function() {
    const contractId = $(this).data('id');
    const drawer = new bootstrap.Offcanvas(document.getElementById('viewContractDrawer'));
    
    $('#contractDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
    drawer.show();
    
    $.ajax({
      url: `/admins/leases/${contractId}`,
      method: 'GET',
      success: function(response) {
        if (response.success) {
          const c = response.contract;
          const t = response.tenant;
          const s = response.stall;
          
          let html = `
            <div class="mb-4">
              <h6 class="text-muted mb-2">Contract Information</h6>
              <p class="mb-1"><strong>Contract ID:</strong> ${c.contractID}</p>
              <p class="mb-1"><strong>Status:</strong> <span class="badge status-${c.contractStatus.toLowerCase()}">${c.contractStatus}</span></p>
              <p class="mb-1"><strong>Start Date:</strong> ${c.startDate || 'N/A'}</p>
              <p class="mb-1"><strong>End Date:</strong> ${c.endDate || 'No end date'}</p>
            </div>
            
            <div class="mb-4">
              <h6 class="text-muted mb-2">Tenant Information</h6>
              <p class="mb-1"><strong>Name:</strong> ${t ? t.name : 'N/A'}</p>
              <p class="mb-1"><strong>Email:</strong> ${t ? t.email : 'N/A'}</p>
              <p class="mb-1"><strong>Contact:</strong> ${t ? t.contactNo : 'N/A'}</p>
            </div>
            
            <div class="mb-4">
              <h6 class="text-muted mb-2">Stall Information</h6>
              <p class="mb-1"><strong>Stall:</strong> ${s ? s.formattedStallId : 'N/A'}</p>
              <p class="mb-1"><strong>Marketplace:</strong> ${s ? s.marketplace : 'N/A'}</p>
              <p class="mb-1"><strong>Monthly Rent:</strong> ₱${s ? parseFloat(s.rentalFee).toFixed(2) : '0.00'}</p>
            </div>
            
            <div class="mb-4">
              <h6 class="text-muted mb-2">Related Records</h6>
              <p class="mb-1"><strong>Bills:</strong> ${response.billsCount} bill(s)</p>
              <p class="mb-1"><strong>Feedback:</strong> ${response.feedbacksCount} feedback(s)</p>
            </div>
          `;
          
          if (c.customReason) {
            html += `<div class="alert alert-info"><strong>Note:</strong> ${c.customReason}</div>`;
          }
          
          $('#contractDetailsContent').html(html);
        }
      },
      error: function() {
        $('#contractDetailsContent').html('<div class="alert alert-danger">Failed to load contract details.</div>');
      }
    });
  });

  // Renew contract
  $(document).on('click', '.renew-contract', function() {
    const contractId = $(this).data('id');
    $('#renewContractId').val(contractId);
    $('#renewContractModal').modal('show');
  });

  $('#renewContractForm').on('submit', function(e) {
    e.preventDefault();
    const contractId = $('#renewContractId').val();
    const months = $('#renewMonths').val();
    
    $.ajax({
      url: `/admins/leases/${contractId}/renew`,
      method: 'POST',
      data: { months: months },
      success: function(response) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: response.message,
          toast: true,
          position: 'top',
          showConfirmButton: false,
          timer: 3000
        });
        $('#renewContractModal').modal('hide');
        table.ajax.reload();
      },
      error: function(xhr) {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: xhr.responseJSON?.message || 'Failed to renew contract.',
          animation: false
        });
      }
    });
  });

  // Terminate contract
  $(document).on('click', '.terminate-contract', function() {
    const contractId = $(this).data('id');
    $('#terminateContractId').val(contractId);
    $('#terminateReason').val('');
    $('#terminateContractModal').modal('show');
  });

  $('#terminateContractForm').on('submit', function(e) {
    e.preventDefault();
    const contractId = $('#terminateContractId').val();
    const reason = $('#terminateReason').val();
    
    Swal.fire({
      title: 'Are you sure?',
      text: 'This will terminate the contract and set the stall to Vacant.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, terminate it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/admins/leases/${contractId}/terminate`,
          method: 'POST',
          data: { reason: reason },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response.message,
              toast: true,
              position: 'top',
              showConfirmButton: false,
              timer: 3000
            });
            $('#terminateContractModal').modal('hide');
            table.ajax.reload();
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: xhr.responseJSON?.message || 'Failed to terminate contract.',
              animation: false
            });
          }
        });
      }
    });
  });

  // Archive contract
  $(document).on('click', '.archive-contract', function() {
    const contractId = $(this).data('id');
    Swal.fire({
      title: 'Archive Contract?',
      text: 'This will move the contract to archived items.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#f0ad4e',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, archive'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('admins.leases.archive', ':id') }}".replace(':id', contractId),
          method: 'POST',
          data: { _token: $('meta[name="csrf-token"]').attr('content') },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Archived',
              text: response.message || 'Contract archived.',
              toast: true,
              position: 'top',
              showConfirmButton: false,
              timer: 2000
            });
            table.ajax.reload();
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: xhr.responseJSON?.message || 'Failed to archive contract.'
            });
          }
        });
      }
    });
  });

});
</script>
@endpush


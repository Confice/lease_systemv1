@extends('layouts.tenant_app')

@section('title', 'My Leases')
@section('page-title', 'My Leases')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
  .lease-card {
    border: 2px solid #6B7A56;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
  }
  
  .lease-card:hover {
    border-color: #7F9267;
    box-shadow: 0 4px 12px rgba(127, 146, 103, 0.15);
  }
  
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
  .alert-renewal {
    background-color: rgba(255, 193, 7, 0.1);
    border-left: 4px solid #ffc107;
  }

  /* Table width and overflow control */
  .table-responsive {
    overflow-x: auto;
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
    width: 100px;
    min-width: 100px;
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

  /* Hide less important columns on smaller screens */
  @media (max-width: 1200px) {
    #leasesTable th:nth-child(4),
    #leasesTable td:nth-child(4),
    #leasesTable th:nth-child(6),
    #leasesTable td:nth-child(6),
    #leasesTable th:nth-child(7),
    #leasesTable td:nth-child(7) {
      display: none;
    }
  }

  @media (max-width: 992px) {
    #leasesTable th:nth-child(5),
    #leasesTable td:nth-child(5) {
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
    
    #statusFilter {
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
        <input type="text" id="leasesSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search by contract ID or stall..." aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;">
      </div>
    </div>
    <!-- /Search Bar -->
  </div>

  <div class="card-body">
    <!-- Action Buttons Group (aligned with DataTables length selector) -->
    <div class="d-flex align-items-center gap-2" id="actionButtonsGroup" style="display: none !important;">
      <!-- Status Filter -->
      <select id="statusFilter" class="form-select" style="max-width: 200px;">
        <option value="all">All Status</option>
        <option value="Active">Active</option>
        <option value="Expiring">Expiring</option>
        <option value="Terminated">Terminated</option>
      </select>

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
            <th>Stall</th>
            <th>Marketplace</th>
            <th>Monthly Rent</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Days Remaining</th>
            <th>Status</th>
            <th>Pending Bills</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<!-- Renewal Alert -->
<div id="renewalAlert" class="alert alert-renewal alert-dismissible fade d-none" role="alert">
  <i class="bx bx-info-circle me-2"></i>
  <strong>Renewal Notice:</strong> You have lease(s) expiring soon. Please contact the admin for renewal.
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  
  var table = $('#leasesTable').DataTable({
    ajax: {
      url: "{{ route('tenants.leases.data') }}",
      data: function(d) {
        d.status = $('#statusFilter').val();
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
      {data: 'pendingBills', render: function(data, type) {
        let content = '<span class="text-muted">None</span>';
        if (data > 0) {
          content = `<span class="badge bg-danger">${data} pending</span>`;
        }
        if (type === 'display') {
          return `<span data-label="Pending Bills">${content}</span>`;
        }
        return content;
      }}
    ],
    order: [[5, 'desc']],
    pageLength: 10,
    responsive: true,
    scrollX: false,
    autoWidth: false,
    dom: 'lrtip',
    language: {
      lengthMenu: "Show _MENU_ entries",
      emptyTable: "No leases yet. Once your lease is active, it will appear here.",
      zeroRecords: "No matching leases found."
    },
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    columnDefs: [
      { targets: '_all', className: 'text-nowrap' }
    ],
    drawCallback: function() {
      // Check for leases needing renewal
      let needsRenewal = false;
      table.rows({search: 'applied'}).every(function() {
        const data = this.data();
        if (data.needsRenewal) {
          needsRenewal = true;
          return false; // break
        }
      });
      
      if (needsRenewal) {
        $('#renewalAlert').removeClass('d-none').addClass('show');
      } else {
        $('#renewalAlert').addClass('d-none');
      }
    }
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

  // Status filter
  $('#statusFilter').on('change', function() {
    table.ajax.reload();
  });
});
</script>
@endpush


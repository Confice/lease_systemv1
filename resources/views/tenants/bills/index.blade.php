@extends('layouts.tenant_app')

@section('title', 'My Bills')
@section('page-title', 'My Bills')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
  .bill-card {
    border: 2px solid #6B7A56;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
  }
  
  .bill-card:hover {
    border-color: #7F9267;
    box-shadow: 0 4px 12px rgba(127, 146, 103, 0.15);
  }
  
  .status-badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
  }
  .status-pending {
    background-color: rgba(255, 193, 7, 0.15);
    color: #cc9a00;
  }
  .status-paid {
    background-color: rgba(25, 135, 84, 0.15);
    color: #198754;
  }
  .status-due {
    background-color: rgba(220, 53, 69, 0.15);
    color: #dc3545;
  }
  .status-invalid {
    background-color: rgba(108, 117, 125, 0.15);
    color: #6c757d;
  }
  
  .upload-area {
    border: 2px dashed #7F9267;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    background-color: #EFEFEA;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .upload-area:hover {
    background-color: rgba(127, 146, 103, 0.1);
    border-color: #6B7A56;
  }
  
  .upload-area.dragover {
    background-color: rgba(127, 146, 103, 0.2);
    border-color: #6B7A56;
  }
  
  /* Ensure modals are clickable and have proper z-index */
  .modal {
    z-index: 1060 !important;
  }
  
  .modal-backdrop {
    z-index: 1059 !important;
  }
  
  .modal-dialog,
  .modal-content {
    pointer-events: auto !important;
  }
  
  /* Prevent backdrop from blocking modal interactions */
  .modal.show {
    pointer-events: auto !important;
  }
  
  .modal.show .modal-dialog {
    pointer-events: auto !important;
  }
  
  .modal.show .modal-content {
    pointer-events: auto !important;
  }
  
  /* Ensure body is scrollable when modal is closed */
  body:not(.modal-open) {
    overflow: auto !important;
    padding-right: 0 !important;
  }
  
  /* Prevent multiple backdrops from stacking */
  .modal-backdrop {
    z-index: 1059 !important;
  }
  
  .modal-backdrop + .modal-backdrop {
    display: none !important;
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
        <input type="text" id="billsSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search by bill ID or stall..." aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;">
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
        <option value="Pending">Pending</option>
        <option value="Paid">Paid</option>
        <option value="Due">Due</option>
        <option value="Invalid">Invalid</option>
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
      <table id="billsTable" class="table table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Bill ID</th>
            <th>Stall</th>
            <th>Marketplace</th>
            <th>Amount</th>
            <th>Due Date</th>
            <th>Date Paid</th>
            <th>Status</th>
            <th>Payment Proof</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<!-- View Payment Proof Modal -->
<div class="modal fade" id="viewProofModal" tabindex="-1" aria-labelledby="viewProofModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Payment Proof</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="proofImage" src="" alt="Payment Proof" class="img-fluid" style="max-height: 500px;">
        <iframe id="proofPdf" src="" style="width: 100%; height: 500px; display: none;"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
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
  
  var table = $('#billsTable').DataTable({
    ajax: {
      url: "{{ route('tenants.bills.data') }}",
      data: function(d) {
        d.status = $('#statusFilter').val();
        d.search = $('#billsSearch').val();
      }
    },
    columns: [
      {data: null, orderable: false, render: function(data, type, row, meta) {
        return meta.row + 1;
      }},
      {data: 'billID'},
      {data: 'formattedStallId'},
      {data: 'marketplace'},
      {data: 'amount', render: function(data) {
        return '₱' + data;
      }},
      {data: 'dueDate'},
      {data: 'datePaid', render: function(data) {
        return data || '—';
      }},
      {data: 'status', render: function(data) {
        const statusClass = 'status-' + data.toLowerCase();
        return `<span class="badge status-badge ${statusClass}">${data}</span>`;
      }},
      {data: 'hasPaymentProof', orderable: false, render: function(data) {
        return data ? '<i class="bx bx-check-circle text-success"></i>' : '<i class="bx bx-x-circle text-muted"></i>';
      }},
      {data: null, orderable: false, className: 'text-center', render: function(data) {
        let html = '<div class="d-flex gap-1 justify-content-center">';
        if (data.hasPaymentProof) {
          html += `<button class="btn btn-sm btn-outline-primary view-proof" data-url="${data.paymentProofUrl}" title="View Proof"><i class="bx bx-image"></i></button>`;
        }
        if (data.canUpload) {
          html += `<a href="/bills/${data.billID}/upload" class="btn btn-sm btn-primary" title="Upload Proof"><i class="bx bx-upload"></i></a>`;
        }
        html += '</div>';
        return html;
      }}
    ],
    order: [[5, 'desc']],
    pageLength: 10,
    responsive: true,
    dom: 'lrtip',
    language: { lengthMenu: "Show _MENU_ entries" },
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
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
  $('#billsSearch').on('keyup', function() {
    table.ajax.reload();
  });

  // Status filter
  $('#statusFilter').on('change', function() {
    table.ajax.reload();
  });

  // View payment proof
  $(document).on('click', '.view-proof', function() {
    const url = $(this).data('url');
    const isPdf = url.toLowerCase().endsWith('.pdf');
    
    if (isPdf) {
      $('#proofImage').hide();
      $('#proofPdf').attr('src', url).show();
    } else {
      $('#proofPdf').hide();
      $('#proofImage').attr('src', url).show();
    }
    
    // Get modal element
    const modalElement = document.getElementById('viewProofModal');
    
    // Remove any existing modal instances
    const existingModal = bootstrap.Modal.getInstance(modalElement);
    if (existingModal) {
      existingModal.dispose();
    }
    
    // Create new modal instance
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
    
    // Set z-index after modal is shown
    setTimeout(function() {
      $(modalElement).css({
        'z-index': '1060',
        'pointer-events': 'auto'
      });
      $(modalElement).find('.modal-dialog, .modal-content').css('pointer-events', 'auto');
      $('.modal-backdrop').last().css('z-index', '1059');
    }, 100);
    
    // Also set on shown event
    $(modalElement).off('shown.bs.modal hidden.bs.modal').on('shown.bs.modal', function() {
      $(this).css({
        'z-index': '1060',
        'pointer-events': 'auto'
      });
      $(this).find('.modal-dialog, .modal-content').css('pointer-events', 'auto');
      $('.modal-backdrop').last().css('z-index', '1059');
    });
    
    // Clean up when modal is hidden
    $(modalElement).on('hidden.bs.modal', function() {
      // Remove any leftover backdrops
      $('.modal-backdrop').remove();
      // Remove modal-open class from body
      $('body').removeClass('modal-open');
      // Remove padding that Bootstrap adds
      $('body').css('padding-right', '');
      // Remove overflow hidden
      $('body').css('overflow', '');
    });
  });
  
  // Global cleanup for any modal close (X button, Cancel button, backdrop click)
  $(document).on('hidden.bs.modal', '.modal', function() {
    // Remove any leftover backdrops
    $('.modal-backdrop').remove();
    // Remove modal-open class from body
    $('body').removeClass('modal-open');
    // Remove padding and overflow
    $('body').css({
      'padding-right': '',
      'overflow': ''
    });
  });
});
</script>
@endpush


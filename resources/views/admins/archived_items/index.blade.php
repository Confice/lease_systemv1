@extends('layouts.admin_app')

@section('title','Archived Items')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <!-- Search Bar -->
        <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
            <div class="position-relative w-100">
                <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 1; color: #7F9267;">
                    <i class="bx bx-search fs-5"></i>
                </span>
                <input type="text" id="archivedItemsSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search..." aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;">
            </div>
        </div>
        <!-- /Search Bar -->
    </div>

    <div class="card-body">
        <!-- Action Buttons Group (aligned with DataTables length selector) -->
        <div class="d-flex align-items-center gap-2" id="actionButtonsGroup" style="display: none !important;">
            <!-- Restore Button (hidden by default, shown when rows are selected) -->
            <button id="btnRestoreSelected" class="btn btn-danger d-none" style="display: none !important;">
                <i class="bx bx-undo me-1"></i> Restore
            </button>
            <!-- Delete Permanently Button (hidden by default, shown when rows are selected) -->
            <button id="btnDeletePermanently" class="btn btn-outline-danger d-none" style="display: none !important;">
                <i class="bx bx-trash me-1"></i> Delete permanently
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
      <table id="archivedItemsTable" class="table table-hover mb-0">
        <thead>
          <tr>
            <th></th>
            <th class="text-center">
              <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Select All">
            </th>
            <th>#</th>
            <th>Reference ID</th>
            <th>Archived At</th>
            <th>Archived From</th>
            <th>Archived By</th>
            <th>Action</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('sneat/assets/css/users-page-improvements.css') }}">
<style>
  /* Keep text black on row hover */
  #archivedItemsTable tbody tr:hover td {
    color: #000000 !important;
  }
  
  #archivedItemsTable tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02) !important;
  }
  
  /* Restore button styling to match Add New Record button */
  #btnRestoreSelected {
    background-color: #6B7A56 !important;
    border-color: #6B7A56 !important;
    color: #FFFFFF !important;
    transform: none !important;
  }
  
  #btnRestoreSelected:hover {
    background-color: #5A6749 !important;
    border-color: #5A6749 !important;
    color: #FFFFFF !important;
    transform: none !important;
  }
  
  #btnRestoreSelected:focus,
  #btnRestoreSelected:active {
    background-color: #5A6749 !important;
    border-color: #5A6749 !important;
    color: #FFFFFF !important;
    box-shadow: 0 0 0 0.2rem rgba(107, 122, 86, 0.5) !important;
    transform: none !important;
  }
  .btn-archive-restore-one, .btn-archive-delete-one { padding: 0.25rem 0.5rem; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  var table = $('#archivedItemsTable').DataTable({
    ajax: "{{ route('admins.archived-items.data') }}",
    columns: [
      {data:null, className:'control', orderable:false, render:()=>''},
      {data:null, orderable:false, className:'text-center', render:function(d){
        return `<input type="checkbox" class="form-check-input row-checkbox" data-id="${d.id}" data-type="${d.module_type}">`;
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
      {data:'reference_id'},
      {data:'archived_at', render:function(d){
        if (!d) return '-';
        // Parse the date string (already in Philippine time from backend)
        const date = new Date(d);
        // Format to Philippine timezone
        return date.toLocaleString('en-US', {
          timeZone: 'Asia/Manila',
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          hour12: true
        });
      }},
      {data:'archived_from'},
      {data:'archived_by', defaultContent:'—'},
      {data:'action', defaultContent:'Archived'},
      {data:null, orderable:false, className:'text-center', render:function(d){
        return `<button type="button" class="btn btn-sm btn-success btn-archive-restore-one" data-id="${d.id}" data-type="${d.module_type}" title="Restore"><i class="bx bx-undo"></i></button>
                <button type="button" class="btn btn-sm btn-outline-danger btn-archive-delete-one" data-id="${d.id}" data-type="${d.module_type}" title="Delete permanently"><i class="bx bx-trash"></i></button>`;
      }}
    ],
    order:[[4,'desc']], // Sort by Archived At descending (most recent first)
    pageLength:10,
    responsive:true,
    dom: 'lrtip', // l = length, r = processing, t = table, i = info, p = pagination
    language: {
      lengthMenu: "Show _MENU_ entries"
    },
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
  });

  // Bind search bar to DataTable
  $('#archivedItemsSearch').on('keyup', function(){
    const query = $(this).val();
    table.search(query).draw();
  });

  // Clear search when input is cleared
  $('#archivedItemsSearch').on('input', function(){
    if ($(this).val() === '') {
      table.search('').draw();
    }
  });

  // Replace native select with Bootstrap dropdown and align action buttons
  function replaceLengthSelectWithDropdown() {
    const $lengthWrapper = $('.dataTables_length');
    const $nativeSelect = $lengthWrapper.find('select');
    
    if ($nativeSelect.length > 0 && !$nativeSelect.data('custom-replaced')) {
      const currentValue = $nativeSelect.val();
      
      // Create custom dropdown HTML
      const $customDropdown = $(`
        <div class="dropdown d-inline-block ms-2">
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
      
      // Replace native select with custom dropdown
      $nativeSelect.hide().after($customDropdown);
      
      // Handle dropdown clicks
      $customDropdown.find('.dropdown-item').on('click', function(e) {
        e.preventDefault();
        const value = parseInt($(this).data('value'));
        const displayValue = value === -1 ? 'All' : value;
        $customDropdown.find('.dropdown-toggle').html(displayValue);
        table.page.len(value).draw();
      });
      
      // Mark as replaced
      $nativeSelect.data('custom-replaced', true);
    }
  }

  // Replace dropdown after table initialization
  replaceLengthSelectWithDropdown();

  // Store selected IDs across page changes
  let selectedItems = new Map();

  // Select All Checkbox
  $('#selectAllCheckbox').on('click', function() {
    const isChecked = $(this).is(':checked');
    $('.row-checkbox').each(function() {
      const itemId = $(this).data('id');
      const itemType = $(this).data('type');
      if (isChecked) {
        selectedItems.set(`${itemType}-${itemId}`, { id: itemId, type: itemType });
      } else {
        selectedItems.delete(`${itemType}-${itemId}`);
      }
      $(this).prop('checked', isChecked);
    });
    updateRestoreButtonVisibility();
  });

  // Individual checkbox change
  $(document).on('change', '.row-checkbox', function() {
    const itemId = $(this).data('id');
    const itemType = $(this).data('type');
    const key = `${itemType}-${itemId}`;
    
    if ($(this).is(':checked')) {
      selectedItems.set(key, { id: itemId, type: itemType });
    } else {
      selectedItems.delete(key);
    }
    
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    updateRestoreButtonVisibility();
  });

  // Restore checkbox state after table redraw
  table.on('draw', function() {
    $('.row-checkbox').each(function() {
      const itemId = $(this).data('id');
      const itemType = $(this).data('type');
      const key = `${itemType}-${itemId}`;
      $(this).prop('checked', selectedItems.has(key));
    });
    
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    updateRestoreButtonVisibility();
  });

  function updateRestoreButtonVisibility() {
    const checkedCount = $('.row-checkbox:checked').length;
    if (checkedCount > 0) {
      $('#btnRestoreSelected').removeClass('d-none').css('display', '');
      $('#btnDeletePermanently').removeClass('d-none').css('display', '');
    } else {
      $('#btnRestoreSelected').addClass('d-none').css('display', 'none !important');
      $('#btnDeletePermanently').addClass('d-none').css('display', 'none !important');
    }
  }

  // Restore selected items
  $('#btnRestoreSelected').on('click', function() {
    const checkedItems = [];
    $('.row-checkbox:checked').each(function() {
      checkedItems.push({
        id: $(this).data('id'),
        type: $(this).data('type')
      });
    });

    if (checkedItems.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Selection',
        text: 'Please select at least one item to restore.',
        animation: false,
        showClass: { popup: '' },
        hideClass: { popup: '' }
      });
      return;
    }

    Swal.fire({
      title: 'Restore Items?',
      text: `Are you sure you want to restore ${checkedItems.length} item(s)?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#7F9267',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, restore them!',
      animation: false,
      showClass: { popup: '' },
      hideClass: { popup: '' }
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('admins.archived-items.restore') }}",
          method: 'POST',
          data: {
            items: checkedItems
          },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response.message || `${checkedItems.length} item(s) restored successfully.`,
              toast: true,
              position: 'top',
              showConfirmButton: false,
              showCloseButton: true,
              timer: 2000,
              timerProgressBar: true
            }).then(() => {
              // Clear selections
              selectedItems.clear();
              $('#selectAllCheckbox').prop('checked', false);
              // Reload table
              table.ajax.reload();
            });
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: xhr.responseJSON?.message || 'Failed to restore items. Please try again.',
              animation: false,
              showClass: { popup: '' },
              hideClass: { popup: '' }
            });
          }
        });
      }
    });
  });

  // Single-row Restore
  $(document).on('click', '.btn-archive-restore-one', function() {
    const id = $(this).data('id');
    const type = $(this).data('type');
    Swal.fire({
      title: 'Restore this item?',
      text: 'The item will be moved back to its module.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#7F9267',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, restore',
      animation: false,
      showClass: { popup: '' },
      hideClass: { popup: '' }
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('admins.archived-items.restore') }}",
          method: 'POST',
          data: { items: [{ id: id, type: type }] },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Restored',
              text: response.message || 'Item restored successfully.',
              toast: true,
              position: 'top',
              showConfirmButton: false,
              showCloseButton: true,
              timer: 2000,
              timerProgressBar: true
            }).then(() => { table.ajax.reload(); });
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: xhr.responseJSON?.message || 'Failed to restore.',
              animation: false,
              showClass: { popup: '' },
              hideClass: { popup: '' }
            });
          }
        });
      }
    });
  });

  // Single-row Delete permanently
  $(document).on('click', '.btn-archive-delete-one', function() {
    const id = $(this).data('id');
    const type = $(this).data('type');
    Swal.fire({
      title: 'Delete permanently?',
      text: 'This cannot be undone. The item will be removed forever.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete permanently',
      animation: false,
      showClass: { popup: '' },
      hideClass: { popup: '' }
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('admins.archived-items.delete') }}",
          method: 'POST',
          data: { items: [{ id: id, type: type }] },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Deleted',
              text: response.message || 'Item permanently deleted.',
              toast: true,
              position: 'top',
              showConfirmButton: false,
              showCloseButton: true,
              timer: 2000,
              timerProgressBar: true
            }).then(() => { table.ajax.reload(); });
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: xhr.responseJSON?.message || 'Failed to delete.',
              animation: false,
              showClass: { popup: '' },
              hideClass: { popup: '' }
            });
          }
        });
      }
    });
  });

  // Bulk Delete permanently
  $('#btnDeletePermanently').on('click', function() {
    const checkedItems = [];
    $('.row-checkbox:checked').each(function() {
      checkedItems.push({
        id: $(this).data('id'),
        type: $(this).data('type')
      });
    });
    if (checkedItems.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Selection',
        text: 'Please select at least one item to delete.',
        animation: false,
        showClass: { popup: '' },
        hideClass: { popup: '' }
      });
      return;
    }
    Swal.fire({
      title: 'Delete permanently?',
      text: `Are you sure you want to permanently delete ${checkedItems.length} item(s)? This cannot be undone.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete permanently',
      animation: false,
      showClass: { popup: '' },
      hideClass: { popup: '' }
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('admins.archived-items.delete') }}",
          method: 'POST',
          data: { items: checkedItems },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Deleted',
              text: response.message || 'Items permanently deleted.',
              toast: true,
              position: 'top',
              showConfirmButton: false,
              showCloseButton: true,
              timer: 2000,
              timerProgressBar: true
            }).then(() => {
              selectedItems.clear();
              $('#selectAllCheckbox').prop('checked', false);
              table.ajax.reload();
            });
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: xhr.responseJSON?.message || 'Failed to delete items.',
              animation: false,
              showClass: { popup: '' },
              hideClass: { popup: '' }
            });
          }
        });
      }
    });
  });

  // CSV Export
  $('#exportCsv').on('click', function(e) {
    e.preventDefault();
    window.location.href = "{{ route('admins.archived-items.export.csv') }}";
  });

  // PDF Export
  $('#exportPdf').on('click', function(e) {
    e.preventDefault();
    // TODO: Implement PDF export
    Swal.fire({
      icon: 'info',
      title: 'Export PDF',
      text: 'PDF export functionality will be implemented soon.',
      timer: 2000,
      showConfirmButton: false,
      animation: false,
      showClass: { popup: '' },
      hideClass: { popup: '' }
    });
  });
});
</script>
@endpush


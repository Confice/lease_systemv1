@extends('layouts.admin_app')

@section('title','Activity Logs')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <!-- Search Bar -->
        <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
            <div class="position-relative w-100">
                <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 1; color: #7F9267;">
                    <i class="bx bx-search fs-5"></i>
                </span>
                <input type="text" id="activityLogsSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search..." aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;">
            </div>
        </div>
        <!-- /Search Bar -->
    </div>

    <div class="card-body">
        <!-- Action Buttons Group (aligned with DataTables length selector) -->
        <div class="d-flex align-items-center gap-2" id="actionButtonsGroup" style="display: none !important;">
            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-label-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bx bx-filter me-1"></i> Filters
                </button>
                <ul class="dropdown-menu p-3" style="min-width: 300px;">
                    <li class="mb-2">
                        <label class="form-label small">Action Type</label>
                        <select id="filterActionType" class="form-select form-select-sm">
                            <option value="">All Actions</option>
                            <option value="Create">Create</option>
                            <option value="Update">Update</option>
                            <option value="Delete">Delete</option>
                            <option value="Login">Login</option>
                            <option value="Logout">Logout</option>
                            <option value="View">View</option>
                            <option value="Other">Other</option>
                        </select>
                    </li>
                    <li class="mb-2">
                        <label class="form-label small">Entity</label>
                        <select id="filterEntity" class="form-select form-select-sm">
                            <option value="">All Entities</option>
                            <option value="users">Users</option>
                            <option value="stalls">Stalls</option>
                            <option value="contracts">Contracts</option>
                            <option value="bills">Bills</option>
                            <option value="feedbacks">Feedbacks</option>
                            <option value="applications">Applications</option>
                        </select>
                    </li>
                    <li class="mb-2">
                        <label class="form-label small">Date From</label>
                        <input type="date" id="filterDateFrom" class="form-control form-control-sm">
                    </li>
                    <li class="mb-3">
                        <label class="form-label small">Date To</label>
                        <input type="date" id="filterDateTo" class="form-control form-control-sm">
                    </li>
                    <li>
                        <button id="btnApplyFilters" class="btn btn-sm btn-primary w-100">
                            <i class="bx bx-check me-1"></i> Apply Filters
                        </button>
                        <button id="btnClearFilters" class="btn btn-sm btn-outline-secondary w-100 mt-2">
                            <i class="bx bx-x me-1"></i> Clear
                        </button>
                    </li>
                </ul>
            </div>

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

    <div class="card-body">
        <div class="table-responsive">
            <table id="activityLogsTable" class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Description</th>
                        <th>User</th>
                        <th>Date & Time</th>
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
<style>
    /* Keep text black on row hover */
    #activityLogsTable tbody tr:hover td {
        color: #000000 !important;
    }
    
    #activityLogsTable tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02) !important;
    }

    /* Action type badges */
    .badge-action {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        font-weight: 600;
    }
    
    .badge-create { background-color: #28a745; color: #fff; }
    .badge-update { background-color: #ffc107; color: #000; }
    .badge-delete { background-color: #dc3545; color: #fff; }
    .badge-login { background-color: #17a2b8; color: #fff; }
    .badge-logout { background-color: #6c757d; color: #fff; }
    .badge-view { background-color: #007bff; color: #fff; }
    .badge-other { background-color: #6f42c1; color: #fff; }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-header > div {
            width: 100%;
            margin-top: 1rem;
        }
    }
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
    
    var table = $('#activityLogsTable').DataTable({
        ajax: {
            url: "{{ route('admins.activity-logs.data') }}",
            data: function(d) {
                d.actionType = $('#filterActionType').val();
                d.entity = $('#filterEntity').val();
                d.dateFrom = $('#filterDateFrom').val();
                d.dateTo = $('#filterDateTo').val();
                d.search = { value: $('#activityLogsSearch').val() };
            }
        },
        columns: [
            {
                data: 'activityID',
                orderable: true,
                render: function(data, type, row, meta) {
                    if (type === 'display' || type === 'type') {
                        const pageInfo = table.page.info();
                        return pageInfo.start + meta.row + 1;
                    }
                    return data;
                }
            },
            {
                data: 'actionType',
                render: function(data) {
                    const actionClass = 'badge-' + data.toLowerCase();
                    return `<span class="badge badge-action ${actionClass}">${data}</span>`;
                }
            },
            {
                data: 'entity',
                render: function(data) {
                    return `<span class="text-capitalize">${data}</span>`;
                }
            },
            {
                data: 'description',
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'user',
                render: function(data, type, row) {
                    return `<div>
                        <div class="fw-semibold">${data}</div>
                        <small class="text-muted">${row.userEmail}</small>
                    </div>`;
                }
            },
            {
                data: 'created_at',
                orderable: true,
                render: function(data, type, row) {
                    if (type === 'display' || type === 'type') {
                        return data;
                    }
                    return row.created_at_raw;
                }
            }
        ],
        order: [[5, 'desc']], // Sort by date descending (most recent first)
        pageLength: 25,
        responsive: true,
        dom: 'lrtip',
        language: {
            lengthMenu: "Show _MENU_ entries",
            emptyTable: "No activity logs found",
            zeroRecords: "No matching activity logs found"
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    });

    // Replace native select with Bootstrap dropdown
    function replaceLengthSelectWithDropdown() {
        const $lengthWrapper = $('.dataTables_length');
        const $nativeSelect = $lengthWrapper.find('select');
        
        if ($nativeSelect.length > 0 && !$nativeSelect.data('custom-replaced')) {
            const currentValue = $nativeSelect.val();
            
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
            
            $nativeSelect.hide().after($customDropdown);
            
            $customDropdown.find('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                const value = parseInt($(this).data('value'));
                const displayValue = value === -1 ? 'All' : value;
                $customDropdown.find('.dropdown-toggle').html(displayValue);
                table.page.len(value).draw();
            });
            
            $nativeSelect.data('custom-replaced', true);
        }
    }

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
    $('#activityLogsSearch').on('keyup', function() {
        table.ajax.reload();
    });

    // Apply filters
    $('#btnApplyFilters').on('click', function() {
        table.ajax.reload();
    });

    // Clear filters
    $('#btnClearFilters').on('click', function() {
        $('#filterActionType').val('');
        $('#filterEntity').val('');
        $('#filterDateFrom').val('');
        $('#filterDateTo').val('');
        table.ajax.reload();
    });

    // CSV Export
    $('#exportCsv').on('click', function(e) {
        e.preventDefault();
        window.location.href = "{{ route('admins.activity-logs.export.csv') }}";
    });

    // PDF Export (print)
    $('#exportPdf').on('click', function(e) {
        e.preventDefault();
        window.open("{{ route('admins.activity-logs.print') }}", '_blank');
    });
});
</script>
@endpush


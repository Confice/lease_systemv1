@extends('layouts.admin_app')

@section('title', 'Analytics & Reports')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <!-- Search Bar -->
                <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
                    <div class="position-relative w-100">
                        <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 1; color: #7F9267;">
                            <i class="bx bx-search fs-5"></i>
                        </span>
                        <input type="text" id="analyticsSearch" class="form-control rounded-pill ps-5 pe-4" placeholder="Search..." aria-label="Search" style="background-color: #EFEFEA; border-color: rgba(127, 146, 103, 0.2); color: #7F9267;" disabled>
                    </div>
                </div>
                <!-- /Search Bar -->
            </div>
            <div class="card-body">
                <!-- Action Buttons Group -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <button class="btn btn-label-primary" id="exportCsv">
                        <i class="bx bx-export me-1"></i> Export CSV
                    </button>
                    <button class="btn btn-label-primary" id="exportPdf">
                        <i class="bx bx-file me-1"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4" id="summaryStats">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Revenue</h6>
                        <h4 class="mb-0" id="totalRevenue">₱0.00</h4>
                    </div>
                    <div class="avatar" style="background-color: #7F9267; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bx bx-money text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Monthly Revenue</h6>
                        <h4 class="mb-0" id="monthlyRevenue">₱0.00</h4>
                    </div>
                    <div class="avatar" style="background-color: #28a745; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bx bx-calendar text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Active Contracts</h6>
                        <h4 class="mb-0" id="activeContracts">0</h4>
                    </div>
                    <div class="avatar" style="background-color: #007bff; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bx bx-file text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Tenants</h6>
                        <h4 class="mb-0" id="totalTenants">0</h4>
                    </div>
                    <div class="avatar" style="background-color: #ffc107; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bx bx-user text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row mb-4">
    <!-- Revenue Trends -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Revenue Trends</h5>
                <select id="revenuePeriod" class="form-select form-select-sm" style="width: auto;">
                    <option value="6">Last 6 Months</option>
                    <option value="12" selected>Last 12 Months</option>
                    <option value="24">Last 24 Months</option>
                </select>
            </div>
            <div class="card-body">
                <div id="revenueChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Occupancy Stats -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Occupancy Rate</h5>
            </div>
            <div class="card-body">
                <div id="occupancyChart" style="min-height: 350px;"></div>
                <div class="text-center mt-3">
                    <h4 id="occupancyRate">0%</h4>
                    <p class="text-muted mb-0">
                        <span id="occupiedStalls">0</span> Occupied / <span id="totalStalls">0</span> Total
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row mb-4">
    <!-- Payment Status -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Payment Status Distribution</h5>
            </div>
            <div class="card-body">
                <div id="paymentStatusChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Lease Expiration Timeline -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lease Expiration Timeline</h5>
            </div>
            <div class="card-body">
                <div id="expirationChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 3 -->
<div class="row mb-4">
    <!-- Marketplace Performance -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Marketplace Performance</h5>
            </div>
            <div class="card-body">
                <div id="marketplaceChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Tenant Retention -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tenant Retention</h5>
            </div>
            <div class="card-body">
                <div id="retentionChart" style="min-height: 350px;"></div>
                <div class="text-center mt-3">
                    <h4 id="retentionRate">0%</h4>
                    <p class="text-muted mb-0">Retention Rate</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Performing Stalls -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top 10 Performing Stalls</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="topStallsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Stall Number</th>
                                <th>Marketplace</th>
                                <th>Total Revenue</th>
                                <th>Bills Paid</th>
                            </tr>
                        </thead>
                        <tbody id="topStallsBody">
                            <tr><td colspan="5" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .avatar {
        flex-shrink: 0;
    }
    
    #summaryStats .card-body h4 {
        color: #7F9267;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<!-- jsPDF and html2canvas for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
$(function(){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    
    let charts = {};
    
    // Cache for PDF export
    let cachedSummary = null;
    let cachedTopStalls = [];
    let cachedRevenue = { labels: [], data: [] };

    // Load summary stats (total revenue, monthly revenue, active contracts, total tenants)
    function loadSummaryStats() {
        $.get("{{ route('admins.analytics.summary') }}")
            .done(function(data) {
                cachedSummary = data;
                var total = parseFloat(data.totalRevenue) || 0;
                var monthly = parseFloat(data.monthlyRevenue) || 0;
                $('#totalRevenue').text('₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#monthlyRevenue').text('₱' + monthly.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#activeContracts').text(data.activeContracts || 0);
                $('#totalTenants').text(data.totalTenants || 0);
            })
            .fail(function() {
                $('#totalRevenue').text('₱0.00');
                $('#monthlyRevenue').text('₱0.00');
                $('#activeContracts').text('0');
                $('#totalTenants').text('0');
            });
    }
    
    // Revenue Trends Chart
    function loadRevenueChart(period = 12) {
        $.get("{{ route('admins.analytics.revenue-trends') }}", { period: period }, function(data) {
            cachedRevenue = { labels: data.labels || [], data: data.data || [] };
            if (charts.revenue) charts.revenue.destroy();
            
            charts.revenue = new ApexCharts(document.querySelector("#revenueChart"), {
                series: [{
                    name: 'Revenue',
                    data: data.data
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: { show: true, tools: { download: true } }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: { categories: data.labels },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return '₱' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }
                },
                colors: ['#7F9267'],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return '₱' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }
                }
            });
            charts.revenue.render();
        });
    }
    
    // Occupancy Chart
    function loadOccupancyChart() {
        $.get("{{ route('admins.analytics.occupancy') }}", function(data) {
            $('#occupiedStalls').text(data.occupied);
            $('#totalStalls').text(data.total);
            $('#occupancyRate').text(data.occupancyRate + '%');
            
            if (charts.occupancy) charts.occupancy.destroy();
            
            charts.occupancy = new ApexCharts(document.querySelector("#occupancyChart"), {
                series: [data.occupied, data.vacant],
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: ['Occupied', 'Vacant'],
                colors: ['#28a745', '#dc3545'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: true }
            });
            charts.occupancy.render();
        });
    }
    
    // Payment Status Chart
    function loadPaymentStatusChart() {
        $.get("{{ route('admins.analytics.payment-status') }}", function(data) {
            if (charts.payment) charts.payment.destroy();
            
            charts.payment = new ApexCharts(document.querySelector("#paymentStatusChart"), {
                series: data.counts,
                chart: {
                    type: 'pie',
                    height: 350
                },
                labels: data.labels,
                colors: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: true }
            });
            charts.payment.render();
        });
    }
    
    // Expiration Timeline Chart
    function loadExpirationChart() {
        $.get("{{ route('admins.analytics.expiration-timeline') }}", function(data) {
            if (charts.expiration) charts.expiration.destroy();
            
            charts.expiration = new ApexCharts(document.querySelector("#expirationChart"), {
                series: [{
                    name: 'Expiring Contracts',
                    data: data.data
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: { borderRadius: 4, horizontal: false }
                },
                dataLabels: { enabled: true },
                xaxis: { categories: data.labels },
                colors: ['#ffc107']
            });
            charts.expiration.render();
        });
    }
    
    // Marketplace Performance Chart
    function loadMarketplaceChart() {
        $.get("{{ route('admins.analytics.marketplace-performance') }}", function(data) {
            if (charts.marketplace) charts.marketplace.destroy();
            
            charts.marketplace = new ApexCharts(document.querySelector("#marketplaceChart"), {
                series: [{
                    name: 'Revenue',
                    data: data.revenue
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: true }
                },
                plotOptions: {
                    bar: { borderRadius: 4, horizontal: true }
                },
                dataLabels: { enabled: true },
                xaxis: { categories: data.labels },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return '₱' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }
                },
                colors: ['#7F9267'],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return '₱' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }
                }
            });
            charts.marketplace.render();
        });
    }
    
    // Retention Chart
    function loadRetentionChart() {
        $.get("{{ route('admins.analytics.tenant-retention') }}", function(data) {
            $('#retentionRate').text(data.retentionRate + '%');
            
            if (charts.retention) charts.retention.destroy();
            
            charts.retention = new ApexCharts(document.querySelector("#retentionChart"), {
                series: [data.renewals, data.terminations, data.newContracts],
                chart: {
                    type: 'donut',
                    height: 250
                },
                labels: ['Renewals', 'Terminations', 'New Contracts'],
                colors: ['#28a745', '#dc3545', '#007bff'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: true }
            });
            charts.retention.render();
        });
    }
    
    // Top Performing Stalls
    function loadTopStalls() {
        $.get("{{ route('admins.analytics.top-stalls') }}")
            .done(function(data) {
                cachedTopStalls = data.stalls || [];
                let html = '';
                if (cachedTopStalls.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">No data available</td></tr>';
                } else {
                    cachedTopStalls.forEach((stall, index) => {
                        html += '<tr><td>' + (index + 1) + '</td><td><strong>' + (stall.stallNo || '') + '</strong></td><td>' + (stall.marketplace || '') + '</td><td>₱' + (stall.revenue || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td><td>' + (stall.billCount || 0) + '</td></tr>';
                    });
                }
                $('#topStallsBody').html(html);
            });
    }
    
    // Revenue period change
    $('#revenuePeriod').on('change', function() {
        loadRevenueChart($(this).val());
    });
    
    // Load all charts
    loadSummaryStats();
    loadRevenueChart();
    loadOccupancyChart();
    loadPaymentStatusChart();
    loadExpirationChart();
    loadMarketplaceChart();
    loadRetentionChart();
    loadTopStalls();
    
    // Export CSV (server returns file download)
    $('#exportCsv').on('click', function() {
        window.location.href = "{{ route('admins.analytics.export-csv') }}";
    });

    // Export PDF (data-based report using jsPDF, not screenshot)
    $('#exportPdf').on('click', function() {
        var jsPDF = window.jspdf && window.jspdf.jsPDF ? window.jspdf.jsPDF : (window.jspdf ? window.jspdf : window.jsPDF);
        if (!jsPDF) { alert('PDF library not loaded.'); return; }
        var doc = new jsPDF('p', 'mm', 'a4');
        var y = 15;
        var left = 15;
        var pageW = doc.internal.pageSize.getWidth();

        doc.setFontSize(16);
        doc.text('Analytics Report', left, y);
        y += 8;
        doc.setFontSize(10);
        doc.text('Generated: ' + new Date().toLocaleString(), left, y);
        y += 12;

        // Summary
        doc.setFontSize(12);
        doc.text('Summary', left, y);
        y += 8;
        doc.setFontSize(10);
        doc.text('Total Revenue: ₱' + (cachedSummary ? (parseFloat(cachedSummary.totalRevenue) || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 }) : '0.00'), left, y);
        y += 6;
        doc.text('Monthly Revenue: ₱' + (cachedSummary ? (parseFloat(cachedSummary.monthlyRevenue) || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 }) : '0.00'), left, y);
        y += 6;
        doc.text('Active Contracts: ' + (cachedSummary ? (cachedSummary.activeContracts || 0) : 0), left, y);
        y += 6;
        doc.text('Total Tenants: ' + (cachedSummary ? (cachedSummary.totalTenants || 0) : 0), left, y);
        y += 14;

        // Top Performing Stalls table
        doc.setFontSize(12);
        doc.text('Top 10 Performing Stalls', left, y);
        y += 8;
        if (cachedTopStalls && cachedTopStalls.length > 0) {
            var headers = ['#', 'Stall', 'Marketplace', 'Revenue', 'Bills'];
            var colW = [12, 35, 50, 45, 25];
            doc.setFontSize(9);
            var x = left;
            headers.forEach(function(h, i) { doc.text(h, x, y); x += colW[i]; });
            y += 6;
            cachedTopStalls.forEach(function(stall, idx) {
                if (y > 270) { doc.addPage(); y = 15; }
                x = left;
                doc.text(String(idx + 1), x, y); x += colW[0];
                doc.text(stall.stallNo || '', x, y); x += colW[1];
                doc.text((stall.marketplace || '').substring(0, 18), x, y); x += colW[2];
                doc.text('₱' + (stall.revenue || 0).toFixed(2), x, y); x += colW[3];
                doc.text(String(stall.billCount || 0), x, y);
                y += 6;
            });
            y += 10;
        } else {
            doc.text('No data', left, y);
            y += 10;
        }

        // Revenue trends (last 12 months) - summary line
        if (cachedRevenue && cachedRevenue.labels && cachedRevenue.labels.length) {
            if (y > 250) { doc.addPage(); y = 15; }
            doc.setFontSize(12);
            doc.text('Revenue Trends (Last 12 Months)', left, y);
            y += 8;
            doc.setFontSize(9);
            cachedRevenue.labels.forEach(function(label, i) {
                if (y > 275) { doc.addPage(); y = 15; }
                var val = cachedRevenue.data[i] || 0;
                doc.text(label + ': ₱' + Number(val).toLocaleString('en-PH', { minimumFractionDigits: 2 }), left, y);
                y += 5;
            });
        }

        doc.save('analytics-report-' + new Date().toISOString().split('T')[0] + '.pdf');
    });
});
</script>
@endpush


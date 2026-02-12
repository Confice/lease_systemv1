<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StallController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\TenantStallController;
use App\Http\Controllers\TenantApplicationController;
use App\Http\Controllers\MarketplaceMapController;
use App\Http\Controllers\ArchivedItemsController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantAccountController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\LandingContactController;
use App\Models\Feedback;

// =======================
// Helper Closure for Role Check
// =======================
function ensureRole($role) {
    $user = Auth::user();
    if (!$user || $user->role !== $role) {
        abort(403, 'Unauthorized');
    }
}

// Homepage
Route::get('/', function () {
    return view('welcome');
});

// Landing page contact form (public)
Route::post('/contact', [LandingContactController::class, 'submit'])->name('landing.contact.submit');

// Debug-only: verify basic Blade rendering (no auth)
if (config('app.debug')) {
    Route::get('/__render-check-public', function () {
        $html = view('tenants.__render_check', [
            'userId' => null,
            'role' => 'PUBLIC',
            'email' => null,
            'time' => now()->toDateTimeString(),
        ])->render();

        return response($html, 200)->header('X-Rendered-View', 'tenants.__render_check');
    })->name('render_check.public');
}

// =======================
// Authentication Routes
// =======================
// Registration (with rate limiting)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1')->name('register.store');

// Email Verification
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');

// Login & Logout (with rate limiting)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Setup (for first-time users)
Route::get('/setup-password/{token}', [AuthController::class, 'showSetupPasswordForm'])->name('setup.password');
Route::post('/setup-password', [AuthController::class, 'setupPassword'])->name('setup.password.store');
Route::post('/resend-setup-link', [AuthController::class, 'resendSetupPasswordLink'])->name('setup.password.resend');

// Forgot & Reset Password (with rate limiting)
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Change password (send link to current user's email) - requires login
Route::post('/send-password-change-link', [AuthController::class, 'sendPasswordChangeLink'])->middleware('auth')->name('password.change.link');

// =======================
// Lease Manager (Admin) Side
// =======================
Route::middleware(['auth', 'role:Lease Manager'])->group(function() {

    // Admin Dashboard
    Route::get('/admins/dashboard', [DashboardController::class, 'adminIndex'])->name('admins.dashboard');
    Route::get('/admins/dashboard/stats', [DashboardController::class, 'adminStats'])->name('admins.dashboard.stats');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('admins.users.index');
    Route::get('/users/data', [UserController::class, 'data'])->name('admins.data');
    Route::post('/users', [UserController::class, 'store'])->name('admins.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('admins.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admins.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admins.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admins.destroy');
    Route::get('/users/export/csv', [UserController::class, 'exportCsv'])->name('admins.export.csv'); 
    Route::get('/print', [UserController::class, 'print'])->name('admins.print');
    Route::post('/users/{user}/reset-password', [AuthController::class, 'sendResetPassword'])->name('admins.users.resetPassword');
    Route::post('/users/archive-multiple', [UserController::class, 'archiveMultiple'])->name('admins.users.archive-multiple');

    // Stall Management
    Route::get('/stalls', [StallController::class, 'index'])->name('admins.stalls.index');
    Route::get('/stalls/data', [StallController::class, 'data'])->name('admins.stalls.data');
    Route::get('/stalls/tenants/list', [StallController::class, 'getTenants'])->name('admins.stalls.tenants.list');
    Route::post('/stalls/assign-tenant', [StallController::class, 'assignTenant'])->name('admins.stalls.assign-tenant');
    Route::get('/stalls/export/csv', [StallController::class, 'exportCsv'])->name('admins.stalls.export.csv');
    Route::get('/stalls/print', [StallController::class, 'print'])->name('admins.stalls.print');
    Route::post('/stalls', [StallController::class, 'store'])->name('admins.stalls.store');
    Route::post('/stalls/archive-multiple', [StallController::class, 'archiveMultiple'])->name('admins.stalls.archive-multiple');
    
    // Requirements Management (must come before /stalls/{stall} to avoid route conflict)
    Route::get('/stalls/requirements', [StallController::class, 'requirementsIndex'])->name('admins.stalls.requirements.index');
    Route::get('/stalls/requirements/{id}', [StallController::class, 'requirementsShow'])->name('admins.stalls.requirements.show');
    Route::post('/stalls/requirements', [StallController::class, 'requirementsStore'])->name('admins.stalls.requirements.store');
    Route::put('/stalls/requirements/{id}', [StallController::class, 'requirementsUpdate'])->name('admins.stalls.requirements.update');
    Route::delete('/stalls/requirements/{id}', [StallController::class, 'requirementsDestroy'])->name('admins.stalls.requirements.destroy');
    
    Route::get('/stalls/{stall}/edit', [StallController::class, 'edit'])->name('admins.stalls.edit');
    Route::put('/stalls/{stall}', [StallController::class, 'update'])->name('admins.stalls.update');
    Route::get('/stalls/{stall}', [StallController::class, 'show'])->name('admins.stalls.show');

    // Marketplace Management
    Route::get('/marketplaces/create', [MarketplaceController::class, 'create'])->name('admins.marketplaces.create');
    Route::post('/marketplaces', [MarketplaceController::class, 'store'])->name('admins.marketplaces.store');

    // Marketplace Map
    Route::get('/admins/marketplace', [MarketplaceMapController::class, 'index'])->name('admins.marketplace.index');
    Route::get('/marketplace/hub', [MarketplaceMapController::class, 'hub'])->name('admins.marketplace.hub');
    Route::get('/marketplace/bazaar', [MarketplaceMapController::class, 'bazaar'])->name('admins.marketplace.bazaar');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admins.analytics.index');
    Route::get('/analytics/revenue-trends', [AnalyticsController::class, 'revenueTrends'])->name('admins.analytics.revenue-trends');
    Route::get('/analytics/occupancy', [AnalyticsController::class, 'occupancyStats'])->name('admins.analytics.occupancy');
    Route::get('/analytics/payment-status', [AnalyticsController::class, 'paymentStatus'])->name('admins.analytics.payment-status');
    Route::get('/analytics/expiration-timeline', [AnalyticsController::class, 'leaseExpirationTimeline'])->name('admins.analytics.expiration-timeline');
    Route::get('/analytics/marketplace-performance', [AnalyticsController::class, 'marketplacePerformance'])->name('admins.analytics.marketplace-performance');
    Route::get('/analytics/tenant-retention', [AnalyticsController::class, 'tenantRetention'])->name('admins.analytics.tenant-retention');
    Route::get('/analytics/top-stalls', [AnalyticsController::class, 'topPerformingStalls'])->name('admins.analytics.top-stalls');
    Route::get('/analytics/summary', [AnalyticsController::class, 'summaryStats'])->name('admins.analytics.summary');
    Route::get('/analytics/export-csv', [AnalyticsController::class, 'exportCsv'])->name('admins.analytics.export-csv');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admins.activity-logs.index');
    Route::get('/activity-logs/data', [ActivityLogController::class, 'data'])->name('admins.activity-logs.data');
    Route::get('/activity-logs/export/csv', [ActivityLogController::class, 'exportCsv'])->name('admins.activity-logs.export.csv');
    Route::get('/activity-logs/print', [ActivityLogController::class, 'print'])->name('admins.activity-logs.print');

    // Archived Items
    Route::get('/archived-items', [ArchivedItemsController::class, 'index'])->name('admins.archived-items.index');
    Route::get('/archived-items/data', [ArchivedItemsController::class, 'data'])->name('admins.archived-items.data');
    Route::post('/archived-items/restore', [ArchivedItemsController::class, 'restore'])->name('admins.archived-items.restore');
    Route::post('/archived-items/delete', [ArchivedItemsController::class, 'destroy'])->name('admins.archived-items.delete');
    Route::post('/archived-items/delete-all', [ArchivedItemsController::class, 'deleteAll'])->name('admins.archived-items.delete-all');
    Route::get('/archived-items/export/csv', [ArchivedItemsController::class, 'exportCsv'])->name('admins.archived-items.export.csv');
    Route::get('/archived-items/print', [ArchivedItemsController::class, 'print'])->name('admins.archived-items.print');

    // Prospective Tenants (Contracts moved here)
    Route::get('/tenants/prospective', [ContractController::class, 'index'])->name('admins.prospective-tenants.index');
    Route::get('/tenants/prospective/data', [ContractController::class, 'data'])->name('admins.prospective-tenants.data');
    Route::get('/tenants/prospective/{stall}/applications', [ContractController::class, 'applications'])->name('admins.prospective-tenants.applications');
    Route::get('/tenants/prospective/{stall}/applications/data', [ContractController::class, 'applicationsData'])->name('admins.prospective-tenants.applications.data');
    Route::get('/tenants/prospective/{stall}/applications/eligible-tenants', [ContractController::class, 'eligibleTenantsForStall'])->name('admins.prospective-tenants.applications.eligible-tenants');
    Route::post('/tenants/prospective/{stall}/applications/store-existing-tenant', [ContractController::class, 'storeApplicationForExistingTenant'])->name('admins.prospective-tenants.applications.store-existing-tenant');
    Route::get('/tenants/prospective/applications/{application}/details', [ContractController::class, 'applicationDetails'])->name('admins.prospective-tenants.application.details');
    Route::post('/tenants/prospective/applications/{application}/schedule-presentation', [ContractController::class, 'schedulePresentation'])->name('admins.prospective-tenants.schedule-presentation');
    Route::post('/tenants/prospective/applications/{application}/approve', [ContractController::class, 'approveApplication'])->name('admins.prospective-tenants.approve');
    Route::post('/tenants/prospective/applications/{application}/reject', [ContractController::class, 'rejectApplication'])->name('admins.prospective-tenants.reject');
    Route::post('/tenants/prospective/applications/{application}/reopen', [ContractController::class, 'reopenApplication'])->name('admins.prospective-tenants.reopen');
    Route::delete('/tenants/prospective/applications/{application}', [ContractController::class, 'deleteApplication'])->name('admins.prospective-tenants.application.delete');
    Route::post('/tenants/prospective/applications/{application}/remove-tenant', [ContractController::class, 'removeApprovedTenant'])->name('admins.prospective-tenants.application.remove-tenant');
    Route::get('/tenants/prospective/{stall}/applications/export/csv', [ContractController::class, 'applicationsExportCsv'])->name('admins.prospective-tenants.applications.export.csv');
    Route::get('/tenants/prospective/{stall}/applications/print', [ContractController::class, 'applicationsPrint'])->name('admins.prospective-tenants.applications.print');
    
    // Tenant Feedback (Admin view, detail & archive)
    Route::get('/tenant-feedback', [FeedbackController::class, 'adminIndex'])->name('admins.feedback.index');
    Route::get('/tenant-feedback/{feedback}', [FeedbackController::class, 'show'])->name('admins.feedback.show');
    Route::post('/tenant-feedback/{feedback}/archive', [FeedbackController::class, 'archive'])->name('admins.feedback.archive');

    // Bills Management (Admin)
    Route::get('/admins/bills', [BillController::class, 'adminIndex'])->name('admins.bills.index');
    Route::get('/admins/bills/data', [BillController::class, 'adminData'])->name('admins.bills.data');
    Route::get('/admins/bills/{bill}/update-status', [BillController::class, 'showUpdateStatusForm'])->name('admins.bills.show-update-status');
    Route::put('/admins/bills/{bill}/status', [BillController::class, 'updateStatus'])->name('admins.bills.update-status');
    Route::post('/admins/bills/{bill}/archive', [BillController::class, 'archive'])->name('admins.bills.archive');
    Route::delete('/admins/bills/{bill}', [BillController::class, 'destroy'])->name('admins.bills.destroy');
    Route::post('/admins/bills/generate', [BillController::class, 'generateMonthlyBills'])->name('admins.bills.generate');
    Route::get('/admins/bills/export/csv', [BillController::class, 'adminExportCsv'])->name('admins.bills.export.csv');
    Route::get('/admins/bills/print', [BillController::class, 'adminPrint'])->name('admins.bills.print');

    // Leases Management
    Route::get('/admins/leases', [ContractController::class, 'leasesIndex'])->name('admins.leases.index');
    Route::get('/admins/leases/data', [ContractController::class, 'leasesData'])->name('admins.leases.data');
    Route::get('/admins/leases/{contract}', [ContractController::class, 'show'])->name('admins.leases.show');
    Route::get('/admins/leases/{contract}/renew', [ContractController::class, 'showRenewForm'])->name('admins.leases.show-renew');
    Route::post('/admins/leases/{contract}/renew', [ContractController::class, 'renew'])->name('admins.leases.renew');
    Route::get('/admins/leases/{contract}/terminate', [ContractController::class, 'showTerminateForm'])->name('admins.leases.show-terminate');
    Route::post('/admins/leases/{contract}/terminate', [ContractController::class, 'terminate'])->name('admins.leases.terminate');
    Route::post('/admins/leases/{contract}/archive', [ContractController::class, 'archive'])->name('admins.leases.archive');
    Route::get('/admins/leases/export/csv', [ContractController::class, 'adminLeasesExportCsv'])->name('admins.leases.export.csv');
    Route::get('/admins/leases/print', [ContractController::class, 'adminLeasesPrint'])->name('admins.leases.print');

    // Lease Manager Profile & Settings
    Route::get('/admins/profile', [AdminAccountController::class, 'profile'])->name('admins.profile');
    Route::get('/admins/settings', [AdminAccountController::class, 'settings'])->name('admins.settings');
    Route::put('/admins/profile', [AdminAccountController::class, 'updateProfile'])->name('admins.profile.update');
    Route::post('/admins/settings', [AdminAccountController::class, 'updateSettings'])->name('admins.settings.update');
});

// =======================
// Shared Routes (Both Admin and Tenant)
// =======================
Route::middleware(['auth'])->group(function() {
    // Marketplace Stalls API (shared by both admin and tenant)
    // The controller handles role checking internally
    Route::get('/marketplace/stalls', [MarketplaceMapController::class, 'getStalls'])->name('marketplace.stalls');
});

// =======================
// Tenant Side
// =======================
Route::middleware(['auth'])->group(function() {

    // Default dashboard redirect (redirects based on role)
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role === 'Tenant') {
            return redirect()->route('tenants.dashboard');
        } elseif ($user->role === 'Lease Manager') {
            return redirect()->route('admins.dashboard');
        }
        abort(403, 'Unauthorized');
    })->name('dashboard');

    // Tenant Dashboard
    Route::middleware(['role:Tenant'])->group(function() {
        // Debug-only: verify Blade view rendering for tenant routes
        if (config('app.debug')) {
            Route::get('/tenants/__render-check', function () {
                $user = Auth::user();
                $html = view('tenants.__render_check', [
                    'userId' => $user?->id,
                    'role' => $user?->role,
                    'email' => $user?->email,
                    'time' => now()->toDateTimeString(),
                ])->render();

                return response($html, 200)
                    ->header('X-Rendered-View', 'tenants.__render_check')
                    ->header('X-Auth-Id', (string) ($user?->id ?? ''))
                    ->header('X-Auth-Role', (string) ($user?->role ?? ''));
            })->name('tenants.render_check');
        }

        Route::get('/tenants/dashboard', [DashboardController::class, 'tenantIndex'])->name('tenants.dashboard');
        Route::get('/tenants/dashboard/stats', [DashboardController::class, 'tenantStats'])->name('tenants.dashboard.stats');

        // Tenant - My Stalls (tenant-facing list)
        Route::get('/tenants/stalls', [TenantStallController::class, 'index'])->name('tenants.stalls.index');
        Route::get('/tenants/stalls/data', [TenantStallController::class, 'data'])->name('tenants.stalls.data');
        Route::get('/tenants/stalls/assigned', [TenantStallController::class, 'assignedStalls'])->name('tenants.stalls.assigned');
        Route::get('/tenants/stalls/{stall}', [TenantStallController::class, 'show'])->name('tenants.stalls.show');

        // Tenant Marketplace Map
        Route::get('/marketplace', [MarketplaceMapController::class, 'index'])->name('tenants.marketplace.index');
        Route::get('/hub', [MarketplaceMapController::class, 'hub'])->name('tenants.marketplace.hub');
        Route::get('/bazaar', [MarketplaceMapController::class, 'bazaar'])->name('tenants.marketplace.bazaar');

        // Tenant Application Submission
        Route::get('/applications/create', [TenantApplicationController::class, 'create'])->name('tenants.applications.create');
        Route::post('/applications', [TenantApplicationController::class, 'store'])->name('tenants.applications.store');
        Route::get('/applications/{id}', [TenantApplicationController::class, 'show'])->name('tenants.applications.show');
        Route::post('/applications/{id}/withdraw', [TenantApplicationController::class, 'withdraw'])->name('tenants.applications.withdraw');

        // Tenant Feedback
        Route::get('/tenants/feedback', [FeedbackController::class, 'tenantForm'])->name('tenants.feedback.index');
        Route::post('/tenants/feedback', [FeedbackController::class, 'tenantStore'])->name('tenants.feedback.store');

        // Contact Us
        Route::get('/contact-us', function () {
            return view('tenants.contact_us.index');
        })->name('tenants.contact.index');

        // Tenant Bills
        Route::get('/bills', [BillController::class, 'tenantIndex'])->name('tenants.bills.index');
        Route::get('/bills/data', [BillController::class, 'tenantData'])->name('tenants.bills.data');
        Route::get('/bills/{bill}/upload', [BillController::class, 'showUploadForm'])->name('tenants.bills.upload');
        Route::post('/bills/{bill}/upload-proof', [BillController::class, 'uploadPaymentProof'])->name('tenants.bills.upload-proof');
        Route::get('/bills/export/csv', [BillController::class, 'tenantExportCsv'])->name('tenants.bills.export.csv');
        Route::get('/bills/print', [BillController::class, 'tenantPrint'])->name('tenants.bills.print');

        // Tenant Leases
        Route::get('/leases', [ContractController::class, 'tenantLeasesIndex'])->name('tenants.leases.index');
        Route::get('/leases/data', [ContractController::class, 'tenantLeasesData'])->name('tenants.leases.data');
        Route::get('/leases/export/csv', [ContractController::class, 'tenantLeasesExportCsv'])->name('tenants.leases.export.csv');
        Route::get('/leases/print', [ContractController::class, 'tenantLeasesPrint'])->name('tenants.leases.print');

        // Tenant Profile & Settings
        Route::get('/tenants/profile', [TenantAccountController::class, 'profile'])->name('tenants.profile');
        Route::get('/tenants/settings', [TenantAccountController::class, 'settings'])->name('tenants.settings');
        Route::put('/tenants/profile', [TenantAccountController::class, 'updateProfile'])->name('tenants.profile.update');
        Route::post('/tenants/settings', [TenantAccountController::class, 'updateSettings'])->name('tenants.settings.update');
    });

    // Add more tenant routes here
});

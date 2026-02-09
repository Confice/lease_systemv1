<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Contract;
use App\Services\ActivityLogService;
use App\Models\Bill;
use App\Models\Stall;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function adminIndex()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }
        $stats = $this->getAdminStats();
        $recentTenantActivity = $this->getRecentTenantActivityWithLinks();

        return view('admins.dashboard', [
            'dashboardStats' => [
                'manageUsers' => $stats['manageUsers'],
                'occupiedStalls' => $stats['occupiedStalls'],
                'totalStalls' => $stats['totalStalls'],
                'expiringContracts' => $stats['expiringContracts'],
                'expectedRentCollected' => number_format($stats['expectedRentCollected'], 2),
            ],
            'recentTenantActivity' => $recentTenantActivity,
        ]);
    }

    /**
     * Get recent tenant activity (applications, bill proof uploads, feedback) with formatted message and goto URL.
     * Excludes login/logout.
     */
    private function getRecentTenantActivityWithLinks(): array
    {
        $logs = ActivityLog::with('user')
            ->whereHas('user', fn ($q) => $q->where('role', 'Tenant'))
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        $result = [];
        foreach ($logs as $log) {
            $formatted = ActivityLogService::formatForDisplay($log, 'Lease Manager');
            if ($formatted !== null) {
                $result[] = [
                    'message' => $formatted['message'],
                    'url' => $formatted['url'],
                    'created_at' => $formatted['created_at'],
                ];
                if (count($result) >= 15) {
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Get admin dashboard statistics
     */
    public function adminStats()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $stats = $this->getAdminStats();

        return response()->json([
            'manageUsers' => $stats['manageUsers'],
            'occupiedStalls' => $stats['occupiedStalls'],
            'totalStalls' => $stats['totalStalls'],
            'expiringContracts' => $stats['expiringContracts'],
            'expectedRentCollected' => (float) $stats['expectedRentCollected'],
            'pendingBills' => $stats['pendingBills'],
            'recentFeedback' => $stats['recentFeedback'],
            'activeContracts' => $stats['activeContracts'],
        ]);
    }

    private function getAdminStats(): array
    {
        // Manage users (all non-deleted users)
        $manageUsers = User::whereNull('deleted_at')->count();

        // Occupied stalls
        $occupiedStalls = Stall::where('stallStatus', 'Occupied')
            ->whereNull('deleted_at')
            ->count();
        $totalStalls = Stall::whereNull('deleted_at')->count();

        // Expiring contracts (within 30 days)
        $expiringContracts = Contract::where('contractStatus', 'Active')
            ->whereNotNull('endDate')
            ->where('endDate', '>=', now())
            ->where('endDate', '<=', now()->addDays(30))
            ->whereNull('deleted_at')
            ->count();

        // Expected rent collected (bills due this month)
        $expectedRentCollected = Bill::whereMonth('dueDate', now()->month)
            ->whereYear('dueDate', now()->year)
            ->whereNull('deleted_at')
            ->sum('amount');

        // Pending bills
        $pendingBills = Bill::whereIn('status', ['Pending', 'Due'])
            ->whereNull('deleted_at')
            ->count();

        // Recent feedback (last 7 days)
        $recentFeedbackQuery = Feedback::where('created_at', '>=', now()->subDays(7))
            ->whereNull('archived_at');
        if (Schema::hasColumn('feedbacks', 'deleted_at')) {
            $recentFeedbackQuery->whereNull('deleted_at');
        }
        $recentFeedback = $recentFeedbackQuery->count();

        // Total active contracts
        $activeContracts = Contract::where('contractStatus', 'Active')
            ->whereNull('deleted_at')
            ->count();

        return [
            'manageUsers' => $manageUsers,
            'occupiedStalls' => $occupiedStalls,
            'totalStalls' => $totalStalls,
            'expiringContracts' => $expiringContracts,
            'expectedRentCollected' => $expectedRentCollected,
            'pendingBills' => $pendingBills,
            'recentFeedback' => $recentFeedback,
            'activeContracts' => $activeContracts,
        ];
    }

    /**
     * Display tenant dashboard
     */
    public function tenantIndex()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Tenant') {
            abort(403, 'Unauthorized');
        }
        try {
            return view('tenants.dashboard');
        } catch (\Throwable $e) {
            \Log::error('Tenant dashboard view error: ' . $e->getMessage(), ['exception' => $e]);
            if (config('app.debug')) {
                return response(
                    '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Tenant Dashboard Error</title></head><body style="font-family:sans-serif;padding:2rem;">'
                    . '<h1>Tenant Dashboard Error</h1><p><strong>' . e($e->getMessage()) . '</strong></p>'
                    . '<pre style="background:#f5f5f5;padding:1rem;overflow:auto;">' . e($e->getTraceAsString()) . '</pre></body></html>',
                    500
                );
            }
            throw $e;
        }
    }

    /**
     * Get tenant dashboard statistics
     */
    public function tenantStats()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Tenant') {
            abort(403, 'Unauthorized');
        }

        // Active leases
        $activeLeases = Contract::where('userID', $user->id)
            ->where('contractStatus', 'Active')
            ->whereNull('deleted_at')
            ->count();

        // Upcoming bills (next 30 days)
        $upcomingBills = Bill::whereHas('contract', function($query) use ($user) {
                $query->where('userID', $user->id)
                      ->where('contractStatus', 'Active')
                      ->whereNull('deleted_at');
            })
            ->whereIn('status', ['Pending', 'Due'])
            ->where('dueDate', '>=', now())
            ->where('dueDate', '<=', now()->addDays(30))
            ->whereNull('deleted_at')
            ->count();

        // Overdue bills
        $overdueBills = Bill::whereHas('contract', function($query) use ($user) {
                $query->where('userID', $user->id)
                      ->where('contractStatus', 'Active')
                      ->whereNull('deleted_at');
            })
            ->whereIn('status', ['Pending', 'Due'])
            ->where('dueDate', '<', now())
            ->whereNull('deleted_at')
            ->count();

        // Total pending amount
        $pendingAmount = Bill::whereHas('contract', function($query) use ($user) {
                $query->where('userID', $user->id)
                      ->where('contractStatus', 'Active')
                      ->whereNull('deleted_at');
            })
            ->whereIn('status', ['Pending', 'Due'])
            ->whereNull('deleted_at')
            ->sum('amount');

        // Contracts expiring soon (within 30 days)
        $expiringContracts = Contract::where('userID', $user->id)
            ->where('contractStatus', 'Active')
            ->whereNotNull('endDate')
            ->where('endDate', '>=', now())
            ->where('endDate', '<=', now()->addDays(30))
            ->whereNull('deleted_at')
            ->count();

        // Recent payments (last 30 days)
        $recentPayments = Bill::whereHas('contract', function($query) use ($user) {
                $query->where('userID', $user->id)
                      ->whereNull('deleted_at');
            })
            ->where('status', 'Paid')
            ->where('datePaid', '>=', now()->subDays(30))
            ->whereNull('deleted_at')
            ->count();

        return response()->json([
            'activeLeases' => $activeLeases,
            'upcomingBills' => $upcomingBills,
            'overdueBills' => $overdueBills,
            'pendingAmount' => (float) $pendingAmount,
            'expiringContracts' => $expiringContracts,
            'recentPayments' => $recentPayments,
        ]);
    }
}

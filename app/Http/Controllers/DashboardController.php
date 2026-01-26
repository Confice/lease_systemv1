<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Bill;
use App\Models\Stall;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        return view('admins.dashboard');
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

        // Active tenants (users with active contracts)
        $activeTenants = User::where('role', 'Tenant')
            ->where('userStatus', 'Active')
            ->whereHas('contracts', function($query) {
                $query->where('contractStatus', 'Active')
                      ->whereNull('deleted_at');
            })
            ->whereNull('deleted_at')
            ->count();

        // Vacant stalls
        $vacantStalls = Stall::where('stallStatus', 'Vacant')
            ->whereNull('deleted_at')
            ->count();

        // Expiring contracts (within 30 days)
        $expiringContracts = Contract::where('contractStatus', 'Active')
            ->whereNotNull('endDate')
            ->where('endDate', '>=', now())
            ->where('endDate', '<=', now()->addDays(30))
            ->whereNull('deleted_at')
            ->count();

        // Rent collected this month (Paid bills)
        $rentCollected = Bill::where('status', 'Paid')
            ->whereMonth('datePaid', now()->month)
            ->whereYear('datePaid', now()->year)
            ->whereNull('deleted_at')
            ->sum('amount');

        // Pending bills
        $pendingBills = Bill::whereIn('status', ['Pending', 'Due'])
            ->whereNull('deleted_at')
            ->count();

        // Recent feedback (last 7 days)
        $recentFeedback = Feedback::where('created_at', '>=', now()->subDays(7))
            ->whereNull('archived_at')
            ->whereNull('deleted_at')
            ->count();

        // Total active contracts
        $activeContracts = Contract::where('contractStatus', 'Active')
            ->whereNull('deleted_at')
            ->count();

        return response()->json([
            'activeTenants' => $activeTenants,
            'vacantStalls' => $vacantStalls,
            'expiringContracts' => $expiringContracts,
            'rentCollected' => number_format($rentCollected, 2),
            'pendingBills' => $pendingBills,
            'recentFeedback' => $recentFeedback,
            'activeContracts' => $activeContracts,
        ]);
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
        return view('tenants.dashboard');
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
            'pendingAmount' => number_format($pendingAmount, 2),
            'expiringContracts' => $expiringContracts,
            'recentPayments' => $recentPayments,
        ]);
    }
}

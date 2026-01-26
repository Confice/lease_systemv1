<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\Stall;
use App\Models\User;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }
        return view('admins.analytics.index');
    }

    /**
     * Get revenue trends data (monthly for last 12 months)
     */
    public function revenueTrends(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $period = $request->input('period', '12'); // months
        $startDate = now()->subMonths($period)->startOfMonth();
        
        $revenue = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->where('datePaid', '>=', $startDate)
            ->whereNull('deleted_at')
            ->selectRaw('DATE_FORMAT(datePaid, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill in missing months with 0
        $months = [];
        $revenueMap = $revenue->pluck('total', 'month')->toArray();
        
        for ($i = $period - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months[] = [
                'month' => now()->subMonths($i)->format('M Y'),
                'revenue' => isset($revenueMap[$month]) ? (float)$revenueMap[$month] : 0
            ];
        }

        return response()->json([
            'labels' => array_column($months, 'month'),
            'data' => array_column($months, 'revenue')
        ]);
    }

    /**
     * Get occupancy statistics
     */
    public function occupancyStats()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $totalStalls = Stall::whereNull('deleted_at')->count();
        $occupiedStalls = Stall::where('stallStatus', 'Occupied')
            ->whereNull('deleted_at')
            ->count();
        $vacantStalls = Stall::where('stallStatus', 'Vacant')
            ->whereNull('deleted_at')
            ->count();

        $occupancyRate = $totalStalls > 0 ? ($occupiedStalls / $totalStalls) * 100 : 0;

        return response()->json([
            'total' => $totalStalls,
            'occupied' => $occupiedStalls,
            'vacant' => $vacantStalls,
            'occupancyRate' => round($occupancyRate, 2)
        ]);
    }

    /**
     * Get payment status distribution
     */
    public function paymentStatus()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $statuses = Bill::whereNull('deleted_at')
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'labels' => $statuses->pluck('status')->toArray(),
            'counts' => $statuses->pluck('count')->toArray(),
            'amounts' => $statuses->pluck('total')->map(function($amount) {
                return (float)$amount;
            })->toArray()
        ]);
    }

    /**
     * Get lease expiration timeline (next 6 months)
     */
    public function leaseExpirationTimeline()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $monthStart = now()->addMonths($i)->startOfMonth();
            $monthEnd = now()->addMonths($i)->endOfMonth();
            
            $count = Contract::where('contractStatus', 'Active')
                ->whereNotNull('endDate')
                ->whereBetween('endDate', [$monthStart, $monthEnd])
                ->whereNull('deleted_at')
                ->count();

            $months[] = [
                'month' => $monthStart->format('M Y'),
                'count' => $count
            ];
        }

        return response()->json([
            'labels' => array_column($months, 'month'),
            'data' => array_column($months, 'count')
        ]);
    }

    /**
     * Get marketplace performance (revenue by marketplace)
     */
    public function marketplacePerformance()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $performance = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->whereNull('bills.deleted_at')
            ->join('contracts', 'bills.contractID', '=', 'contracts.contractID')
            ->join('stalls', 'contracts.stallID', '=', 'stalls.stallID')
            ->join('marketplaces', 'stalls.marketplaceID', '=', 'marketplaces.marketplaceID')
            ->select('marketplaces.marketplace', DB::raw('SUM(bills.amount) as total_revenue'), DB::raw('COUNT(DISTINCT contracts.contractID) as contract_count'))
            ->groupBy('marketplaces.marketplaceID', 'marketplaces.marketplace')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return response()->json([
            'labels' => $performance->pluck('marketplace')->toArray(),
            'revenue' => $performance->pluck('total_revenue')->map(function($amount) {
                return (float)$amount;
            })->toArray(),
            'contracts' => $performance->pluck('contract_count')->toArray()
        ]);
    }

    /**
     * Get tenant retention (renewals vs terminations)
     */
    public function tenantRetention(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $period = $request->input('period', '12'); // months
        $startDate = now()->subMonths($period)->startOfMonth();

        $renewals = Contract::whereNotNull('renewedFrom')
            ->where('created_at', '>=', $startDate)
            ->whereNull('deleted_at')
            ->count();

        $terminations = Contract::where('contractStatus', 'Terminated')
            ->where('updated_at', '>=', $startDate)
            ->whereNull('deleted_at')
            ->count();

        $newContracts = Contract::whereNull('renewedFrom')
            ->where('created_at', '>=', $startDate)
            ->whereNull('deleted_at')
            ->count();

        return response()->json([
            'renewals' => $renewals,
            'terminations' => $terminations,
            'newContracts' => $newContracts,
            'retentionRate' => ($renewals + $newContracts) > 0 
                ? round(($renewals / ($renewals + $newContracts + $terminations)) * 100, 2) 
                : 0
        ]);
    }

    /**
     * Get top performing stalls
     */
    public function topPerformingStalls()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $stalls = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->whereNull('bills.deleted_at')
            ->join('stalls', 'bills.stallID', '=', 'stalls.stallID')
            ->join('marketplaces', 'stalls.marketplaceID', '=', 'marketplaces.marketplaceID')
            ->select(
                'stalls.stallNo',
                'marketplaces.marketplace',
                DB::raw('SUM(bills.amount) as total_revenue'),
                DB::raw('COUNT(bills.billID) as bill_count')
            )
            ->groupBy('stalls.stallID', 'stalls.stallNo', 'marketplaces.marketplace')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'stalls' => $stalls->map(function($stall) {
                return [
                    'stallNo' => $stall->stallNo,
                    'marketplace' => $stall->marketplace,
                    'revenue' => (float)$stall->total_revenue,
                    'billCount' => $stall->bill_count
                ];
            })
        ]);
    }

    /**
     * Get summary statistics
     */
    public function summaryStats()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        // Total revenue (all time)
        $totalRevenue = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->whereNull('deleted_at')
            ->sum('amount');

        // Revenue this month
        $monthlyRevenue = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->whereMonth('datePaid', now()->month)
            ->whereYear('datePaid', now()->year)
            ->whereNull('deleted_at')
            ->sum('amount');

        // Revenue this year
        $yearlyRevenue = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->whereYear('datePaid', now()->year)
            ->whereNull('deleted_at')
            ->sum('amount');

        // Average monthly revenue (last 12 months)
        $avgMonthlyRevenue = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->where('datePaid', '>=', now()->subMonths(12))
            ->whereNull('deleted_at')
            ->selectRaw('AVG(monthly_total) as avg')
            ->fromSub(function($query) {
                $query->selectRaw('DATE_FORMAT(datePaid, "%Y-%m") as month, SUM(amount) as monthly_total')
                    ->from('bills')
                    ->where('status', 'Paid')
                    ->whereNotNull('datePaid')
                    ->where('datePaid', '>=', now()->subMonths(12))
                    ->whereNull('deleted_at')
                    ->groupBy('month');
            }, 'monthly_revenue')
            ->value('avg') ?? 0;

        // Total active contracts
        $activeContracts = Contract::where('contractStatus', 'Active')
            ->whereNull('deleted_at')
            ->count();

        // Total tenants
        $totalTenants = User::where('role', 'Tenant')
            ->whereNull('deleted_at')
            ->count();

        return response()->json([
            'totalRevenue' => number_format($totalRevenue, 2),
            'monthlyRevenue' => number_format($monthlyRevenue, 2),
            'yearlyRevenue' => number_format($yearlyRevenue, 2),
            'avgMonthlyRevenue' => number_format($avgMonthlyRevenue, 2),
            'activeContracts' => $activeContracts,
            'totalTenants' => $totalTenants
        ]);
    }

    /**
     * Export analytics data to CSV
     */
    public function exportCsv()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $filename = 'analytics-report-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Summary Statistics
            fputcsv($file, ['ANALYTICS REPORT - ' . now()->format('F Y')]);
            fputcsv($file, []);
            fputcsv($file, ['SUMMARY STATISTICS']);
            fputcsv($file, ['Metric', 'Value']);
            
            $summary = $this->getSummaryData();
            fputcsv($file, ['Total Revenue', '₱' . $summary['totalRevenue']]);
            fputcsv($file, ['Monthly Revenue', '₱' . $summary['monthlyRevenue']]);
            fputcsv($file, ['Yearly Revenue', '₱' . $summary['yearlyRevenue']]);
            fputcsv($file, ['Average Monthly Revenue', '₱' . $summary['avgMonthlyRevenue']]);
            fputcsv($file, ['Active Contracts', $summary['activeContracts']]);
            fputcsv($file, ['Total Tenants', $summary['totalTenants']]);
            fputcsv($file, []);
            
            // Revenue Trends (Last 12 Months)
            fputcsv($file, ['REVENUE TRENDS (Last 12 Months)']);
            fputcsv($file, ['Month', 'Revenue']);
            $revenue = Bill::where('status', 'Paid')
                ->whereNotNull('datePaid')
                ->where('datePaid', '>=', now()->subMonths(12)->startOfMonth())
                ->whereNull('deleted_at')
                ->selectRaw('DATE_FORMAT(datePaid, "%Y-%m") as month, SUM(amount) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            
            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i)->format('Y-m');
                $monthName = now()->subMonths($i)->format('M Y');
                $revenueData = $revenue->firstWhere('month', $month);
                fputcsv($file, [$monthName, '₱' . number_format($revenueData->total ?? 0, 2)]);
            }
            fputcsv($file, []);
            
            // Top Performing Stalls
            fputcsv($file, ['TOP 10 PERFORMING STALLS']);
            fputcsv($file, ['Rank', 'Stall Number', 'Marketplace', 'Total Revenue', 'Bills Paid']);
            $stalls = Bill::where('status', 'Paid')
                ->whereNotNull('datePaid')
                ->whereNull('bills.deleted_at')
                ->join('stalls', 'bills.stallID', '=', 'stalls.stallID')
                ->join('marketplaces', 'stalls.marketplaceID', '=', 'marketplaces.marketplaceID')
                ->select(
                    'stalls.stallNo',
                    'marketplaces.marketplace',
                    DB::raw('SUM(bills.amount) as total_revenue'),
                    DB::raw('COUNT(bills.billID) as bill_count')
                )
                ->groupBy('stalls.stallID', 'stalls.stallNo', 'marketplaces.marketplace')
                ->orderBy('total_revenue', 'desc')
                ->limit(10)
                ->get();
            
            $rank = 1;
            foreach ($stalls as $stall) {
                fputcsv($file, [
                    $rank++,
                    $stall->stallNo,
                    $stall->marketplace,
                    '₱' . number_format($stall->total_revenue, 2),
                    $stall->bill_count
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper method to get summary data
     */
    private function getSummaryData()
    {
        $totalRevenue = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->whereNull('deleted_at')
            ->sum('amount');

        $monthlyRevenue = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->whereMonth('datePaid', now()->month)
            ->whereYear('datePaid', now()->year)
            ->whereNull('deleted_at')
            ->sum('amount');

        $yearlyRevenue = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->whereYear('datePaid', now()->year)
            ->whereNull('deleted_at')
            ->sum('amount');

        $avgMonthlyRevenue = Bill::where('status', 'Paid')
            ->whereNotNull('datePaid')
            ->where('datePaid', '>=', now()->subMonths(12))
            ->whereNull('deleted_at')
            ->selectRaw('AVG(monthly_total) as avg')
            ->fromSub(function($query) {
                $query->selectRaw('DATE_FORMAT(datePaid, "%Y-%m") as month, SUM(amount) as monthly_total')
                    ->from('bills')
                    ->where('status', 'Paid')
                    ->whereNotNull('datePaid')
                    ->where('datePaid', '>=', now()->subMonths(12))
                    ->whereNull('deleted_at')
                    ->groupBy('month');
            }, 'monthly_revenue')
            ->value('avg') ?? 0;

        $activeContracts = Contract::where('contractStatus', 'Active')
            ->whereNull('deleted_at')
            ->count();

        $totalTenants = User::where('role', 'Tenant')
            ->whereNull('deleted_at')
            ->count();

        return [
            'totalRevenue' => number_format($totalRevenue, 2),
            'monthlyRevenue' => number_format($monthlyRevenue, 2),
            'yearlyRevenue' => number_format($yearlyRevenue, 2),
            'avgMonthlyRevenue' => number_format($avgMonthlyRevenue, 2),
            'activeContracts' => $activeContracts,
            'totalTenants' => $totalTenants
        ];
    }
}


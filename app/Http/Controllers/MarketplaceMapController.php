<?php

namespace App\Http\Controllers;

use App\Models\Stall;
use App\Models\Marketplace;
use App\Models\Contract;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketplaceMapController extends Controller
{
    /**
     * Display marketplace map with tabs
     */
    public function index()
    {
        $isAdmin = Auth::user()->role === 'Lease Manager';
        
        return view($isAdmin ? 'admins.marketplace.index' : 'tenants.marketplace.index');
    }

    /**
     * Display The Hub marketplace map
     */
    public function hub()
    {
        $marketplace = Marketplace::where('marketplace', 'like', '%Hub%')
            ->orWhere('marketplace', 'like', '%HUB%')
            ->first();
        
        if (!$marketplace) {
            abort(404, 'The Hub marketplace not found.');
        }

        $isAdmin = Auth::user()->role === 'Lease Manager';
        
        return view($isAdmin ? 'admins.marketplace.hub' : 'tenants.marketplace.hub', [
            'marketplace' => $marketplace
        ]);
    }

    /**
     * Display Bazaar marketplace map
     */
    public function bazaar()
    {
        $marketplace = Marketplace::where('marketplace', 'like', '%Bazaar%')
            ->orWhere('marketplace', 'like', '%BAZAAR%')
            ->orWhere('marketplace', 'like', '%One-Stop%')
            ->first();
        
        if (!$marketplace) {
            abort(404, 'Bazaar marketplace not found.');
        }

        $isAdmin = Auth::user()->role === 'Lease Manager';
        
        return view($isAdmin ? 'admins.marketplace.bazaar' : 'tenants.marketplace.bazaar', [
            'marketplace' => $marketplace
        ]);
    }

    /**
     * Get stalls data for marketplace map (AJAX)
     */
    public function getStalls(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['data' => [], 'error' => 'Unauthorized'], 401);
            }
            
            $marketplaceName = $request->input('marketplace');
            $isAdmin = $user->role === 'Lease Manager';
            
            // Find marketplace
            $marketplace = Marketplace::where('marketplace', 'like', "%{$marketplaceName}%")
                ->first();
            
            if (!$marketplace) {
                \Log::warning("Marketplace not found: {$marketplaceName}");
                return response()->json(['data' => []]);
            }

        // Get all stalls for this marketplace (both vacant and occupied)
        $stalls = Stall::where('marketplaceID', $marketplace->marketplaceID)
            ->whereNull('deleted_at')
            ->with(['marketplace', 'store.user', 'contracts' => function($query) {
                $query->where('contractStatus', 'Active');
            }])
            ->get();

        // Get user's applications for these stalls (if tenant)
        $userApplications = [];
        if (!$isAdmin && Auth::check()) {
            $userApplications = Application::where('userID', Auth::id())
                ->whereIn('stallID', $stalls->pluck('stallID'))
                ->whereNull('deleted_at')
                ->get()
                ->keyBy('stallID');
        }

        $data = $stalls->map(function ($stall) use ($isAdmin, $userApplications) {
            $contract = $stall->contracts->first();
            $isOccupied = $stall->stallStatus === 'Occupied' && $contract;
            $isVacant = $stall->stallStatus === 'Vacant';
            
            // Check if application is open (deadline not passed)
            $applicationOpen = false;
            if ($isVacant && $stall->applicationDeadline) {
                $applicationOpen = now()->lte($stall->applicationDeadline);
            }
            
            // Check if user has an active application for this stall (for tenants)
            $hasActiveApplication = false;
            $canReapply = false;
            if (!$isAdmin && isset($userApplications[$stall->stallID])) {
                $userApp = $userApplications[$stall->stallID];
                // Allow reapplication if status is "Proposal Rejected" or "Withdrawn"
                $canReapply = in_array($userApp->appStatus, ['Proposal Rejected', 'Withdrawn']);
                // Active application means status is not "Proposal Rejected" or "Withdrawn"
                $hasActiveApplication = !$canReapply;
            }

            // Extract numeric part from stallNo for positioning
            // For Bazaar: "L1-1" -> 1, "L2-6" -> 6 (extract number after dash)
            // For Hub: "Stall01" -> 1 (extract all digits)
            $stallNumber = null;
            $stallNoUpper = strtoupper(trim($stall->stallNo));
            
            // Check if it's Bazaar format (L{level}-{number})
            if (preg_match('/^L\d+-(\d+)$/', $stallNoUpper, $matches)) {
                $stallNumber = (int)$matches[1]; // Extract number after dash
            } else {
                // For other formats, extract all digits (e.g., "Stall01" -> 1)
                $stallNumber = preg_replace('/[^0-9]/', '', $stall->stallNo);
                $stallNumber = $stallNumber ? (int)$stallNumber : null;
            }
            
            // Fallback to stallID if no number extracted
            if (!$stallNumber) {
                $stallNumber = $stall->stallID;
            }

            $result = [
                'stallID' => $stall->stallID,
                'stallNo' => strtoupper($stall->stallNo),
                'stallNumber' => $stallNumber, // For positioning
                'formattedStallId' => $stall->formatted_stall_id,
                'size' => $stall->size ?? '-',
                'status' => $stall->stallStatus,
                'isOccupied' => $isOccupied,
                'isVacant' => $isVacant,
                'applicationOpen' => $applicationOpen,
                'applicationDeadline' => $stall->applicationDeadline ? $stall->applicationDeadline->format('Y-m-d') : null,
                'hasActiveApplication' => $hasActiveApplication, // For tenants: true if they have an active application
                'canReapply' => $canReapply, // For tenants: true if status is "Proposal Rejected"
            ];

            if ($isOccupied && $contract) {
                $user = $contract->user;
                $store = $stall->store;
                
                $result['rentBy'] = trim(($user->firstName ?? '') . ' ' . ($user->middleName ?? '') . ' ' . ($user->lastName ?? ''));
                $result['storeName'] = $store ? $store->storeName : '-';
                $result['businessType'] = $store ? $store->businessType : '-';
            }

            return $result;
        });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            \Log::error("Error in getStalls: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }
}


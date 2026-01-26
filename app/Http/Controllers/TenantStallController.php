<?php

namespace App\Http\Controllers;

use App\Models\Stall;
use App\Models\Contract;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantStallController extends Controller
{
    /**
     * Display a listing of the tenant's stalls
     * Only accessible to users with 'Tenant' role
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ensure only tenants can access this
        if (!$user || $user->role !== 'Tenant') {
            abort(403, 'Only tenants can access this page.');
        }
        
        return view('tenants.stalls.index');
    }

    /**
     * Get data for tenant's applications (AJAX)
     * Returns stalls where tenant has submitted applications
     */
    public function data()
    {
        $user = Auth::user();
        
        // Ensure we have an authenticated tenant user
        if (!$user || $user->role !== 'Tenant') {
            return response()->json(['data' => []], 403);
        }
        
        // Get all applications for this tenant (including withdrawn ones)
        $applications = Application::where('userID', $user->id)
            ->whereNull('deleted_at')
            ->with(['stall' => function($query) {
                $query->whereNull('deleted_at');
            }, 'stall.marketplace'])
            ->whereHas('stall', function($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy('dateApplied', 'desc')
            ->get();

        // Map applications to display data
        $data = $applications->map(function ($application) {
            $stall = $application->stall;
            
            if (!$stall) {
                return null;
            }
            
            return [
                'applicationID' => $application->applicationID,
                'stallID' => $stall->stallID,
                'stallNo' => strtoupper($stall->stallNo),
                'formattedStallId' => $stall->formatted_stall_id,
                'marketplace' => $stall->marketplace ? $stall->marketplace->marketplace : '-',
                'marketplaceAddress' => $stall->marketplace ? $stall->marketplace->marketplaceAddress : '-',
                'size' => $stall->size ?? '-',
                'rentalFee' => $stall->rentalFee ? number_format($stall->rentalFee, 2) : '-',
                'appStatus' => $application->appStatus ?? 'Proposal Received',
                'dateApplied' => $application->dateApplied ? $application->dateApplied->format('Y-m-d') : now()->format('Y-m-d'),
                'applicationDeadline' => $stall->applicationDeadline ? $stall->applicationDeadline->format('Y-m-d') : null,
            ];
        })->filter(); // Remove null entries

        return response()->json(['data' => $data->values()]);
    }

    /**
     * Get data for tenant's assigned stalls (AJAX)
     * Returns stalls where tenant has active contracts
     */
    public function assignedStalls()
    {
        $user = Auth::user();
        
        // Ensure we have an authenticated tenant user
        if (!$user || $user->role !== 'Tenant') {
            return response()->json(['data' => []], 403);
        }
        
        // Get all active contracts for this tenant
        $contracts = Contract::where('userID', $user->id)
            ->where('contractStatus', 'Active')
            ->whereNull('deleted_at')
            ->with(['stall' => function($query) {
                $query->whereNull('deleted_at');
            }, 'stall.marketplace'])
            ->whereHas('stall', function($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy('startDate', 'desc')
            ->get();

        // Map contracts to display data
        $data = $contracts->map(function ($contract) {
            $stall = $contract->stall;
            
            if (!$stall) {
                return null;
            }
            
            return [
                'contractID' => $contract->contractID,
                'stallID' => $stall->stallID,
                'stallNo' => strtoupper($stall->stallNo),
                'formattedStallId' => $stall->formatted_stall_id,
                'marketplace' => $stall->marketplace ? $stall->marketplace->marketplace : '-',
                'marketplaceAddress' => $stall->marketplace ? $stall->marketplace->marketplaceAddress : '-',
                'size' => $stall->size ?? '-',
                'rentalFee' => $stall->rentalFee ? number_format($stall->rentalFee, 2) : '-',
                'contractStatus' => $contract->contractStatus ?? 'Active',
                'startDate' => $contract->startDate ? $contract->startDate->format('Y-m-d') : '-',
                'endDate' => $contract->endDate ? $contract->endDate->format('Y-m-d') : null,
            ];
        })->filter(); // Remove null entries

        return response()->json(['data' => $data->values()]);
    }

    /**
     * Show details of a specific stall
     * Only accessible if the tenant has an active contract for this stall
     */
    public function show(Stall $stall)
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a tenant
        if (!$user || $user->role !== 'Tenant') {
            abort(403, 'Only tenants can access this page.');
        }
        
        // Verify that this stall belongs to the tenant via an active contract
        // This prevents tenants from accessing stalls they don't rent
        $contract = Contract::where('stallID', $stall->stallID)
            ->where('userID', $user->id)
            ->where('contractStatus', 'Active')
            ->whereNull('deleted_at')
            ->first();

        if (!$contract) {
            abort(403, 'Unauthorized access to this stall. You can only view stalls that you are currently renting.');
        }
        
        // Double-check: ensure the stall is not deleted
        if ($stall->deleted_at) {
            abort(404, 'Stall not found.');
        }

        $stall->load(['marketplace', 'store']);
        
        return response()->json([
            'stallID' => $stall->stallID,
            'formattedStallId' => $stall->formatted_stall_id,
            'stallNo' => strtoupper($stall->stallNo),
            'marketplaceID' => $stall->marketplaceID,
            'marketplace' => $stall->marketplace ? $stall->marketplace->marketplace : null,
            'marketplaceAddress' => $stall->marketplace ? $stall->marketplace->marketplaceAddress : null,
            'size' => $stall->size,
            'rentalFee' => $stall->rentalFee,
            'stallStatus' => $stall->stallStatus,
            'contract' => [
                'contractID' => $contract->contractID,
                'status' => $contract->contractStatus,
                'startDate' => $contract->startDate->format('Y-m-d'),
                'endDate' => $contract->endDate ? $contract->endDate->format('Y-m-d') : null,
            ],
        ]);
    }
}


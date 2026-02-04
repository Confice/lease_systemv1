<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\Stall;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Http\Requests\UpdateBillStatusRequest;
use App\Http\Requests\UploadPaymentProofRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    /**
     * Display bills for admin (all bills)
     */
    public function adminIndex()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }
        return view('admins.bills.index');
    }

    /**
     * Get bills data for admin (AJAX)
     */
    public function adminData(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            return response()->json(['data' => []], 403);
        }
        
        try {
            $status = $request->input('status');
            $search = $request->input('search', '');

            // All non-deleted bills; contract must exist and not be deleted (integrity with leases)
            $query = Bill::with(['contract.user', 'stall.marketplace'])
                ->whereNull('bills.deleted_at')
                ->whereHas('contract', function ($q) {
                    $q->whereNull('contracts.deleted_at');
                });

            // Filter by status
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            // Search by tenant name, stall number, or bill ID
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('billID', 'like', "%{$search}%")
                      ->orWhereHas('contract.user', function($userQuery) use ($search) {
                          $userQuery->whereRaw("CONCAT(firstName, ' ', lastName) LIKE ?", ["%{$search}%"]);
                      })
                      ->orWhereHas('stall', function($stallQuery) use ($search) {
                          $stallQuery->where('stallNo', 'like', "%{$search}%");
                      });
                });
            }

            $bills = $query->orderBy('dueDate', 'desc')->get();

            $data = $bills->map(function ($bill) {
                $tenant = $bill->contract->user ?? null;
                $stall = $bill->stall;
                $marketplace = $stall->marketplace ?? null;

                return [
                    'billID' => $bill->billID,
                    'tenantName' => $tenant ? trim(($tenant->firstName ?? '') . ' ' . ($tenant->lastName ?? '')) : 'N/A',
                    'stallNo' => $stall ? strtoupper($stall->stallNo) : 'N/A',
                    'formattedStallId' => $stall ? $stall->formatted_stall_id : 'N/A',
                    'marketplace' => $marketplace ? $marketplace->marketplace : 'N/A',
                    'amount' => number_format($bill->amount, 2),
                    'dueDate' => $bill->dueDate ? $bill->dueDate->format('M d, Y') : 'N/A',
                    'datePaid' => $bill->datePaid ? $bill->datePaid->format('M d, Y h:i A') : null,
                    'status' => $bill->status,
                    'hasPaymentProof' => !empty($bill->paymentProof),
                    'paymentProofUrl' => $bill->paymentProof ? Storage::disk('public')->url($bill->paymentProof) : null,
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            \Log::error("Admin bills data error: " . $e->getMessage());
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show update bill status form (admin)
     */
    public function showUpdateStatusForm($bill)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $billModel = Bill::where('billID', $bill)
            ->whereNull('deleted_at')
            ->with(['contract.user', 'stall.marketplace'])
            ->first();

        if (!$billModel) {
            return redirect()->route('admins.bills.index')
                ->with('warning', 'Bill not found or it has been archived.');
        }

        return view('admins.bills.update-status', [
            'bill' => $billModel,
        ]);
    }

    /**
     * Update bill status (admin only)
     */
    public function updateStatus(UpdateBillStatusRequest $request, $bill)
    {
        try {

            $bill = Bill::where('billID', $bill)->whereNull('deleted_at')->firstOrFail();

            $updateData = ['status' => $request->status];

            // If marking as paid, set datePaid
            if ($request->status === 'Paid' && !$bill->datePaid) {
                $updateData['datePaid'] = now();
            }

            // If marking as invalid, clear datePaid
            if ($request->status === 'Invalid') {
                $updateData['datePaid'] = null;
            }

            $bill->update($updateData);

            // Log activity
            try {
                ActivityLogService::logUpdate('bills', $bill->billID, "Bill status updated to {$request->status}");
            } catch (\Exception $e) {
                \Log::warning("Failed to log bill update activity: " . $e->getMessage());
            }

            // If request expects JSON (AJAX), return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bill status updated successfully.'
                ]);
            }
            
            // Otherwise, redirect back with success message
            return redirect()
                ->route('admins.bills.index')
                ->with('success', 'Bill status updated successfully.');
        } catch (\Exception $e) {
            \Log::error("Update bill status error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bill status.'
            ], 500);
        }
    }

    /**
     * Archive a bill (soft delete)
     */
    public function archive($bill)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $bill = Bill::where('billID', $bill)->whereNull('deleted_at')->firstOrFail();
            $bill->delete();

            try {
                ActivityLogService::logDelete('bills', $bill->billID, "Archived bill #{$bill->billID}");
            } catch (\Exception $e) {
                \Log::warning("Failed to log bill archive activity: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Bill archived successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error("Archive bill error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive bill.'
            ], 500);
        }
    }

    /**
     * Permanently delete a bill (force delete)
     */
    public function destroy($bill)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $bill = Bill::withTrashed()->where('billID', $bill)->firstOrFail();
            if ($bill->paymentProof) {
                Storage::disk('public')->delete($bill->paymentProof);
            }

            $bill->forceDelete();

            try {
                ActivityLogService::logDelete('bills', $bill->billID, "Deleted bill #{$bill->billID} permanently");
            } catch (\Exception $e) {
                \Log::warning("Failed to log bill delete activity: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Bill deleted permanently.'
            ]);
        } catch (\Exception $e) {
            \Log::error("Delete bill error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bill.'
            ], 500);
        }
    }

    /**
     * Display bills for tenant (their bills only)
     */
    public function tenantIndex()
    {
        return view('tenants.bills.index');
    }

    /**
     * Get bills data for tenant (AJAX)
     */
    public function tenantData(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'Tenant') {
                return response()->json(['data' => []], 403);
            }

            $status = $request->input('status');
            $search = $request->input('search', '');

            // Bills only for this tenant's active, non-deleted contracts (same source as My Leases)
            $query = Bill::with(['stall.marketplace', 'contract'])
                ->whereNull('bills.deleted_at')
                ->whereHas('contract', function ($q) use ($user) {
                    $q->where('userID', $user->id)
                        ->where('contractStatus', 'Active')
                        ->whereNull('contracts.deleted_at');
                });

            // Filter by status
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            // Search by stall number or bill ID
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('billID', 'like', "%{$search}%")
                      ->orWhereHas('stall', function($stallQuery) use ($search) {
                          $stallQuery->where('stallNo', 'like', "%{$search}%");
                      });
                });
            }

            $bills = $query->orderBy('dueDate', 'desc')->get();

            $data = $bills->map(function ($bill) {
                $stall = $bill->stall;
                $marketplace = $stall->marketplace ?? null;

                return [
                    'billID' => $bill->billID,
                    'stallNo' => $stall ? strtoupper($stall->stallNo) : 'N/A',
                    'formattedStallId' => $stall ? $stall->formatted_stall_id : 'N/A',
                    'marketplace' => $marketplace ? $marketplace->marketplace : 'N/A',
                    'amount' => number_format($bill->amount, 2),
                    'dueDate' => $bill->dueDate ? $bill->dueDate->format('M d, Y') : 'N/A',
                    'datePaid' => $bill->datePaid ? $bill->datePaid->format('M d, Y h:i A') : null,
                    'status' => $bill->status,
                    'hasPaymentProof' => !empty($bill->paymentProof),
                    'paymentProofUrl' => $bill->paymentProof ? Storage::disk('public')->url($bill->paymentProof) : null,
                    'canUpload' => in_array($bill->status, ['Pending', 'Due']) && empty($bill->paymentProof),
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            \Log::error("Tenant bills data error: " . $e->getMessage());
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show upload payment proof page (tenant)
     */
    public function showUploadForm($bill)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Tenant') {
            abort(403, 'Unauthorized');
        }

        $bill = Bill::where('billID', $bill)
            ->whereNull('deleted_at')
            ->with(['contract', 'stall.marketplace'])
            ->firstOrFail();

        // Verify bill belongs to user
        if ($bill->contract->userID !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Only allow upload if bill is Pending or Due and no proof exists
        if (!in_array($bill->status, ['Pending', 'Due']) || !empty($bill->paymentProof)) {
            return redirect()
                ->route('tenants.bills.index')
                ->with('error', 'You cannot upload payment proof for this bill.');
        }

        return view('tenants.bills.upload', [
            'bill' => $bill,
        ]);
    }

    /**
     * Upload payment proof (tenant only)
     */
    public function uploadPaymentProof(UploadPaymentProofRequest $request, $bill)
    {
        try {
            $user = Auth::user();

            $bill = Bill::where('billID', $bill)
                ->whereNull('deleted_at')
                ->with('contract')
                ->firstOrFail();

            // Verify bill belongs to user
            if ($bill->contract->userID !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this bill.'
                ], 403);
            }

            // Check if bill can accept payment proof
            if (!in_array($bill->status, ['Pending', 'Due'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment proof can only be uploaded for Pending or Due bills.'
                ], 400);
            }

            // Store file
            $file = $request->file('paymentProof');
            $path = $file->store('payment-proofs', 'public');

            // Update bill
            $bill->update([
                'paymentProof' => $path,
                'dateUploaded' => now(),
                'status' => 'Pending', // Keep as Pending until admin verifies
            ]);

            // Log activity
            try {
                ActivityLogService::logUpdate('bills', $bill->billID, 'Payment proof uploaded');
            } catch (\Exception $e) {
                \Log::warning("Failed to log payment proof upload activity: " . $e->getMessage());
            }

            // If request expects JSON (AJAX), return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment proof uploaded successfully. Waiting for admin verification.',
                    'paymentProofUrl' => Storage::disk('public')->url($path),
                ]);
            }
            
            // Otherwise, redirect back with success message
            return redirect()
                ->route('tenants.bills.index')
                ->with('success', 'Payment proof uploaded successfully. Waiting for admin verification.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error("Upload payment proof error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload payment proof.'
            ], 500);
        }
    }

    /**
     * Generate monthly bills for active contracts
     * This can be called manually by admin or via a scheduled job
     */
    public function generateMonthlyBills()
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'Lease Manager') {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }

            // Get all active contracts
            $activeContracts = Contract::where('contractStatus', 'Active')
                ->whereNull('deleted_at')
                ->with(['stall'])
                ->get();

            $billsGenerated = 0;
            $errors = [];

            foreach ($activeContracts as $contract) {
                try {
                    $stall = $contract->stall;
                    if (!$stall || !$stall->rentalFee) {
                        continue; // Skip if stall or rental fee is missing
                    }

                    // Calculate next bill due date (1 month from now or from contract start/end)
                    $lastBill = Bill::where('contractID', $contract->contractID)
                        ->whereNull('deleted_at')
                        ->orderBy('dueDate', 'desc')
                        ->first();

                    if ($lastBill && $lastBill->dueDate) {
                        $nextDueDate = $lastBill->dueDate->copy()->addMonth();
                    } else {
                        // First bill - due 1 month from contract start
                        $nextDueDate = $contract->startDate->copy()->addMonth();
                    }

                    // Don't generate if contract has ended
                    if ($contract->endDate && $nextDueDate->gt($contract->endDate)) {
                        continue;
                    }

                    // Check if bill for this date already exists
                    $existingBill = Bill::where('contractID', $contract->contractID)
                        ->where('dueDate', $nextDueDate->format('Y-m-d'))
                        ->whereNull('deleted_at')
                        ->first();

                    if (!$existingBill) {
                        $bill = Bill::create([
                            'stallID' => $contract->stallID,
                            'contractID' => $contract->contractID,
                            'dueDate' => $nextDueDate,
                            'amount' => $stall->rentalFee,
                            'status' => 'Pending',
                        ]);
                        $billsGenerated++;

                        // Log activity
                        try {
                            ActivityLogService::logCreate('bills', $bill->billID, "Monthly bill generated for contract #{$contract->contractID}");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log bill creation activity: " . $e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "Contract {$contract->contractID}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Generated {$billsGenerated} bill(s).",
                'billsGenerated' => $billsGenerated,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            \Log::error("Generate monthly bills error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate bills.'
            ], 500);
        }
    }
}


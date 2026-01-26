<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Stall;
use App\Models\User;
use App\Models\Application;
use App\Models\Marketplace;
use App\Models\Document;
use App\Models\Requirement;
use App\Services\ActivityLogService;
use App\Http\Requests\RenewContractRequest;
use App\Http\Requests\TerminateContractRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\PresentationScheduled;

class ContractController extends Controller
{
    /**
     * Display the Prospective Tenants (Contracts) index page
     */
    public function index()
    {
        return view('admins.prospective_tenants.index');
    }

    /**
     * Get vacant stalls with application counts for Prospective Tenants module
     */
    public function data(Request $request)
    {
        try {
            $sortBy = $request->input('sort', 'stall_name');
            
            // Get only vacant stalls with application deadline set (not null and not passed)
            $stallsQuery = Stall::where('stallStatus', 'Vacant')
                ->whereNull('deleted_at')
                ->whereNotNull('applicationDeadline')
                ->where('applicationDeadline', '>=', now())
                ->with(['marketplace', 'applications' => function($query) {
                    $query->whereNull('deleted_at');
                }]);
            
            // Apply sorting
            switch ($sortBy) {
                case 'location':
                    // Sort by location (will be handled after fetching)
                    $stallsQuery->orderBy('stallNo'); // Default order, will sort in PHP
                    break;
                case 'recent_application':
                    // Sort by most recent application date (will be handled after fetching)
                    $stallsQuery->orderBy('stallNo'); // Default order, will sort in PHP
                    break;
                case 'deadline_desc':
                    $stallsQuery->orderBy('applicationDeadline', 'desc');
                    break;
                case 'deadline_asc':
                    $stallsQuery->orderBy('applicationDeadline', 'asc');
                    break;
                case 'stall_name':
                default:
                    $stallsQuery->orderBy('stallNo');
                    break;
            }
            
            $stalls = $stallsQuery->get();

            $data = $stalls->map(function ($stall) {
                // Count active applications (excluding withdrawn)
                $applicationCount = $stall->applications
                    ->where('deleted_at', null)
                    ->where('appStatus', '!=', 'Withdrawn')
                    ->count();
                
                // Get most recent application date
                $mostRecentApplication = $stall->applications
                    ->where('deleted_at', null)
                    ->where('appStatus', '!=', 'Withdrawn')
                    ->sortByDesc('dateApplied')
                    ->first();
                
                return [
                    'stallID' => $stall->stallID,
                    'stallNo' => strtoupper($stall->stallNo),
                    'formattedStallId' => $stall->formatted_stall_id,
                    'location' => $stall->marketplace ? $stall->marketplace->marketplace : '-',
                    'size' => $stall->size ?? '-',
                    'rentalFee' => $stall->rentalFee ? number_format($stall->rentalFee, 2) : '-',
                    'applicationDeadline' => $stall->applicationDeadline ? $stall->applicationDeadline->format('Y-m-d') : null,
                    'applicationCount' => $applicationCount,
                    'mostRecentApplicationDate' => $mostRecentApplication && $mostRecentApplication->dateApplied 
                        ? $mostRecentApplication->dateApplied->format('Y-m-d H:i:s') 
                        : null,
                ];
            });
            
            // Sort by location or most recent application if needed (after mapping to get the data)
            if ($sortBy === 'location') {
                $data = $data->sortBy(function ($stall) {
                    return $stall['location'];
                })->values();
            } elseif ($sortBy === 'recent_application') {
                $data = $data->sortByDesc(function ($stall) {
                    return $stall['mostRecentApplicationDate'] ?? '1970-01-01 00:00:00';
                })->values();
            }

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            \Log::error("Prospective Tenants data error: " . $e->getMessage());
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display applications for a specific stall
     */
    public function applications($stall)
    {
        $stallModel = Stall::with('marketplace')->findOrFail($stall);
        
        return view('admins.prospective_tenants.applications', [
            'stall' => $stallModel
        ]);
    }

    /**
     * Get applications data for a specific stall (AJAX)
     */
    public function applicationsData($stall)
    {
        try {
            $stallModel = Stall::with('marketplace')->findOrFail($stall);
            
            // Get all applications for this stall (excluding soft-deleted)
            $applications = Application::where('stallID', $stall)
                ->whereNull('deleted_at')
                ->with(['user', 'documents' => function($query) {
                    $query->where('documentType', 'Proposal')
                          ->whereNull('deleted_at');
                }])
                ->orderBy('dateApplied', 'desc')
                ->get();

            $data = $applications->map(function ($application, $index) {
                $user = $application->user;
                $fullName = trim(($user->firstName ?? '') . ' ' . ($user->middleName ?? '') . ' ' . ($user->lastName ?? ''));
                
                // Check if requirements are submitted (has documents)
                $hasRequirements = $application->documents->count() > 0;
                
                return [
                    'applicationID' => $application->applicationID,
                    'number' => $index + 1,
                    'name' => $fullName ?: '-',
                    'hasRequirements' => $hasRequirements,
                    'requirementCount' => $application->documents->count(),
                    'status' => $application->appStatus,
                    'dateApplied' => $application->dateApplied ? $application->dateApplied->format('Y-m-d H:i:s') : null,
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            \Log::error("Applications data error: " . $e->getMessage());
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get application details for admin viewing
     */
    public function applicationDetails($application)
    {
        try {
            $applicationModel = Application::where('applicationID', $application)
                ->whereNull('deleted_at')
                ->with(['stall.marketplace', 'user', 'documents'])
                ->first();

            if (!$applicationModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 404);
            }

            $stall = $applicationModel->stall;
            $user = $applicationModel->user;
            
            // Get documents for this application
            $document = Document::where('applicationID', $applicationModel->applicationID)
                ->where('documentType', 'Proposal')
                ->whereNull('deleted_at')
                ->first();

            // Get Proposal requirements
            $proposalRequirements = Requirement::where('document_type', 'Proposal')
                ->whereNull('deleted_at')
                ->orderBy('sort_order')
                ->orderBy('requirement_name')
                ->get();

            // Get Tenancy requirements (only if application is approved)
            $tenancyRequirements = collect();
            if ($applicationModel->appStatus === 'Requirements Received') {
                $tenancyRequirements = Requirement::where('document_type', 'Tenancy')
                    ->whereNull('deleted_at')
                    ->orderBy('sort_order')
                    ->orderBy('requirement_name')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'application' => [
                    'applicationID' => $applicationModel->applicationID,
                    'stallID' => $applicationModel->stallID,
                    'appStatus' => $applicationModel->appStatus,
                    'dateApplied' => $applicationModel->dateApplied ? $applicationModel->dateApplied->format('Y-m-d H:i:s') : null,
                    'remarks' => $applicationModel->remarks,
                ],
                'stall' => [
                    'stallID' => $stall->stallID,
                    'stallNo' => $stall->stallNo,
                    'formattedStallId' => $stall->formatted_stall_id,
                    'marketplace' => $stall->marketplace ? $stall->marketplace->marketplace : null,
                    'size' => $stall->size,
                    'rentalFee' => $stall->rentalFee,
                    'applicationDeadline' => $stall->applicationDeadline ? $stall->applicationDeadline->format('Y-m-d') : null,
                ],
                'document' => $document ? [
                    'documentID' => $document->documentID,
                    'files' => $this->processDocumentFiles($document->files),
                    'docStatus' => $document->docStatus,
                    'revisionComment' => $document->revisionComment,
                ] : null,
                'user' => $user ? [
                    'id' => $user->id,
                    'firstName' => $user->firstName,
                    'middleName' => $user->middleName,
                    'lastName' => $user->lastName,
                    'email' => $user->email,
                    'contactNo' => $user->contactNo,
                    'homeAddress' => $user->homeAddress,
                    'birthDate' => $user->birthDate ? $user->birthDate->format('Y-m-d') : null,
                    'userStatus' => $user->userStatus,
                    'customReason' => $user->customReason,
                    'created_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : null,
                    'role' => $user->role,
                ] : null,
                'proposalRequirements' => $proposalRequirements->map(function($req) {
                    return [
                        'requirementID' => $req->requirementID,
                        'requirement_name' => $req->requirement_name,
                        'is_active' => $req->is_active,
                    ];
                })->values()->toArray(),
                'tenancyRequirements' => $tenancyRequirements->map(function($req) {
                    return [
                        'requirementID' => $req->requirementID,
                        'requirement_name' => $req->requirement_name,
                        'is_active' => $req->is_active,
                    ];
                })->values()->toArray(),
            ]);
        } catch (\Exception $e) {
            \Log::error("Application details error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process document files to add proper URLs
     */
    private function processDocumentFiles($files)
    {
        if (empty($files)) {
            return [];
        }

        $filesArray = is_string($files) ? json_decode($files, true) : ($files ?? []);
        
        if (!is_array($filesArray)) {
            return [];
        }

        // Process each file to ensure proper URL
        return array_map(function($file) {
            if (isset($file['filePath'])) {
                // filePath from store() is like "documents/filename.pdf"
                // Generate proper URL using Storage facade
                $file['fileUrl'] = \Storage::disk('public')->url($file['filePath']);
            }
            return $file;
        }, $filesArray);
    }

    /**
     * Generate contract number for display (LC-YYYYMMDD-000X)
     */
    private function generateContractNumber($contract)
    {
        $date = $contract->created_at ? $contract->created_at : now();
        $dateStr = $date->format('Ymd');
        
        // Get the sequence number for contracts created on the same date
        // Count contracts created on the same date with contractID <= current contractID
        $sameDateContracts = Contract::whereDate('created_at', $date->format('Y-m-d'))
            ->whereNull('deleted_at')
            ->orderBy('contractID', 'asc')
            ->pluck('contractID')
            ->toArray();
        
        $index = array_search($contract->contractID, $sameDateContracts);
        $sequence = ($index !== false) ? $index + 1 : count($sameDateContracts);
        
        $sequenceStr = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return "LC-{$dateStr}-{$sequenceStr}";
    }

    /**
     * Schedule presentation for an application
     */
    public function schedulePresentation(Request $request, $application)
    {
        try {
            $request->validate([
                'presentation_date' => 'required|date|after_or_equal:today',
                'presentation_time' => 'required|string',
            ]);

            $applicationModel = Application::where('applicationID', $application)
                ->whereNull('deleted_at')
                ->with(['user', 'stall.marketplace'])
                ->first();

            if (!$applicationModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 404);
            }

            // Check if status is Proposal Received
            if ($applicationModel->appStatus !== 'Proposal Received') {
                return response()->json([
                    'success' => false,
                    'message' => 'Presentation can only be scheduled for applications with "Proposal Received" status.'
                ], 400);
            }

            // Combine date and time
            $presentationDateTime = $request->presentation_date . ' ' . $request->presentation_time;
            $presentationDateTimeObj = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $presentationDateTime);

            // Update application status and store presentation date/time
            $applicationModel->update([
                'appStatus' => 'Presentation Scheduled',
                'noticeDate' => $presentationDateTimeObj,
                'noticeType' => 'Presentation Scheduled',
                'remarks' => $request->presentation_time // Store time in remarks for email
            ]);

            // Get marketplace information
            $marketplace = $applicationModel->stall->marketplace;
            $user = $applicationModel->user;

            // Format date for email
            $formattedDate = $presentationDateTimeObj->format('F j, Y');
            $formattedTime = $presentationDateTimeObj->format('g:i A');

            // Send email to tenant
            try {
                Mail::to($user->email)->send(new PresentationScheduled(
                    $user,
                    $applicationModel,
                    $formattedDate,
                    $formattedTime,
                    $marketplace
                ));
            } catch (\Exception $e) {
                \Log::error("Failed to send presentation scheduled email: " . $e->getMessage());
                // Don't fail the request if email fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Presentation scheduled successfully. Email notification sent to tenant.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error("Error scheduling presentation: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule presentation. Please try again.'
            ], 500);
        }
    }

    /**
     * Display leases management page for admin
     */
    public function leasesIndex()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }
        return view('admins.leases.index');
    }

    /**
     * Get leases data for admin (AJAX)
     */
    public function leasesData(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            return response()->json(['data' => []], 403);
        }

        try {
            $status = $request->input('status', 'all');
            $search = $request->input('search', '');

            $query = Contract::with(['user', 'stall.marketplace'])
                ->whereNull('deleted_at');

            // Filter by status
            if ($status !== 'all') {
                $query->where('contractStatus', $status);
            }

            // Search by tenant name, stall number, or contract ID
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('contractID', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->whereRaw("CONCAT(firstName, ' ', lastName) LIKE ?", ["%{$search}%"]);
                      })
                      ->orWhereHas('stall', function($stallQuery) use ($search) {
                          $stallQuery->where('stallNo', 'like', "%{$search}%");
                      });
                });
            }

            $contracts = $query->orderBy('startDate', 'desc')->get();

            $data = $contracts->map(function ($contract) {
                $tenant = $contract->user;
                $stall = $contract->stall;
                $marketplace = $stall->marketplace ?? null;
                $daysRemaining = null;
                
                if ($contract->endDate) {
                    $daysRemaining = now()->diffInDays($contract->endDate, false);
                }

                return [
                    'contractID' => $contract->contractID,
                    'tenantName' => $tenant ? trim(($tenant->firstName ?? '') . ' ' . ($tenant->lastName ?? '')) : 'N/A',
                    'tenantEmail' => $tenant->email ?? 'N/A',
                    'stallNo' => $stall ? strtoupper($stall->stallNo) : 'N/A',
                    'formattedStallId' => $stall ? $stall->formatted_stall_id : 'N/A',
                    'marketplace' => $marketplace ? $marketplace->marketplace : 'N/A',
                    'rentalFee' => $stall ? number_format($stall->rentalFee, 2) : '0.00',
                    'startDate' => $contract->startDate ? $contract->startDate->format('M d, Y') : 'N/A',
                    'endDate' => $contract->endDate ? $contract->endDate->format('M d, Y') : 'No end date',
                    'daysRemaining' => $daysRemaining,
                    'contractStatus' => $contract->contractStatus,
                    'expiringStatus' => $contract->expiringStatus,
                    'canRenew' => $contract->contractStatus === 'Active' && $daysRemaining !== null && $daysRemaining <= 30,
                    'canTerminate' => $contract->contractStatus === 'Active',
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            \Log::error("Leases data error: " . $e->getMessage());
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get contract details for viewing
     */
    public function show($contract)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        try {
            $contract = Contract::with(['user', 'stall.marketplace', 'bills', 'feedbacks'])
                ->where('contractID', $contract)
                ->whereNull('deleted_at')
                ->firstOrFail();

            $tenant = $contract->user;
            $stall = $contract->stall;
            $marketplace = $stall->marketplace ?? null;

            return response()->json([
                'success' => true,
                'contract' => [
                    'contractID' => $contract->contractID,
                    'startDate' => $contract->startDate ? $contract->startDate->format('Y-m-d') : null,
                    'endDate' => $contract->endDate ? $contract->endDate->format('Y-m-d') : null,
                    'contractStatus' => $contract->contractStatus,
                    'expiringStatus' => $contract->expiringStatus,
                    'customReason' => $contract->customReason,
                ],
                'tenant' => $tenant ? [
                    'id' => $tenant->id,
                    'name' => trim(($tenant->firstName ?? '') . ' ' . ($tenant->lastName ?? '')),
                    'email' => $tenant->email,
                    'contactNo' => $tenant->contactNo,
                ] : null,
                'stall' => $stall ? [
                    'stallID' => $stall->stallID,
                    'stallNo' => $stall->stallNo,
                    'formattedStallId' => $stall->formatted_stall_id,
                    'marketplace' => $marketplace ? $marketplace->marketplace : null,
                    'rentalFee' => $stall->rentalFee,
                ] : null,
                'billsCount' => $contract->bills->count(),
                'feedbacksCount' => $contract->feedbacks->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error("Contract show error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load contract details.'
            ], 500);
        }
    }

    /**
     * Renew a contract (extend end date)
     */
    public function renew(RenewContractRequest $request, $contract)
    {
        try {
            $contract = Contract::where('contractID', $contract)
                ->whereNull('deleted_at')
                ->firstOrFail();

            if ($contract->contractStatus !== 'Active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only active contracts can be renewed.'
                ], 400);
            }

            // Calculate new end date
            $currentEndDate = $contract->endDate ?? now();
            $newEndDate = $currentEndDate->copy()->addMonths($request->months);

            $contract->update([
                'endDate' => $newEndDate,
                'expiringStatus' => null, // Clear any expiring status
            ]);

            // Generate bill for the renewal period
            $stall = $contract->stall;
            if ($stall && $stall->rentalFee) {
                $totalAmount = $stall->rentalFee * $request->months;
                
                // Create monthly bills for the renewal period
                for ($i = 0; $i < $request->months; $i++) {
                    $billDueDate = $currentEndDate->copy()->addMonths($i + 1);
                    
                    \App\Models\Bill::create([
                        'stallID' => $contract->stallID,
                        'contractID' => $contract->contractID,
                        'dueDate' => $billDueDate,
                        'amount' => $stall->rentalFee,
                        'status' => 'Pending',
                    ]);
                }
            }

            // Log activity
            try {
                ActivityLogService::logUpdate('contracts', $contract->contractID, "Contract renewed for {$request->months} month(s). New end date: {$newEndDate->format('M d, Y')}");
            } catch (\Exception $e) {
                \Log::warning("Failed to log contract renewal activity: " . $e->getMessage());
            }

            // If request expects JSON (AJAX), return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Contract renewed for {$request->months} month(s). Bills generated.",
                    'newEndDate' => $newEndDate->format('M d, Y'),
                ]);
            }
            
            // Otherwise, redirect back with success message
            return redirect()
                ->route('admins.leases.index')
                ->with('success', "Contract renewed for {$request->months} month(s). Bills generated.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error("Contract renew error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to renew contract.'
            ], 500);
        }
    }

    /**
     * Show terminate contract form (admin)
     */
    public function showTerminateForm($contract)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $contract = Contract::where('contractID', $contract)
            ->whereNull('deleted_at')
            ->with(['user', 'stall.marketplace'])
            ->firstOrFail();

        if ($contract->contractStatus !== 'Active') {
            return redirect()
                ->route('admins.leases.index')
                ->with('error', 'Only active contracts can be terminated.');
        }

        return view('admins.leases.terminate', [
            'contract' => $contract,
        ]);
    }

    /**
     * Terminate a contract
     */
    public function terminate(TerminateContractRequest $request, $contract)
    {
        try {

            $contract = Contract::where('contractID', $contract)
                ->whereNull('deleted_at')
                ->firstOrFail();

            if ($contract->contractStatus !== 'Active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only active contracts can be terminated.'
                ], 400);
            }

            DB::beginTransaction();
            try {
                // Update contract status
                $contract->update([
                    'contractStatus' => 'Terminated',
                    'endDate' => now(),
                    'customReason' => $request->reason,
                ]);

                // Update stall status to Vacant
                $stall = $contract->stall;
                if ($stall) {
                    $stall->update([
                        'stallStatus' => 'Vacant',
                    ]);
                }

                DB::commit();

                // Log activity
                try {
                    ActivityLogService::logUpdate('contracts', $contract->contractID, "Contract terminated. Reason: {$request->reason}");
                } catch (\Exception $e) {
                    \Log::warning("Failed to log contract termination activity: " . $e->getMessage());
                }

                // If request expects JSON (AJAX), return JSON response
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Contract terminated successfully.'
                    ]);
                }
                
                // Otherwise, redirect back with success message
                return redirect()
                    ->route('admins.leases.index')
                    ->with('success', 'Contract terminated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error("Contract terminate error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to terminate contract.'
            ], 500);
        }
    }

    /**
     * Display tenant's leases page
     */
    public function tenantLeasesIndex()
    {
        return view('tenants.leases.index');
    }

    /**
     * Get tenant's leases data (AJAX)
     */
    public function tenantLeasesData(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'Tenant') {
                return response()->json(['data' => []], 403);
            }

            $status = $request->input('status', 'all');
            $search = $request->input('search', '');

            $query = Contract::where('userID', $user->id)
                ->with(['stall.marketplace'])
                ->whereNull('deleted_at');

            // Filter by status
            if ($status !== 'all') {
                $query->where('contractStatus', $status);
            }

            // Search by stall number or contract ID
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('contractID', 'like', "%{$search}%")
                      ->orWhereHas('stall', function($stallQuery) use ($search) {
                          $stallQuery->where('stallNo', 'like', "%{$search}%");
                      });
                });
            }

            $contracts = $query->orderBy('startDate', 'desc')->get();

            $data = $contracts->map(function ($contract) {
                $stall = $contract->stall;
                $marketplace = $stall->marketplace ?? null;
                $daysRemaining = null;
                
                if ($contract->endDate) {
                    $daysRemaining = now()->diffInDays($contract->endDate, false);
                }

                // Get pending bills count
                $pendingBills = $contract->bills()
                    ->where('status', 'Pending')
                    ->whereNull('deleted_at')
                    ->count();

                return [
                    'contractID' => $contract->contractID,
                    'stallNo' => $stall ? strtoupper($stall->stallNo) : 'N/A',
                    'formattedStallId' => $stall ? $stall->formatted_stall_id : 'N/A',
                    'marketplace' => $marketplace ? $marketplace->marketplace : 'N/A',
                    'rentalFee' => $stall ? number_format($stall->rentalFee, 2) : '0.00',
                    'startDate' => $contract->startDate ? $contract->startDate->format('M d, Y') : 'N/A',
                    'endDate' => $contract->endDate ? $contract->endDate->format('M d, Y') : 'No end date',
                    'daysRemaining' => $daysRemaining,
                    'contractStatus' => $contract->contractStatus,
                    'expiringStatus' => $contract->expiringStatus,
                    'pendingBills' => $pendingBills,
                    'needsRenewal' => $daysRemaining !== null && $daysRemaining <= 30 && $daysRemaining > 0,
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            \Log::error("Tenant leases data error: " . $e->getMessage());
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }
}


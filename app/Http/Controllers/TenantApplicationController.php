<?php

namespace App\Http\Controllers;

use App\Models\Stall;
use App\Models\Requirement;
use App\Models\Application;
use App\Models\Document;
use App\Models\Contract;
use App\Mail\ProposalSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class TenantApplicationController extends Controller
{
    /**
     * Show the submission of requirements page
     */
    public function create(Request $request)
    {
        $stallId = $request->query('stall');
        
        if (!$stallId) {
            return redirect()->route('tenants.marketplace.index')
                ->with('error', 'Please select a stall to apply for.');
        }

        $stall = Stall::with('marketplace')->findOrFail($stallId);
        
        // Check if user already has an application for this stall
        $existingApplication = Application::where('userID', Auth::id())
            ->where('stallID', $stallId)
            ->whereNull('deleted_at')
            ->first();
        
        // If application exists and is not "Proposal Rejected" or "Withdrawn", prevent reapplication
        if ($existingApplication && !in_array($existingApplication->appStatus, ['Proposal Rejected', 'Withdrawn'])) {
            return redirect()->route('tenants.marketplace.index')
                ->with('error', 'You already have an active application for this stall. Please wait for the review process to complete.');
        }

        // Get Proposal requirements (always show - include both required and optional)
        $proposalRequirements = Requirement::where('document_type', 'Proposal')
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('requirement_name')
            ->get();

        // Get Tenancy requirements (only show if application is approved - include both required and optional)
        // Show tenancy requirements if status is 'Requirements Received'
        $tenancyRequirements = collect();
        if ($existingApplication && $existingApplication->appStatus === 'Requirements Received') {
            $tenancyRequirements = Requirement::where('document_type', 'Tenancy')
                ->whereNull('deleted_at')
                ->orderBy('sort_order')
                ->orderBy('requirement_name')
                ->get();
        }

        return view('tenants.applications.create', [
            'stall' => $stall,
            'existingApplication' => $existingApplication,
            'proposalRequirements' => $proposalRequirements,
            'tenancyRequirements' => $tenancyRequirements,
        ]);
    }

    /**
     * Store the application and uploaded documents
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stallID' => 'required|exists:stalls,stallID',
            'files' => 'array',
            'files.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $stallId = $validated['stallID'];

            // Check if application already exists
            $existingApplication = Application::where('userID', $userId)
                ->where('stallID', $stallId)
                ->whereNull('deleted_at')
                ->first();

            // Prevent duplicate applications unless status is "Proposal Rejected" or "Withdrawn"
            if ($existingApplication && !in_array($existingApplication->appStatus, ['Proposal Rejected', 'Withdrawn'])) {
                DB::rollBack();
                return back()->withInput()
                    ->with('error', 'You already have an active application for this stall. Please wait for the review process to complete.');
            }

            // Create new application or update rejected/withdrawn one
            if (!$existingApplication || in_array($existingApplication->appStatus, ['Proposal Rejected', 'Withdrawn'])) {
                // If rejected or withdrawn application exists, update it; otherwise create new
                if ($existingApplication && in_array($existingApplication->appStatus, ['Proposal Rejected', 'Withdrawn'])) {
                    // Update the rejected/withdrawn application to restart the process
                    $existingApplication->update([
                        'appStatus' => 'Proposal Received',
                        'dateApplied' => now(),
                    ]);
                    $application = $existingApplication;
                } else {
                    // Create new application with status "Proposal Received"
                    $application = Application::create([
                        'userID' => $userId,
                        'stallID' => $stallId,
                        'appStatus' => 'Proposal Received',
                        'dateApplied' => now(),
                    ]);
                }
            } else {
                // This should not happen due to the check above, but just in case
                $application = $existingApplication;
            }

            // Get all proposal requirements (both required and optional) to match uploaded files
            $proposalRequirements = Requirement::where('document_type', 'Proposal')
                ->whereNull('deleted_at')
                ->get()
                ->keyBy('requirement_name');
            
            // Validate that required files are uploaded
            $requiredRequirements = Requirement::where('document_type', 'Proposal')
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->pluck('requirement_name')
                ->toArray();
            
            $uploadedFiles = array_keys($request->file('files', []));
            $missingRequired = array_diff($requiredRequirements, $uploadedFiles);
            
            if (!empty($missingRequired)) {
                return back()->withInput()
                    ->with('error', 'Please upload all required documents: ' . implode(', ', $missingRequired));
            }

            // Store uploaded files
            $filesData = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $requirementName => $file) {
                    if ($file && $file->isValid()) {
                        // Store file in storage/app/public/documents
                        $path = $file->store('documents', 'public');
                        
                        $filesData[] = [
                            'requirementName' => $requirementName,
                            'filePath' => $path,
                            'originalName' => $file->getClientOriginalName(),
                            'dateUploaded' => now()->toDateString(),
                        ];
                    }
                }
            }

            // Create or update document record
            $document = Document::updateOrCreate(
                [
                    'userID' => $userId,
                    'applicationID' => $application->applicationID,
                    'documentType' => 'Proposal',
                ],
                [
                    'files' => json_encode($filesData),
                    'docStatus' => 'Pending',
                ]
            );

            // Create contract record for Leases module
            // Tenant and Start Date will be null until status becomes Requirements Received
            $contract = Contract::firstOrCreate(
                [
                    'userID' => $userId,
                    'stallID' => $stallId,
                ],
                [
                    'startDate' => null, // Will be set when status becomes Requirements Received
                    'endDate' => null, // Will be set to 1 year after start date when contract is given
                    'contractStatus' => 'Active',
                ]
            );

            DB::commit();

            // Send email notification to tenant
            try {
                $user = Auth::user();
                Mail::to($user->email)->send(new ProposalSubmitted($user));
            } catch (\Exception $e) {
                \Log::error("Failed to send proposal submission email: " . $e->getMessage());
                // Don't fail the request if email fails
            }

            return redirect()->route('tenants.stalls.index')
                ->with('success', 'Your application has been submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Failed to submit application: " . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to submit application. Please try again.');
        }
    }

    /**
     * Withdraw an application (set status to Withdrawn, keep record and files visible)
     */
    public function withdraw($id)
    {
        try {
            $user = Auth::user();
            
            $application = Application::where('applicationID', $id)
                ->where('userID', $user->id)
                ->whereNull('deleted_at')
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found or unauthorized.'
                ], 404);
            }

            // Update application status to "Withdrawn" - keep all documents and files
            $application->update([
                'appStatus' => 'Withdrawn'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application withdrawn successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error withdrawing application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to withdraw application. Please try again.'
            ], 500);
        }
    }

    /**
     * Get application details for viewing submission
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $application = Application::where('applicationID', $id)
            ->where('userID', $user->id)
            ->whereNull('deleted_at')
            ->with(['stall.marketplace', 'documents'])
            ->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found or unauthorized.'
            ], 404);
        }

        $stall = $application->stall;
        
        // Get documents for this application
        $document = Document::where('applicationID', $application->applicationID)
            ->where('userID', $user->id)
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
        if ($application->appStatus === 'Requirements Received') {
            $tenancyRequirements = Requirement::where('document_type', 'Tenancy')
                ->whereNull('deleted_at')
                ->orderBy('sort_order')
                ->orderBy('requirement_name')
                ->get();
        }

        return response()->json([
            'success' => true,
            'application' => [
                'applicationID' => $application->applicationID,
                'stallID' => $application->stallID,
                'appStatus' => $application->appStatus,
                'dateApplied' => $application->dateApplied ? $application->dateApplied->format('Y-m-d H:i:s') : null,
                'remarks' => $application->remarks,
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
                $file['fileUrl'] = Storage::disk('public')->url($file['filePath']);
            }
            return $file;
        }, $filesArray);
    }
}

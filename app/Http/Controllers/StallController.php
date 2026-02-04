<?php

namespace App\Http\Controllers;

use App\Models\Stall;
use App\Models\Marketplace;
use App\Models\Store;
use App\Models\Contract;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StallController extends Controller
{
    public function index()
    {
        $marketplaces = Marketplace::whereNull('deleted_at')->orderBy('marketplace')->get();
        $stores = Store::orderBy('storeName')->get();
        return view('admins.stalls.index', compact('marketplaces', 'stores'));
    }

    public function getTenants()
    {
        $tenants = \App\Models\User::where('role', 'Tenant')
            ->where('userStatus', 'Active')
            ->whereNull('deleted_at')
            ->orderBy('firstName')
            ->orderBy('lastName')
            ->get()
            ->map(function ($user) {
                return [
                    'userID' => $user->id,
                    'fullName' => trim($user->firstName . ' ' . ($user->middleName ? $user->middleName . ' ' : '') . $user->lastName),
                ];
            });

        return response()->json(['tenants' => $tenants]);
    }

    public function assignTenant(Request $request)
    {
        try {
            $validated = $request->validate([
                'stallID' => 'required|exists:stalls,stallID',
                'userID' => 'required|exists:users,id',
            ]);

            DB::beginTransaction();

            $stall = Stall::findOrFail($validated['stallID']);
            
            // Check if stall is already occupied
            if ($stall->stallStatus === 'Occupied') {
                return response()->json([
                    'success' => false,
                    'message' => 'This stall is already occupied.'
                ], 422);
            }

            // Create a new contract
            $contract = Contract::create([
                'stallID' => $validated['stallID'],
                'userID' => $validated['userID'],
                'startDate' => now(),
                'contractStatus' => 'Active',
            ]);

            // Update stall status to Occupied
            $stall->update([
                'stallStatus' => 'Occupied',
                'lastStatusChange' => now(),
            ]);

            DB::commit();

            // Log activity
            try {
                \App\Services\ActivityLogService::logCreate('contracts', $contract->contractID, "Tenant assigned to stall #{$stall->stallID}");
                \App\Services\ActivityLogService::logUpdate('stalls', $stall->stallID, "Stall status updated to Occupied");
            } catch (\Exception $e) {
                \Log::warning("Failed to log tenant assignment activity: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Tenant assigned successfully and stall status updated to Occupied.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Failed to assign tenant: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign tenant: ' . $e->getMessage()
            ], 500);
        }
    }

    public function data()
    {
        $stalls = Stall::with(['marketplace', 'store', 'contracts.user'])
            ->whereNull('deleted_at')
            ->orderBy('stallID')
            ->get();

        $data = $stalls->map(function ($stall) {
            // Get the current active contract's user as "Rent By"
            $rentBy = null;
            $contract = $stall->contracts()->where('contractStatus', 'Active')->first();
            if ($contract && $contract->user) {
                $user = $contract->user;
                $rentBy = trim(($user->firstName ?? '') . ' ' . ($user->middleName ?? '') . ' ' . ($user->lastName ?? ''));
            }
            
            return [
                'stallID' => $stall->stallID,
                'formatted_stall_id' => $stall->formatted_stall_id,
                'stallNo' => strtoupper($stall->stallNo),
                'marketplace' => $stall->marketplace ? $stall->marketplace->marketplace : '-',
                'rentBy' => $rentBy ?? '-',
                'contractID' => $contract ? $contract->contractID : null,
                'contractStatus' => $contract ? $contract->contractStatus : null,
                'stallStatus' => $stall->stallStatus,
                'size' => $stall->size ?? '-',
                'rentalFee' => number_format($stall->rentalFee, 2),
                'applicationDeadline' => optional($stall->applicationDeadline)->format('Y-m-d'),
                'lastStatusChange' => optional($stall->lastStatusChange)->format('Y-m-d'),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function show(Stall $stall)
    {
        $stall->load(['marketplace', 'store.user', 'contracts.user', 'contracts.documents']);
        
        $contract = $stall->contracts()->where('contractStatus', 'Active')->first();
        
        // Get user information if contract exists
        $userData = null;
        if ($contract && $contract->user) {
            $user = $contract->user;
            $fullName = trim(($user->firstName ?? '') . ' ' . ($user->middleName ?? '') . ' ' . ($user->lastName ?? ''));
            $userData = [
                'userID' => $user->id,
                'firstName' => $user->firstName,
                'middleName' => $user->middleName,
                'lastName' => $user->lastName,
                'fullName' => $fullName,
                'email' => $user->email,
                'contactNo' => $user->contactNo,
                'homeAddress' => $user->homeAddress,
                'birthDate' => $user->birthDate,
                'userStatus' => $user->userStatus,
                'created_at' => $user->created_at,
            ];
        }
        
        // Get documents/requirements for the contract
        $requirements = [];
        if ($contract) {
            $documents = $contract->documents()->whereNull('deleted_at')->get();
            foreach ($documents as $doc) {
                $requirements[] = [
                    'documentID' => $doc->documentID,
                    'documentType' => $doc->documentType,
                    'docStatus' => $doc->docStatus,
                    'files' => $doc->files ?? [],
                    'requirementConfig' => $doc->requirementConfig ?? [],
                    'revisionComment' => $doc->revisionComment,
                    'created_at' => optional($doc->created_at)->format('Y-m-d H:i:s'),
                    'updated_at' => optional($doc->updated_at)->format('Y-m-d H:i:s'),
                ];
            }
        }
        
        return response()->json([
            'stallID' => $stall->stallID,
            'stallNo' => strtoupper($stall->stallNo),
            'marketplaceID' => $stall->marketplaceID,
            'marketplace' => $stall->marketplace ? $stall->marketplace->marketplace : null,
            'store' => $stall->store ? $stall->store->storeName : null,
            'storeUser' => $stall->store && $stall->store->user 
                ? $stall->store->user->firstName . ' ' . $stall->store->user->lastName 
                : null,
            'size' => $stall->size,
            'rentalFee' => $stall->rentalFee,
            'applicationDeadline' => optional($stall->applicationDeadline)->format('Y-m-d'),
            'stallStatus' => $stall->stallStatus,
            'contract' => $contract ? [
                'contractID' => $contract->contractID,
                'user' => $contract->user->firstName . ' ' . $contract->user->lastName,
                'status' => $contract->contractStatus,
                'startDate' => $contract->startDate->format('Y-m-d'),
                'endDate' => optional($contract->endDate)->format('Y-m-d'),
            ] : null,
            'user' => $userData,
            'requirements' => $requirements,
            'lastStatusChange' => optional($stall->lastStatusChange)->format('Y-m-d'),
            'created_at' => optional($stall->created_at)->toDateTimeString(),
        ]);
    }

    public function edit(Stall $stall)
    {
        $stall->load(['marketplace', 'store']);
        return response()->json($stall);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'stallNo' => 'required|string|max:20',
                'marketplaceID' => 'required|exists:marketplaces,marketplaceID',
                'size' => 'nullable|string|max:50',
                'rentalFee' => 'required|numeric|min:0',
                'applicationDeadline' => 'nullable|date|after_or_equal:today',
                'stallStatus' => 'required|in:Vacant,Occupied',
            ]);
            
            // Application deadline is optional when status is Vacant
            // If status is Vacant and no deadline provided, set to null
            if ($validated['stallStatus'] === 'Vacant' && empty($validated['applicationDeadline'])) {
                $validated['applicationDeadline'] = null;
            }

            DB::beginTransaction();

            $stall = Stall::create([
                'stallNo' => strtoupper($validated['stallNo']),
                'marketplaceID' => $validated['marketplaceID'],
                'userID' => null, // Rented By is filled automatically based on tenant assigned
                'size' => $validated['size'] ?? null,
                'rentalFee' => $validated['rentalFee'],
                'applicationDeadline' => $validated['applicationDeadline'] ?? null,
                'stallStatus' => $validated['stallStatus'],
                'lastStatusChange' => now(),
            ]);

            DB::commit();

            try {
                \App\Services\ActivityLogService::logCreate('stalls', $stall->stallID, "Stall created: #{$stall->stallID} ({$stall->stallNo})");
            } catch (\Exception $e) {
                \Log::warning("Failed to log stall create: " . $e->getMessage());
            }

            return response()->json(['success' => true, 'message' => 'Stall added successfully!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Failed to create stall: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create stall: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Stall $stall)
    {
        try {
            $validated = $request->validate([
                'stallNo' => 'nullable|string|max:20',
                'marketplaceID' => 'required|exists:marketplaces,marketplaceID',
                'size' => 'nullable|string|max:50',
                'rentalFee' => 'required|numeric|min:0',
                'applicationDeadline' => 'nullable|date|after_or_equal:today',
                'stallStatus' => 'required|in:Vacant,Occupied',
            ]);
            
            // Application deadline is optional when status is Vacant
            // If status is Vacant and no deadline provided, set to null
            if ($validated['stallStatus'] === 'Vacant' && empty($validated['applicationDeadline'])) {
                $validated['applicationDeadline'] = null;
            }

            DB::beginTransaction();

            // Update lastStatusChange if status changed
            if ($stall->stallStatus !== $validated['stallStatus']) {
                $validated['lastStatusChange'] = now();
            }
            
            // Convert stallNo to uppercase if provided
            if (isset($validated['stallNo'])) {
                $validated['stallNo'] = strtoupper($validated['stallNo']);
            }

            $stall->update($validated);

            DB::commit();

            try {
                \App\Services\ActivityLogService::logUpdate('stalls', $stall->stallID, "Stall #{$stall->stallID} updated.");
            } catch (\Exception $e) {
                \Log::warning("Failed to log stall update: " . $e->getMessage());
            }

            return response()->json(['success' => true, 'message' => 'Stall updated successfully!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Failed to update stall: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stall: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Stall $stall)
    {
        $stall->delete();
        try {
            \App\Services\ActivityLogService::logDelete('stalls', $stall->stallID, "Archived stall #{$stall->stallID}");
        } catch (\Exception $e) {
            \Log::warning("Failed to log stall archive activity: " . $e->getMessage());
        }
        return response()->json(['success' => true, 'message' => 'Stall archived successfully.']);
    }

    public function archiveMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:stalls,stallID'
        ]);

        $count = Stall::whereIn('stallID', $request->ids)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);

        foreach ($request->ids as $id) {
            try {
                \App\Services\ActivityLogService::logDelete('stalls', (int) $id, "Archived stall #{$id}");
            } catch (\Exception $e) {
                \Log::warning("Failed to log stall archive activity: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} stall(s) archived successfully."
        ]);
    }

    public function exportCsv()
    {
        $fileName = 'stalls_' . now()->format('Ymd_His') . '.csv';

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID','Stall Number','Marketplace','Rent By','Contract','Status','Size','Rental Fee','Application Deadline','Last Status Change']);

            Stall::with(['marketplace', 'store', 'contracts.user'])
                ->whereNull('deleted_at')
                ->orderBy('stallID')
                ->chunk(200, function ($stalls) use ($handle) {
                    foreach ($stalls as $stall) {
                        $rentBy = '-';
                        $contract = $stall->contracts()->where('contractStatus', 'Active')->first();
                        if ($contract && $contract->user) {
                            $user = $contract->user;
                            $rentBy = trim(($user->firstName ?? '') . ' ' . ($user->middleName ?? '') . ' ' . ($user->lastName ?? ''));
                        }

                        fputcsv($handle, [
                            $stall->formatted_stall_id,
                            strtoupper($stall->stallNo),
                            $stall->marketplace ? $stall->marketplace->marketplace : '-',
                            $rentBy,
                            $contract ? $contract->contractStatus : '-',
                            $stall->stallStatus,
                            $stall->size ?? '-',
                            number_format($stall->rentalFee, 2),
                            optional($stall->applicationDeadline)->format('Y-m-d'),
                            optional($stall->lastStatusChange)->format('Y-m-d'),
                        ]);
                    }
                });

            fclose($handle);
        });

        $response->headers->set('Content-Type','text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition','attachment; filename="'.$fileName.'"');

        return $response;
    }

    public function print()
    {
        $stalls = Stall::with(['marketplace', 'store', 'contracts.user'])
            ->whereNull('deleted_at')
            ->orderBy('stallID')
            ->get();
        return view('admins.stalls.print', compact('stalls'));
    }

    // ============================================
    // Requirements Management
    // ============================================

    public function requirementsIndex()
    {
        $requirements = Requirement::whereNull('deleted_at')
            ->orderBy('document_type')
            ->orderBy('sort_order')
            ->orderBy('requirement_name')
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'requirement_name' => $req->requirement_name,
                    'document_type' => $req->document_type,
                    'description' => $req->description,
                    'is_active' => $req->is_active,
                    'sort_order' => $req->sort_order,
                ];
            });

        return response()->json(['requirements' => $requirements]);
    }

    public function requirementsShow($id)
    {
        $requirement = Requirement::whereNull('deleted_at')->findOrFail($id);
        
        return response()->json([
            'requirement' => [
                'id' => $requirement->id,
                'requirement_name' => $requirement->requirement_name,
                'document_type' => $requirement->document_type,
                'description' => $requirement->description,
                'is_active' => $requirement->is_active,
                'sort_order' => $requirement->sort_order,
            ]
        ]);
    }

    public function requirementsStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'requirement_name' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($request) {
                        $documentType = $request->input('document_type');
                        $exists = Requirement::where('document_type', $documentType)
                            ->where('requirement_name', $value)
                            ->whereNull('deleted_at')
                            ->exists();
                        
                        if ($exists) {
                            $fail("The requirement name '{$value}' already exists for {$documentType} requirements.");
                        }
                    },
                ],
                'document_type' => 'required|in:Proposal,Tenancy',
                'description' => 'nullable|string',
                'is_active' => 'sometimes|boolean',
            ]);

            $maxSortOrder = Requirement::where('document_type', $validated['document_type'])->max('sort_order') ?? 0;
            
            $requirement = Requirement::create([
                'requirement_name' => $validated['requirement_name'],
                'document_type' => $validated['document_type'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => $maxSortOrder + 1,
            ]);

            try {
                \App\Services\ActivityLogService::logCreate('requirements', $requirement->id, "Requirement created: {$requirement->requirement_name} ({$requirement->document_type})");
            } catch (\Exception $e) {
                \Log::warning("Failed to log requirement create: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Requirement added successfully.',
                'requirement' => $requirement
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error("Failed to create requirement: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create requirement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function requirementsUpdate(Request $request, $id)
    {
        try {
            $requirement = Requirement::whereNull('deleted_at')->findOrFail($id);

            // For checkbox updates, we might only be updating is_active
            // So we need to get the existing values if not provided
            $existingRequirement = Requirement::whereNull('deleted_at')->findOrFail($id);
            
            $validated = $request->validate([
                'requirement_name' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($request, $id) {
                        $documentType = $request->input('document_type');
                        if ($documentType && $value) {
                            $exists = Requirement::where('document_type', $documentType)
                                ->where('requirement_name', $value)
                                ->where('id', '!=', $id)
                                ->whereNull('deleted_at')
                                ->exists();
                            
                            if ($exists) {
                                $fail("The requirement name '{$value}' already exists for {$documentType} requirements.");
                            }
                        }
                    },
                ],
                'document_type' => 'sometimes|required|in:Proposal,Tenancy',
                'description' => 'nullable|string',
                'is_active' => 'sometimes|in:0,1,true,false',
            ]);

            $updateData = [];
            
            // Only update fields that are provided
            if (isset($validated['requirement_name'])) {
                $updateData['requirement_name'] = $validated['requirement_name'];
            }
            if (isset($validated['document_type'])) {
                $updateData['document_type'] = $validated['document_type'];
            }
            if (isset($validated['description'])) {
                $updateData['description'] = $validated['description'];
            }
            if (isset($validated['is_active'])) {
                // Convert to boolean: handle '0', '1', 'true', 'false', true, false
                $isActiveValue = $validated['is_active'];
                if (is_string($isActiveValue)) {
                    $updateData['is_active'] = in_array(strtolower($isActiveValue), ['1', 'true'], true);
                } else {
                    $updateData['is_active'] = (bool)$isActiveValue;
                }
            } else if ($request->has('is_active')) {
                // Handle case where is_active is sent but not in validated (e.g., '0' or '1')
                $isActiveValue = $request->input('is_active');
                if (is_string($isActiveValue)) {
                    $updateData['is_active'] = in_array(strtolower($isActiveValue), ['1', 'true'], true);
                } else {
                    $updateData['is_active'] = (bool)$isActiveValue;
                }
            }
            
            // If no data provided, use existing values
            if (empty($updateData)) {
                $updateData = [
                    'requirement_name' => $existingRequirement->requirement_name,
                    'document_type' => $existingRequirement->document_type,
                    'description' => $existingRequirement->description,
                    'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : $existingRequirement->is_active,
                ];
            }
            
            $requirement->update($updateData);

            try {
                \App\Services\ActivityLogService::logUpdate('requirements', $requirement->id, "Requirement #{$requirement->id} updated: {$requirement->requirement_name}");
            } catch (\Exception $e) {
                \Log::warning("Failed to log requirement update: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Requirement updated successfully.',
                'requirement' => $requirement
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error("Failed to update requirement: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update requirement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function requirementsDestroy($id)
    {
        try {
            $requirement = Requirement::whereNull('deleted_at')->findOrFail($id);
            $name = $requirement->requirement_name;
            $reqId = $requirement->id;
            $requirement->delete();

            try {
                \App\Services\ActivityLogService::logDelete('requirements', $reqId, "Requirement archived: {$name}");
            } catch (\Exception $e) {
                \Log::warning("Failed to log requirement delete: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Requirement deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to delete requirement: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete requirement: ' . $e->getMessage()
            ], 500);
        }
    }
}

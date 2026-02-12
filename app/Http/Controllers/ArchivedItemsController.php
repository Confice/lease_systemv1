<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stall;
use App\Models\Feedback;
use App\Models\Contract;
use App\Models\Bill;
use App\Models\Application;
use App\Models\Document;
use App\Models\Store;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArchivedItemsController extends Controller
{
    public function index()
    {
        return view('admins.archived_items.index');
    }

    public function data()
    {
        $archivedItems = $this->getArchivedItems();

        return response()->json(['data' => $archivedItems]);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.type' => 'required|string|in:user,stall,feedback,contract,bill',
        ]);

        $restoredCount = 0;
        $errors = [];

        foreach ($request->items as $item) {
            try {
                $entityId = (int) $item['id'];

                if ($item['type'] === 'user') {
                    $user = User::onlyTrashed()->find($item['id']);
                    if ($user) {
                        $user->restore();
                        $restoredCount++;
                        try {
                            ActivityLogService::logUpdate('users', $entityId, "User #{$entityId} restored from archive.");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log restore: " . $e->getMessage());
                        }
                    }
                } elseif ($item['type'] === 'stall') {
                    $stall = Stall::onlyTrashed()->find($item['id']);
                    if ($stall) {
                        $stall->restore();
                        $restoredCount++;
                        try {
                            ActivityLogService::logUpdate('stalls', $entityId, "Stall #{$entityId} restored from archive.");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log restore: " . $e->getMessage());
                        }
                    }
                } elseif ($item['type'] === 'feedback') {
                    $feedback = Feedback::find($item['id']);
                    if ($feedback && $feedback->archived_at) {
                        $feedback->archived_at = null;
                        $feedback->save();
                        $restoredCount++;
                        try {
                            ActivityLogService::logUpdate('feedbacks', $entityId, "Feedback #{$entityId} restored from archive.");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log restore: " . $e->getMessage());
                        }
                    }
                } elseif ($item['type'] === 'contract') {
                    $contract = Contract::onlyTrashed()->find($item['id']);
                    if ($contract) {
                        $contract->restore();
                        $restoredCount++;
                        try {
                            ActivityLogService::logUpdate('contracts', $entityId, "Contract #{$entityId} restored from archive.");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log restore: " . $e->getMessage());
                        }
                    }
                } elseif ($item['type'] === 'bill') {
                    $bill = Bill::onlyTrashed()->find($item['id']);
                    if ($bill) {
                        $bill->restore();
                        $restoredCount++;
                        try {
                            ActivityLogService::logUpdate('bills', $entityId, "Bill #{$entityId} restored from archive.");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log restore: " . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to restore {$item['type']} ID {$item['id']}: " . $e->getMessage();
            }
        }

        if ($restoredCount > 0) {
            return response()->json([
                'success' => true,
                'message' => "{$restoredCount} item(s) restored successfully."
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No items were restored. Please check if the items exist.'
            ], 422);
        }
    }

    /**
     * Permanently delete archived items (no undo).
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.type' => 'required|string|in:user,stall,feedback,contract,bill',
        ]);

        $deletedCount = 0;
        $errors = [];

        foreach ($request->items as $item) {
            try {
                $id = is_numeric($item['id']) ? (int) $item['id'] : $item['id'];
                if ($item['type'] === 'user') {
                    $user = User::onlyTrashed()->find($id);
                    if ($user) {
                        $this->permanentlyDeleteUser($user);
                        $deletedCount++;
                        try {
                            ActivityLogService::logDelete('users', $id, "User #{$id} permanently deleted from archive.");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log permanent delete: " . $e->getMessage());
                        }
                    }
                } elseif ($item['type'] === 'stall') {
                    $stall = Stall::onlyTrashed()->find($id);
                    if ($stall) {
                        $this->permanentlyDeleteStall($stall);
                        $deletedCount++;
                        try {
                            ActivityLogService::logDelete('stalls', $id, "Stall #{$id} permanently deleted from archive.");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log permanent delete: " . $e->getMessage());
                        }
                    }
                } elseif ($item['type'] === 'feedback') {
                    $feedback = Feedback::find($id);
                    if ($feedback && $feedback->archived_at) {
                        $feedback->delete();
                        $deletedCount++;
                        try {
                            ActivityLogService::logDelete('feedbacks', $id, "Feedback #{$id} permanently deleted from archive.");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log permanent delete: " . $e->getMessage());
                        }
                    }
                } elseif ($item['type'] === 'contract') {
                    $contract = Contract::onlyTrashed()->find($id);
                    if ($contract) {
                        $this->permanentlyDeleteContract($contract);
                        $deletedCount++;
                        try {
                            ActivityLogService::logDelete('contracts', $id, "Contract #{$id} permanently deleted from archive.");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log permanent delete: " . $e->getMessage());
                        }
                    }
                } elseif ($item['type'] === 'bill') {
                    $bill = Bill::onlyTrashed()->find($id);
                    if ($bill) {
                        $bill->forceDelete();
                        $deletedCount++;
                        try {
                            ActivityLogService::logDelete('bills', $id, "Bill #{$id} permanently deleted from archive.");
                        } catch (\Exception $e) {
                            \Log::warning("Failed to log permanent delete: " . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to delete {$item['type']} ID {$item['id']}: " . $e->getMessage();
            }
        }

        if ($deletedCount > 0) {
            $payload = [
                'success' => true,
                'message' => "{$deletedCount} item(s) permanently deleted.",
            ];
            if (!empty($errors)) {
                $payload['errors'] = $errors;
            }
            return response()->json($payload);
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Could not delete some or all items.',
                'errors'  => $errors,
            ], 422);
        }

        // No items were found to delete (already removed or never existed). Return success
        // so the frontend can refresh the list and remove those rows from the UI.
        return response()->json([
            'success' => true,
            'message' => 'Those items are no longer in the archive and have been removed from the list.',
        ]);
    }

    /**
     * Permanently delete a contract and all dependents (bills, feedback, docs; clear FKs on applications and contracts).
     */
    private function permanentlyDeleteContract(Contract $contract): void
    {
        $contractId = $contract->contractID;
        Contract::where('renewedFrom', $contractId)->update(['renewedFrom' => null]);
        Application::withTrashed()->where('contractID', $contractId)->update(['contractID' => null]);
        Bill::withTrashed()->where('contractID', $contractId)->forceDelete();
        Feedback::where('contractID', $contractId)->delete();
        Document::withTrashed()->where('contractID', $contractId)->forceDelete();
        $contract->forceDelete();
    }

    /**
     * Permanently delete a user and all related data (contracts, bills, feedback, docs, applications, stores, activity logs).
     */
    private function permanentlyDeleteUser(User $user): void
    {
        $userId = $user->id;
        $contracts = Contract::withTrashed()->where('userID', $userId)->get();
        foreach ($contracts as $contract) {
            $stall = Stall::withTrashed()->find($contract->stallID);
            if ($stall) {
                $stall->update(['stallStatus' => 'Vacant', 'storeID' => null]);
            }
            $this->permanentlyDeleteContract($contract);
        }
        Document::withTrashed()->where('userID', $userId)->forceDelete();
        Application::withTrashed()->where('userID', $userId)->forceDelete();
        $storeIds = Store::where('userID', $userId)->pluck('storeID');
        if ($storeIds->isNotEmpty()) {
            Stall::withTrashed()->whereIn('storeID', $storeIds)->update(['storeID' => null, 'stallStatus' => 'Vacant']);
        }
        Store::where('userID', $userId)->delete();
        ActivityLog::where('userID', $userId)->delete();
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        $user->forceDelete();
    }

    /**
     * Permanently delete a stall after removing any contracts (and their dependents) that reference it.
     */
    private function permanentlyDeleteStall(Stall $stall): void
    {
        $stallId = $stall->stallID;
        $contracts = Contract::withTrashed()->where('stallID', $stallId)->get();
        foreach ($contracts as $contract) {
            $this->permanentlyDeleteContract($contract);
        }
        $stall->forceDelete();
    }

    /**
     * Permanently delete ALL archived items (no undo).
     */
    public function deleteAll(Request $request)
    {
        $archivedItems = $this->getArchivedItems();
        if ($archivedItems->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No archived items to delete.',
            ]);
        }

        $items = $archivedItems->map(fn ($row) => [
            'id'   => $row['id'],
            'type' => $row['module_type'],
        ])->values()->toArray();

        return $this->destroy(new Request(['items' => $items]));
    }

    public function exportCsv()
    {
        $fileName = 'archived_items_' . now()->format('Ymd_His') . '.csv';
        $archivedItems = $this->getArchivedItems();

        $response = new StreamedResponse(function () use ($archivedItems) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Reference ID', 'Archived At', 'Archived From', 'Archived By', 'Action']);

            foreach ($archivedItems as $item) {
                fputcsv($handle, [
                    $item['reference_id'],
                    $item['archived_at'],
                    $item['archived_from'],
                    $item['archived_by'] ?? '—',
                    $item['action'] ?? 'Archived',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');

        return $response;
    }

    /**
     * Print archived items (for PDF)
     */
    public function print()
    {
        $archivedItems = $this->getArchivedItems();
        return view('admins.archived_items.print', compact('archivedItems'));
    }

    private function getArchivedItems()
    {
        $archivedItems = collect();

        // Get archived users
        $archivedUsers = User::onlyTrashed()->get();
        foreach ($archivedUsers as $user) {
            $archivedAt = null;
            if ($user->deleted_at) {
                $archivedAt = Carbon::parse($user->deleted_at)->setTimezone('Asia/Manila')->toIso8601String();
            }

            $logMeta = $this->getArchiveLogMeta('users', $user->id);
            $archivedItems->push([
                'id' => $user->id,
                'reference_id' => 'USER-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'archived_at' => $archivedAt,
                'archived_from' => 'Users',
                'module_type' => 'user',
                'original_id' => $user->id,
                'archived_by' => $logMeta['archived_by'],
                'action' => $logMeta['action'],
            ]);
        }

        // Get archived stalls
        $archivedStalls = Stall::onlyTrashed()->with('marketplace')->get();
        foreach ($archivedStalls as $stall) {
            $marketplaceName = $stall->marketplace ? $stall->marketplace->marketplace : '';
            $marketplaceUpper = strtoupper($marketplaceName);

            $prefix = '';
            if (str_contains($marketplaceUpper, 'THE HUB') || str_contains($marketplaceUpper, 'HUB BY D & G')) {
                $prefix = 'HUB-';
            } elseif (str_contains($marketplaceUpper, 'ONE-STOP BAZAAR') || str_contains($marketplaceUpper, 'YOUR ONE-STOP')) {
                $prefix = 'BAZ-';
            } else {
                $prefix = strtoupper(substr($marketplaceName, 0, 3)) . '-';
            }

            $formattedId = $prefix . str_pad($stall->stallID, 4, '0', STR_PAD_LEFT);

            $archivedAt = null;
            if ($stall->deleted_at) {
                $archivedAt = Carbon::parse($stall->deleted_at)->setTimezone('Asia/Manila')->toIso8601String();
            }

            $logMeta = $this->getArchiveLogMeta('stalls', $stall->stallID);
            $archivedItems->push([
                'id' => $stall->stallID,
                'reference_id' => $formattedId,
                'archived_at' => $archivedAt,
                'archived_from' => 'Stalls',
                'module_type' => 'stall',
                'original_id' => $stall->stallID,
                'archived_by' => $logMeta['archived_by'],
                'action' => $logMeta['action'],
            ]);
        }

        // Get archived feedback entries
        $archivedFeedback = Feedback::whereNotNull('archived_at')->with('tenant')->get();
        foreach ($archivedFeedback as $feedback) {
            $logMeta = $this->getArchiveLogMeta('feedbacks', $feedback->feedbackID);
            $archivedItems->push([
                'id' => $feedback->feedbackID,
                'reference_id' => 'FDBK-' . str_pad($feedback->feedbackID, 4, '0', STR_PAD_LEFT),
                'archived_at' => Carbon::parse($feedback->archived_at)->setTimezone('Asia/Manila')->toIso8601String(),
                'archived_from' => 'Tenant Feedback',
                'module_type' => 'feedback',
                'original_id' => $feedback->feedbackID,
                'archived_by' => $logMeta['archived_by'],
                'action' => $logMeta['action'],
            ]);
        }

        // Get archived contracts
        $archivedContracts = Contract::onlyTrashed()->get();
        foreach ($archivedContracts as $contract) {
            $archivedAt = $contract->deleted_at
                ? Carbon::parse($contract->deleted_at)->setTimezone('Asia/Manila')->toIso8601String()
                : null;

            $logMeta = $this->getArchiveLogMeta('contracts', $contract->contractID);
            $archivedItems->push([
                'id' => $contract->contractID,
                'reference_id' => 'CONTRACT-' . str_pad($contract->contractID, 4, '0', STR_PAD_LEFT),
                'archived_at' => $archivedAt,
                'archived_from' => 'Leases',
                'module_type' => 'contract',
                'original_id' => $contract->contractID,
                'archived_by' => $logMeta['archived_by'],
                'action' => $logMeta['action'],
            ]);
        }

        // Get archived bills
        $archivedBills = Bill::onlyTrashed()->get();
        foreach ($archivedBills as $bill) {
            $archivedAt = $bill->deleted_at
                ? Carbon::parse($bill->deleted_at)->setTimezone('Asia/Manila')->toIso8601String()
                : null;

            $logMeta = $this->getArchiveLogMeta('bills', $bill->billID);
            $archivedItems->push([
                'id' => $bill->billID,
                'reference_id' => 'BILL-' . str_pad($bill->billID, 4, '0', STR_PAD_LEFT),
                'archived_at' => $archivedAt,
                'archived_from' => 'Bills',
                'module_type' => 'bill',
                'original_id' => $bill->billID,
                'archived_by' => $logMeta['archived_by'],
                'action' => $logMeta['action'],
            ]);
        }

        return $archivedItems->sortByDesc('archived_at')->values();
    }

    /**
     * Purge archived items older than the given number of years (data retention).
     * Called by the archive:purge-old command.
     */
    public function purgeOldArchivedItems(int $years = 5): array
    {
        $cutoff = now()->subYears($years);
        $counts = ['users' => 0, 'stalls' => 0, 'feedback' => 0, 'contracts' => 0, 'bills' => 0];

        // Delete in order: bills, feedback, contracts (handles FKs), then users, stalls
        $oldBills = Bill::onlyTrashed()->where('deleted_at', '<', $cutoff)->get();
        foreach ($oldBills as $bill) {
            $bill->forceDelete();
            $counts['bills']++;
        }

        $oldFeedback = Feedback::whereNotNull('archived_at')->where('archived_at', '<', $cutoff)->get();
        foreach ($oldFeedback as $feedback) {
            $feedback->delete();
            $counts['feedback']++;
        }

        $oldContracts = Contract::onlyTrashed()->where('deleted_at', '<', $cutoff)->get();
        foreach ($oldContracts as $contract) {
            $this->permanentlyDeleteContract($contract);
            $counts['contracts']++;
        }

        $oldUsers = User::onlyTrashed()->where('deleted_at', '<', $cutoff)->get();
        foreach ($oldUsers as $user) {
            $this->permanentlyDeleteUser($user);
            $counts['users']++;
        }

        $oldStalls = Stall::onlyTrashed()->where('deleted_at', '<', $cutoff)->get();
        foreach ($oldStalls as $stall) {
            $this->permanentlyDeleteStall($stall);
            $counts['stalls']++;
        }

        return $counts;
    }

    /**
     * Get "archived by" user name and action description from activity log for an archived entity.
     * Archive actions are logged as actionType 'Delete' with description like "Archived ...".
     * Uses User::withTrashed() so we show the archiver even if they were later soft-deleted.
     */
    private function getArchiveLogMeta(string $entity, int $entityId): array
    {
        // Find log: prefer "Archived..." description, else any Delete for this entity (so we catch all archive actions)
        $log = ActivityLog::where('entity', $entity)
            ->where('entityID', (int) $entityId)
            ->where(function ($q) {
                $q->where('description', 'like', 'Archived%')
                  ->orWhere('actionType', 'Delete');
            })
            ->orderByDesc('created_at')
            ->first();

        if (!$log) {
            return ['archived_by' => '—', 'action' => 'Archived'];
        }

        $action = $log->description ?? 'Archived';

        if (!$log->userID) {
            return ['archived_by' => '—', 'action' => $action];
        }

        $archiver = User::withTrashed()->find($log->userID);
        if (!$archiver) {
            return ['archived_by' => 'User #' . $log->userID, 'action' => $action];
        }

        $name = trim(($archiver->firstName ?? '') . ' ' . ($archiver->lastName ?? ''));
        if ($name === '') {
            $name = $archiver->email ?? ('User #' . $log->userID);
        }
        if (($archiver->role ?? '') !== '') {
            $name = $name . ' (' . $archiver->role . ')';
        }

        return [
            'archived_by' => $name,
            'action' => $action,
        ];
    }
}


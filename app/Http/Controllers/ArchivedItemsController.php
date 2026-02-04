<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stall;
use App\Models\Feedback;
use App\Models\Contract;
use App\Models\Bill;
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
                        $user->forceDelete();
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
                        $stall->forceDelete();
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
                        $contract->forceDelete();
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
            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} item(s) permanently deleted.",
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No items were deleted. They may no longer exist or were already removed.',
        ], 422);
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
     * Get "archived by" user name and action description from activity log for an archived entity.
     * Uses User::withTrashed() so we show the archiver even if that user was later soft-deleted.
     */
    private function getArchiveLogMeta(string $entity, int $entityId): array
    {
        $log = ActivityLog::where('entity', $entity)
            ->where('entityID', (int) $entityId)
            ->where('description', 'like', 'Archived%')
            ->orderByDesc('created_at')
            ->first();

        if (!$log || !$log->userID) {
            return ['archived_by' => '—', 'action' => 'Archived'];
        }

        // Load archiver with withTrashed() so we show name even if they were later deleted
        $archiver = User::withTrashed()->find($log->userID);
        if (!$archiver) {
            return [
                'archived_by' => '—',
                'action' => $log->description ?? 'Archived',
            ];
        }

        $name = trim(($archiver->firstName ?? '') . ' ' . ($archiver->lastName ?? ''));
        if ($name === '') {
            $name = $archiver->email ?? '—';
        }

        return [
            'archived_by' => $name,
            'action' => $log->description ?? 'Archived',
        ];
    }
}


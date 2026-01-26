<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stall;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArchivedItemsController extends Controller
{
    public function index()
    {
        return view('admins.archived_items.index');
    }

    public function data()
    {
        $archivedItems = collect();

        // Get archived users
        $archivedUsers = User::onlyTrashed()->get();
        foreach ($archivedUsers as $user) {
            $archivedAt = null;
            if ($user->deleted_at) {
                $archivedAt = Carbon::parse($user->deleted_at)->setTimezone('Asia/Manila')->toIso8601String();
            }
            
            $archivedItems->push([
                'id' => $user->id,
                'reference_id' => 'USER-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'archived_at' => $archivedAt,
                'archived_from' => 'Users',
                'module_type' => 'user',
                'original_id' => $user->id,
            ]);
        }

        // Get archived stalls
        $archivedStalls = Stall::onlyTrashed()->with('marketplace')->get();
        foreach ($archivedStalls as $stall) {
            // Use the formatted stall ID logic
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
            
            $archivedItems->push([
                'id' => $stall->stallID,
                'reference_id' => $formattedId,
                'archived_at' => $archivedAt,
                'archived_from' => 'Stalls',
                'module_type' => 'stall',
                'original_id' => $stall->stallID,
            ]);
        }

        // Get archived feedback entries
        $archivedFeedback = Feedback::whereNotNull('archived_at')->with('tenant')->get();
        foreach ($archivedFeedback as $feedback) {
            $archivedItems->push([
                'id' => $feedback->feedbackID,
                'reference_id' => 'FDBK-' . str_pad($feedback->feedbackID, 4, '0', STR_PAD_LEFT),
                'archived_at' => Carbon::parse($feedback->archived_at)->setTimezone('Asia/Manila')->toIso8601String(),
                'archived_from' => 'Tenant Feedback',
                'module_type' => 'feedback',
                'original_id' => $feedback->feedbackID,
            ]);
        }

        // Sort by archived_at descending (most recent first)
        $archivedItems = $archivedItems->sortByDesc('archived_at')->values();

        return response()->json(['data' => $archivedItems]);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.type' => 'required|string|in:user,stall,feedback',
        ]);

        $restoredCount = 0;
        $errors = [];

        foreach ($request->items as $item) {
            try {
                if ($item['type'] === 'user') {
                    $user = User::onlyTrashed()->find($item['id']);
                    if ($user) {
                        $user->restore();
                        $restoredCount++;
                    }
                } elseif ($item['type'] === 'stall') {
                    $stall = Stall::onlyTrashed()->find($item['id']);
                    if ($stall) {
                        $stall->restore();
                        $restoredCount++;
                    }
                } elseif ($item['type'] === 'feedback') {
                    $feedback = Feedback::find($item['id']);
                    if ($feedback && $feedback->archived_at) {
                        $feedback->archived_at = null;
                        $feedback->save();
                        $restoredCount++;
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
}


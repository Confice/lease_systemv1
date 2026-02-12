<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    /**
     * Display the activity logs page
     */
    public function index()
    {
        return view('admins.activity_logs.index');
    }

    /**
     * Get activity logs data for DataTable (AJAX)
     */
    public function data(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('actionType', 'like', "%{$search}%")
                  ->orWhere('entity', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('firstName', 'like', "%{$search}%")
                                ->orWhere('lastName', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by action type
        if ($request->has('actionType') && $request->actionType) {
            $query->where('actionType', $request->actionType);
        }

        // Filter by entity
        if ($request->has('entity') && $request->entity) {
            $query->where('entity', $request->entity);
        }

        // Filter by user
        if ($request->has('userID') && $request->userID) {
            $query->where('userID', $request->userID);
        }

        // Date range filter
        if ($request->has('dateFrom') && $request->dateFrom) {
            $query->whereDate('created_at', '>=', $request->dateFrom);
        }
        if ($request->has('dateTo') && $request->dateTo) {
            $query->whereDate('created_at', '<=', $request->dateTo);
        }

        $totalRecords = $query->count();
        
        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $logs = $query->skip($start)->take($length)->get();

        $data = $logs->map(function($log) {
            $user = $log->user;
            $userName = $user ? trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? '')) : 'Unknown User';
            $userEmail = $user ? $user->email : 'N/A';

            return [
                'activityID' => $log->activityID,
                'actionType' => $log->actionType,
                'entity' => ucfirst($log->entity),
                'entityID' => $log->entityID,
                'description' => $log->description ?? '-',
                'user' => $userName,
                'userEmail' => $userEmail,
                'created_at' => $log->created_at->format('M d, Y h:i A'),
                'created_at_raw' => $log->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => ActivityLog::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    /**
     * Export activity logs as CSV
     */
    public function exportCsv()
    {
        $fileName = 'activity_logs_' . now()->format('Ymd_His') . '.csv';
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        $response = new StreamedResponse(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Action Type', 'Entity', 'Entity ID', 'Description', 'User', 'Email', 'Created At']);
            foreach ($logs as $log) {
                $user = $log->user;
                fputcsv($handle, [
                    $log->activityID,
                    $log->actionType,
                    ucfirst($log->entity),
                    $log->entityID,
                    $log->description ?? '-',
                    $user ? trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? '')) : 'Unknown',
                    $user ? $user->email : 'N/A',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($handle);
        });
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        return $response;
    }

    /**
     * Print activity logs (for PDF)
     */
    public function print()
    {
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(500)
            ->get();
        return view('admins.activity_logs.print', compact('logs'));
    }
}


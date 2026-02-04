<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\Bill;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ActivityLogService
{
    /**
     * Exclude these action types from "process" activity (dashboard / notify).
     */
    public static function isSystemProcessLog(ActivityLog $log): bool
    {
        return !in_array($log->actionType, ['Login', 'Logout'], true);
    }

    /**
     * Format a single activity log for display (friendly message + goto URL).
     * Returns null for login/logout or when entity is not one we format.
     * @param string $viewerRole 'Lease Manager' or 'Tenant' – URL built for that role.
     */
    public static function formatForDisplay(ActivityLog $log, string $viewerRole = 'Lease Manager'): ?array
    {
        if (!self::isSystemProcessLog($log)) {
            return null;
        }

        $userName = 'A tenant';
        if ($log->user) {
            $name = trim(($log->user->firstName ?? '') . ' ' . ($log->user->lastName ?? ''));
            $userName = $name ?: ($log->user->email ?? 'A tenant');
        }
        $dateTime = $log->created_at->format('M j, Y g:i A');
        $entity = $log->entity ?? '';
        $entityID = $log->entityID;
        $isAdmin = $viewerRole === 'Lease Manager';

        $message = null;
        $url = null;

        switch ($entity) {
            case 'applications':
                $app = Application::with(['stall.marketplace'])->whereNull('deleted_at')->find($entityID);
                $stallName = $app && $app->stall
                    ? $app->stall->stallNo . ($app->stall->marketplace ? ' (' . $app->stall->marketplace->marketplace . ')' : '')
                    : 'a stall';
                $message = "{$userName} applied to {$stallName} on {$dateTime}";
                if ($isAdmin) {
                    $url = $app && $app->stall ? route('admins.prospective-tenants.applications', $app->stallID) : route('admins.prospective-tenants.index');
                } else {
                    $url = route('tenants.stalls.index');
                }
                break;

            case 'bills':
                $desc = $log->description ?? '';
                if (stripos($desc, 'proof') === false) {
                    return null; // only show payment proof uploads
                }
                $bill = Bill::with(['contract.stall.marketplace'])->whereNull('deleted_at')->find($entityID);
                $stallName = 'a stall';
                if ($bill && $bill->contract && $bill->contract->stall) {
                    $s = $bill->contract->stall;
                    $stallName = $s->stallNo . ($s->marketplace ? ' (' . $s->marketplace->marketplace . ')' : '');
                }
                $message = "{$userName} uploaded bill proof for {$stallName} on {$dateTime}";
                if ($isAdmin) {
                    $url = Route::has('admins.bills.show-update-status') ? route('admins.bills.show-update-status', $entityID) : route('admins.bills.index');
                } else {
                    $url = $entityID && Route::has('tenants.bills.upload') ? route('tenants.bills.upload', $entityID) : route('tenants.bills.index');
                }
                break;

            case 'feedbacks':
                $feedback = Feedback::with(['contract.stall.marketplace'])->find($entityID);
                $stallName = '';
                if ($feedback && $feedback->contract && $feedback->contract->stall) {
                    $s = $feedback->contract->stall;
                    $stallName = ' (' . $s->stallNo . ($s->marketplace ? ' - ' . $s->marketplace->marketplace : '') . ')';
                }
                $message = "{$userName} sent a feedback{$stallName} on {$dateTime}";
                if ($isAdmin) {
                    $url = Route::has('admins.feedback.show') ? route('admins.feedback.show', $entityID) : route('admins.feedback.index');
                } else {
                    $url = route('tenants.feedback.index');
                }
                break;

            default:
                return null;
        }

        return [
            'message' => $message,
            'url' => $url,
            'created_at' => $log->created_at,
        ];
    }
    /**
     * Log an activity
     *
     * @param string $actionType Create, Update, Delete, Login, Logout, View, Other
     * @param string $entity Entity name (e.g., 'users', 'contracts', 'bills')
     * @param int|null $entityID ID of the affected record
     * @param string $description Description of the action
     * @param int|null $userID User ID (defaults to authenticated user)
     * @return ActivityLog
     */
    public static function log(
        string $actionType,
        string $entity,
        ?int $entityID = null,
        string $description = '',
        ?int $userID = null
    ): ActivityLog {
        return ActivityLog::create([
            'actionType' => $actionType,
            'entity' => $entity,
            'entityID' => $entityID,
            'description' => $description,
            'userID' => $userID ?? Auth::id(),
        ]);
    }

    /**
     * Log a create action
     */
    public static function logCreate(string $entity, ?int $entityID, string $description = ''): ActivityLog
    {
        return self::log('Create', $entity, $entityID, $description ?: "Created {$entity} #{$entityID}");
    }

    /**
     * Log an update action
     */
    public static function logUpdate(string $entity, ?int $entityID, string $description = ''): ActivityLog
    {
        return self::log('Update', $entity, $entityID, $description ?: "Updated {$entity} #{$entityID}");
    }

    /**
     * Log a delete action
     */
    public static function logDelete(string $entity, ?int $entityID, string $description = ''): ActivityLog
    {
        return self::log('Delete', $entity, $entityID, $description ?: "Deleted {$entity} #{$entityID}");
    }

    /**
     * Log a view action
     */
    public static function logView(string $entity, ?int $entityID, string $description = ''): ActivityLog
    {
        return self::log('View', $entity, $entityID, $description ?: "Viewed {$entity} #{$entityID}");
    }

    /**
     * Log a login action
     */
    public static function logLogin(?int $userID = null): ActivityLog
    {
        return self::log('Login', 'users', $userID, 'User logged in', $userID);
    }

    /**
     * Log a logout action
     */
    public static function logLogout(?int $userID = null): ActivityLog
    {
        return self::log('Logout', 'users', $userID, 'User logged out', $userID);
    }
}


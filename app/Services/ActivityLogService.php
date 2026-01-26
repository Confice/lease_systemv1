<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
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


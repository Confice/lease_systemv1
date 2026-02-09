<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Application;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL older than 5.7.7 or MariaDB < 10.2.2
        Schema::defaultStringLength(191);

        // Share recent activity for notification dropdown (formatted, no login/logout)
        View::composer(['layouts.admin_app', 'layouts.tenant_app'], function ($view) {
            $recentActivityFormatted = [];
            if (Auth::check()) {
                $user = Auth::user();
                $viewerRole = $user->role === 'Lease Manager' ? 'Lease Manager' : 'Tenant';
                if ($user->role === 'Lease Manager') {
                    $logs = ActivityLog::with('user')
                        ->whereHas('user', fn ($q) => $q->where('role', 'Tenant'))
                        ->orderByDesc('created_at')
                        ->limit(20)
                        ->get();
                } else {
                    // Tenant: own activity + lease manager actions on this tenant's applications
                    $tenantApplicationIds = Application::where('userID', $user->id)->pluck('applicationID')->toArray();
                    $logs = ActivityLog::with('user')
                        ->where(function ($q) use ($user, $tenantApplicationIds) {
                            $q->where('userID', $user->id);
                            if (count($tenantApplicationIds) > 0) {
                                $q->orWhere(function ($q2) use ($tenantApplicationIds) {
                                    $q2->where('entity', 'applications')
                                        ->whereIn('entityID', $tenantApplicationIds);
                                });
                            }
                        })
                        ->orderByDesc('created_at')
                        ->limit(25)
                        ->get();
                }
                foreach ($logs as $log) {
                    $item = $viewerRole === 'Tenant'
                        ? ActivityLogService::formatForDisplay($log, $viewerRole, (int) $user->id)
                        : ActivityLogService::formatForDisplay($log, $viewerRole);
                    if ($item !== null) {
                        $recentActivityFormatted[] = $item;
                        if (count($recentActivityFormatted) >= 10) {
                            break;
                        }
                    }
                }
            }
            $view->with('recentActivityFormatted', $recentActivityFormatted);
        });
    }
}

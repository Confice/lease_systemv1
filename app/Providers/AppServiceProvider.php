<?php

namespace App\Providers;

use App\Models\ActivityLog;
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
                $query = $user->role === 'Lease Manager'
                    ? ActivityLog::with('user')->whereHas('user', fn ($q) => $q->where('role', 'Tenant'))
                    : ActivityLog::with('user')->where('userID', $user->id);
                $logs = $query->orderByDesc('created_at')->limit(20)->get();
                foreach ($logs as $log) {
                    $item = ActivityLogService::formatForDisplay($log, $viewerRole);
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

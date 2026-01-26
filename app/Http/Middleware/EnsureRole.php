<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized. Please log in.');
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            abort(403, 'Unauthorized. Insufficient permissions.');
        }

        return $next($request);
    }
}

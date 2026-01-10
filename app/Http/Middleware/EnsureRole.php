<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Usage: ->middleware('role:super_admin,admin')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            $msg = "EnsureRole: Access denied. No authenticated user for URL: " . $request->fullUrl();
            \Illuminate\Support\Facades\Log::warning($msg);
            error_log($msg); // Direct to docker logs
            abort(401);
        }

        if (empty($roles)) {
            return $next($request);
        }

        // Flatten roles in case they are passed as a single comma-separated string: 'role:admin,super_admin'
        $normalizedRoles = [];
        foreach ($roles as $role) {
            if (str_contains($role, ',')) {
                $normalizedRoles = array_merge($normalizedRoles, explode(',', $role));
            } else {
                $normalizedRoles[] = $role;
            }
        }
        $normalizedRoles = array_map('trim', $normalizedRoles);

        $userRole = $user->role instanceof \UnitEnum ? $user->role->value : $user->role;

        if (! in_array($userRole, $normalizedRoles)) {
            $msg = "EnsureRole: Access forbidden for user {$user->id} ({$userRole}) on URL: " . $request->fullUrl() . ". Required roles: " . implode(', ', $normalizedRoles);
            \Illuminate\Support\Facades\Log::warning($msg);
            error_log($msg); // Direct to docker logs
            abort(403);
        }

        return $next($request);
    }
}



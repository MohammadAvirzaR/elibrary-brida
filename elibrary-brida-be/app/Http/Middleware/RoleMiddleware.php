<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class RoleMiddleware
{
    /**
     * Handle the incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Jika user tidak terautentikasi
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Load role relationship if not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // Jika user tidak punya role
        if (!$user->role) {
            return response()->json(['message' => 'User role not found.'], 403);
        }

        // Normalize role names (remove extra spaces, convert to lowercase for comparison)
        $userRole = strtolower(trim($user->role->name));
        $allowedRoles = array_map(function ($role) {
            return strtolower(trim($role));
        }, $roles);

        // Cek apakah user role ada di dalam allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            return response()->json(
                ['message' => 'Forbidden: Access denied. Required roles: ' . implode(', ', $roles)],
                403
            );
        }

        return $next($request);
    }
}

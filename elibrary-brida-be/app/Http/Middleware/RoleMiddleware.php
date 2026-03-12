<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class RoleMiddleware
{
    /**
     * Handle the incoming request.
     * Menggunakan Spatie HasRoles untuk pengecekan role.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Normalize role names
        $allowedRoles = array_map(fn($r) => strtolower(trim($r)), $roles);

        // Cek apakah user memiliki salah satu dari role yang diizinkan (Spatie)
        $userHasRole = false;
        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                $userHasRole = true;
                break;
            }
        }

        if (!$userHasRole) {
            return response()->json(
                ['message' => 'Forbidden: Access denied. Required roles: ' . implode(', ', $roles)],
                403
            );
        }

        return $next($request);
    }
}

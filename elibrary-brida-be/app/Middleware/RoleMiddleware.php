<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Pastikan sudah login
        if (!$user || !$user->role) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Cek apakah role user termasuk role yang diizinkan
        if (!in_array($user->role->name, $roles)) {
            return response()->json(['message' => 'Forbidden: Access denied.'], 403);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        return $next($request);
    }
}

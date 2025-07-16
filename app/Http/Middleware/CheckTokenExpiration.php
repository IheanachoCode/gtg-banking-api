<?php

namespace App\Http\Middleware;

use Closure;

class CheckTokenExpiration
{
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->token()->expires_at < now()) {
            return response()->json(['message' => 'Token expired'], 401);
        }

        return $next($request);
    }
}

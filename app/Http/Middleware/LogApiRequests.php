<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle($request, Closure $next)
    {
        Log::info('API Request', [
            'ip' => $request->ip(),
            'path' => $request->path(),
            'method' => $request->method(),
            'user' => $request->user() ? $request->user()->id : 'guest'
        ]);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiVersion
{
    public function handle(Request $request, Closure $next)
    {
        // Add API version to response headers
        $response = $next($request);
        $response->headers->set('X-API-Version', 'v1');
        
        return $response;
    }
}
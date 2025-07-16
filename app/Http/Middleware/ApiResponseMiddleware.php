<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // If the response is already a JsonResponse, we don't need to modify it
        if ($response instanceof JsonResponse) {
            return $response;
        }

        // If the response is not JSON, convert it to JSON
        if (!$response->headers->has('Content-Type')) {
            $response->header('Content-Type', 'application/json');
        }

        // Ensure response time is optimized
        if (defined('LARAVEL_START')) {
            $response->header('X-Response-Time', (microtime(true) - LARAVEL_START) * 1000);
        }

        return $response;
    }
} 
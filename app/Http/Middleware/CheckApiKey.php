<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('CheckApiKey middleware executed');
        
        $apiKey = $request->header('X-API-Key');
        $configApiKey = config('app.api_key');
        
        Log::info('Received API Key: ' . $apiKey);
        Log::info('Expected API Key: ' . $configApiKey);
        
        if (!$apiKey || $apiKey !== $configApiKey) {
            return response()->json([
                'message' => 'Invalid API key',
                'status' => 'error'
            ], 401);
        }

        return $next($request);
    }
}
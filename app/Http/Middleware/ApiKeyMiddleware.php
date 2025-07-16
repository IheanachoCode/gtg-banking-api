<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('x-api-key');
        
        // Check if API key is provided
        if (!$apiKey) {
            return response()->json([
                'error' => 'API key is required',
                'message' => 'Please provide x-api-key header'
            ], 401);
        }
        
        // Get valid API keys from config
        $validApiKeys = config('app.api_keys', []);
        
        // Check if the provided API key is valid
        if (!in_array($apiKey, $validApiKeys)) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'The provided API key is not valid'
            ], 401);
        }
        
        return $next($request);
    }
}





// public function handle(Request $request, Closure $next): Response
// {
//     $apiKey = $request->header('x-api-key');
    
//     if (!$apiKey) {
//         return response()->json([
//             'error' => 'API key is required',
//             'message' => 'Please provide x-api-key header'
//         ], 401);
//     }
    
//     // Check against database (assuming you have an ApiKey model)
//     $validKey = \App\Models\ApiKey::where('key', $apiKey)
//         ->where('is_active', true)
//         ->first();
    
//     if (!$validKey) {
//         return response()->json([
//             'error' => 'Invalid API key',
//             'message' => 'The provided API key is not valid'
//         ], 401);
//     }
    
//     // Optionally, you can attach the API key info to the request
//     $request->merge(['api_key_info' => $validKey]);
    
//     return $next($request);
// }
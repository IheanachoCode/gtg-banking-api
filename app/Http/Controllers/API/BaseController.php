<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="GTG API Documentation",
 *     description="API Documentation for GTG Application",
 *     @OA\Contact(
 *         email="support@gtg.com",
 *         name="GTG Support"
 *     )
 * )
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="X-API-Key",
 *     name="X-API-Key"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */


class BaseController extends Controller
{
    use ApiResponse;

    /**
     * Return unauthorized response
     * 
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse($message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }

    /**
     * Return validation error response
     * 
     * @param Validator $validator
     * @return JsonResponse
     */
    protected function validationErrorResponse($validator): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }
}
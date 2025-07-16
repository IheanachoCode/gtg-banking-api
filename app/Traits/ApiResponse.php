<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Success Response
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'response_time' => defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START) * 1000, 2) : 0
        ], $code);
    }

    /**
     * Error Response
     *
     * @param string $message
     * @param int $code
     * @param mixed $data
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Error', int $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'response_time' => defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START) * 1000, 2) : 0
        ], $code);
    }

    /**
     * Validation Error Response
     *
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function validationErrorResponse($errors): JsonResponse
    {
        return $this->errorResponse('Validation failed', 422, $errors);
    }
} 
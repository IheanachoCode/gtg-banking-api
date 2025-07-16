<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\MeterType;
use App\Http\Resources\MeterTypeResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;


class MeterTypeController extends BaseController
{
    /**
     * Get all meter types
     * 
     * Retrieve a list of all available meter types in the system.
     * 
     * @header x-api-key string required API key for authentication
     * @response 200 {
     *   "status": true,
     *   "message": "Request successfully completed",
     *   "data": {
     *     "meter_types": [
     *       {
     *         "id": 1,
     *         "name": "Prepaid Meter",
     *         "code": "PRE",
     *         "description": "Prepaid electricity meter",
     *         "is_active": true,
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       },
     *       {
     *         "id": 2,
     *         "name": "Postpaid Meter",
     *         "code": "POST",
     *         "description": "Postpaid electricity meter",
     *         "is_active": true,
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "An error occurred while fetching meter types",
     *   "data": null
     * }
     *
     * @security ApiKeyAuth
     */
    public function getAllMeterTypes(): JsonResponse
    {
        try {
            $meterTypes = MeterType::all();

            Log::info('Meter types fetched', [
                'count' => $meterTypes->count()
            ]);

            return $this->successResponse([
                'meter_types' => MeterTypeResource::collection($meterTypes)
            ], 'Request successfully completed');

        } catch (\Exception $e) {
            Log::error('Error fetching meter types', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'An error occurred while fetching meter types', 
                500
            );
        }
    }
}
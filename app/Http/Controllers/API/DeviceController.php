<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceSetupRequest;
use App\Services\DeviceSetupService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(
 *   schema="DeviceSetupRequest",
 *   type="object",
 *   required={"user_id", "device_id", "device_type"},
 *   @OA\Property(property="user_id", type="string", example="user123"),
 *   @OA\Property(property="device_id", type="string", example="device-abc-123"),
 *   @OA\Property(property="device_type", type="string", example="android")
 * )
 */
class DeviceController extends Controller
{
    use ApiResponse;
    
    protected $deviceService;

    public function __construct(DeviceSetupService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Setup device for user
     * 
     * Register a device for a specific user.
     * 
     * @header Authorization string required Bearer token
     * @body user_id string required The user's unique identifier
     * @body device_id string required The device identifier
     * @body device_type string required The type of device (android, ios, web)
     * @response 200 {
     *   "status": true,
     *   "message": "Device setup successful",
     *   "data": {
     *     "device_id": "device-abc-123",
     *     "user_id": "user123",
     *     "device_type": "android",
     *     "setup_date": "2024-01-01T10:30:00.000000Z"
     *   }
     * }
     * @response 200 {
     *   "status": true,
     *   "message": "Device setup failed",
     *   "data": {
     *     "data": "Failed",
     *     "status": false
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function setup(DeviceSetupRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceService->setupDevice($request->validated());
            return $this->successResponse($result);

        } catch (\Exception $e) {
            Log::error('Device setup error', [
                'error' => $e->getMessage(),
                'user' => $request->user_id
            ]);

            return $this->successResponse([
                'data' => 'Failed',
                'status' => false
            ]);
        }
    }
}

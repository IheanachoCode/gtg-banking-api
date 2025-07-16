<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Vendor;
use App\Traits\ApiResponse;
use App\Http\Resources\VendorResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class VendorController extends BaseController
{
    use ApiResponse;

    /**
     * Get all vendors
     * 
     * Retrieve a list of all available vendors in the system.
     * 
     * @header x-api-key string required API key for authentication
     * @response 200 {
     *   "status": true,
     *   "message": "Request successfully completed",
     *   "data": {
     *     "vendors": [
     *       {
     *         "id": 1,
     *         "name": "Vendor Name",
     *         "code": "VND001",
     *         "description": "Vendor description",
     *         "contact_person": "John Doe",
     *         "phone": "08012345678",
     *         "email": "vendor@example.com",
     *         "address": "123 Vendor Street",
     *         "is_active": true,
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "An error occurred while fetching vendors",
     *   "data": null
     * }
     *
     * @security ApiKeyAuth
     */
    public function getAllVendors(): JsonResponse
    {
        try {
            $vendors = Vendor::all();
            return $this->successResponse([
                'vendors' => VendorResource::collection($vendors)
            ], 'Request successfully completed');
        } catch (\Exception $e) {
            Log::error('Error fetching vendors: ' . $e->getMessage());
            return $this->errorResponse('An error occurred while fetching vendors', 500);
        }
    }
}
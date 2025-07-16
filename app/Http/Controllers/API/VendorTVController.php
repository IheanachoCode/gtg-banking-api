<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\VendorTV;
use App\Http\Resources\VendorTVResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;


class VendorTVController extends BaseController
{
    /**
     * Get all TV vendors
     * 
     * Retrieve a list of all TV vendors in the system.
     * 
     * @header x-api-key string required API key for authentication
     * @response 200 {
     *   "status": true,
     *   "message": "Request successfully completed",
     *   "data": {
     *     "vendors": [
     *       {
     *         "id": 1,
     *         "name": "DSTV",
     *         "code": "DSTV",
     *         "description": "Digital Satellite Television",
     *         "logo": "https://example.com/dstv-logo.png",
     *         "is_active": true,
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       },
     *       {
     *         "id": 2,
     *         "name": "GOTV",
     *         "code": "GOTV",
     *         "description": "GOtv Nigeria",
     *         "logo": "https://example.com/gotv-logo.png",
     *         "is_active": true,
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "An error occurred while fetching TV vendors",
     *   "data": null
     * }
     *
     * @security ApiKeyAuth
     */
    public function getAllVendors(): JsonResponse
    {
        try {
            $vendors = VendorTV::all();

            Log::info('TV Vendors fetched', [
                'count' => $vendors->count()
            ]);

            return $this->successResponse([
                'vendors' => VendorTVResource::collection($vendors)
            ], 'Request successfully completed');

        } catch (\Exception $e) {
            Log::error('Error fetching TV vendors', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'An error occurred while fetching TV vendors', 
                500
            );
        }
    }
}
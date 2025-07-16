<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Desco;
use App\Http\Resources\DescoResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class DescoController extends BaseController
{
    /**
     * Get all DESCO providers
     *
     * Retrieve a list of all active DESCO (Distribution Company) providers.
     * 
     * @header x-api-key string required API key for authentication
     * @response 200 {
     *   "status": true,
     *   "message": "DESCO providers fetched successfully",
     *   "data": {
     *     "desco": [
     *       {
     *         "id": 1,
     *         "name": "Ikeja Electric",
     *         "code": "IE",
     *         "area": "Ikeja, Lagos",
     *         "status": "active",
     *         "contact_number": "08012345678",
     *         "email": "info@ikejaelectric.com",
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       },
     *       {
     *         "id": 2,
     *         "name": "Eko Electricity",
     *         "code": "EKEDC",
     *         "area": "Victoria Island, Lagos",
     *         "status": "active",
     *         "contact_number": "08087654321",
     *         "email": "info@ekedc.com",
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "Failed to fetch DESCO providers",
     *   "data": null
     * }
     * @security ApiKeyAuth
     */
    public function getAllDesco(): JsonResponse
    {
        try {
            $desco = Desco::where('status', 'active')->get();
            return $this->successResponse([
                'desco' => DescoResource::collection($desco),
            ], 'DESCO providers fetched successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching DESCO: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch DESCO providers', 500);
        }
    }
}
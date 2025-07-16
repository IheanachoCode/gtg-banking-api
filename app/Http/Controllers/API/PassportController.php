<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPassportRequest;
use App\Services\PassportUploadService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PassportController extends Controller
{
    use ApiResponse;
    protected $passportUploadService;

    public function __construct(PassportUploadService $passportUploadService)
    {
        $this->passportUploadService = $passportUploadService;
    }

    /**
     * Upload user passport image
     *
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function upload(UploadPassportRequest $request): JsonResponse
    {
        $result = $this->passportUploadService->uploadPassport($request->validated());
        if (!empty($result['status']) && $result['status'] === true) {
            return $this->successResponse($result['data'] ?? [], $result['message'] ?? 'Passport uploaded successfully');
        }
        return $this->errorResponse($result['message'] ?? 'Upload failed', 400, $result['data'] ?? []);
    }
}

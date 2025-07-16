<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackImagesRequest;
use App\Services\FeedbackImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackImageController extends Controller
{
    use ApiResponse;
    protected $feedbackImageService;

    public function __construct(FeedbackImageService $feedbackImageService)
    {
        $this->feedbackImageService = $feedbackImageService;
    }

    /**
     * Upload feedback images
     * 
     * Upload images related to feedback submission.
     * 
     * @header Authorization string required Bearer token
     * @body reference_no string required Reference number for the feedback
     * @body first_image file required First feedback image file
     * @body second_image file required Second feedback image file
     * @response 200 {
     *   "received": {
     *     "reference_no": "FBK-123456",
     *     "first_image": "image1.jpg",
     *     "second_image": "image2.jpg"
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function store(FeedbackImagesRequest $request): JsonResponse
    {
        $result = $this->feedbackImageService->uploadImages($request->validated());
        if (!empty($result['status']) && $result['status'] === true) {
            return $this->successResponse($result['data'] ?? [], $result['message'] ?? 'Images uploaded successfully');
        }
        return $this->errorResponse($result['message'] ?? 'Upload failed', 400, $result['data'] ?? []);
    }

    
    
} 
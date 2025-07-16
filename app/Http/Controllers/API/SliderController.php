<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SliderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SliderController extends Controller
{
    use ApiResponse;
    
    protected $sliderService;

    public function __construct(SliderService $sliderService)
    {
        $this->sliderService = $sliderService;
    }

    /**
     * Get all slider images
     * 
     * Retrieve all slider images for the application.
     * 
     * @header Authorization string required Bearer token
     * @response 200 {
     *   "status": true,
     *   "message": "Slider images retrieved successfully",
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Welcome to GTG",
     *       "description": "Your trusted financial partner",
     *       "image_url": "https://example.com/slider1.jpg",
     *       "order": 1,
     *       "is_active": true
     *     },
     *     {
     *       "id": 2,
     *       "title": "Easy Banking",
     *       "description": "Banking made simple",
     *       "image_url": "https://example.com/slider2.jpg",
     *       "order": 2,
     *       "is_active": true
     *     }
     *   ]
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getSlides(): JsonResponse
    {
        $result = $this->sliderService->getAllSlideImages();
        
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message']);
        } else {
            return $this->errorResponse($result['message'], 404);
        }
    }
}

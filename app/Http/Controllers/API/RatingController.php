<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RateAccountOfficerRequest;
use App\Services\RatingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;


class RatingController extends Controller
{
    use ApiResponse;

    protected $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * Rate an account officer
     * 
     * Submit a rating and comment for an account officer.
     * 
     * @header Authorization string required Bearer token
     * @body officer_id string required The account officer's unique identifier
     * @body user_id string required The user's unique identifier
     * @body rating integer required The rating (1-5)
     * @body comment string required The rating comment
     * @response 200 {
     *   "status": true,
     *   "message": "Rating submitted successfully",
     *   "data": {
     *     "rating_id": 1,
     *     "officer_id": "OFFICER123",
     *     "user_id": "USER456",
     *     "rating": 5,
     *     "comment": "Very helpful and professional",
     *     "created_at": "2024-01-01T10:30:00.000000Z"
     *   }
     * }
     * @response 400 {
     *   "status": false,
     *   "message": "Rating failed",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function store(RateAccountOfficerRequest $request): JsonResponse
    {
        $result = $this->ratingService->rateOfficer($request->validated());
        if ($result['status']) {
            return $this->successResponse($result['data'] ?? null, $result['message'] ?? 'Rating submitted successfully');
        } else {
            return $this->errorResponse($result['message'] ?? 'Rating failed', 400, $result['data'] ?? null);
        }
    }
}

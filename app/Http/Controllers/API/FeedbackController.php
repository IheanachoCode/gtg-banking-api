<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountOfficerFeedbackRequest;
use App\Services\FeedbackService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    use ApiResponse;
    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    /**
     * Submit feedback for account officer
     * 
     * Submit feedback and rating for an account officer.
     * 
     * @header Authorization string required Bearer token
     * @body user_id string required The user's unique identifier
     * @body officer_id string required The account officer's unique identifier
     * @body rating integer required The rating (1-5)
     * @body comment string required The feedback comment
     * @response 200 {
     *   "status": true,
     *   "message": "Feedback submitted successfully",
     *   "data": {
     *     "feedback_id": 1,
     *     "user_id": "user123",
     *     "officer_id": "officer456",
     *     "rating": 5,
     *     "comment": "Very helpful and responsive",
     *     "created_at": "2024-01-01T10:30:00.000000Z"
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function store(AccountOfficerFeedbackRequest $request): JsonResponse
    {
        $result = $this->feedbackService->submitFeedback($request->validated());
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message']);
        } else {
            return $this->errorResponse($result['message'], 400, $result['data'] ?? []);
        }
    }
}
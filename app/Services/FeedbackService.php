<?php

namespace App\Services;

use App\Models\Feedback;
use Illuminate\Support\Facades\Log;

class FeedbackService
{
    public function submitFeedback(array $data): array
    {
        try {
            $feedback = Feedback::create($data);
            return [
                'status' => (bool) $feedback,
                'message' => $feedback ? 'Feedback submitted successfully.' : 'Failed to submit feedback.',
                'data' => $feedback ? $feedback->toArray() : null
            ];
        } catch (\Exception $e) {
            Log::error('Feedback submission failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return [
                'status' => false,
                'message' => 'Failed to submit feedback.',
                'data' => null
            ];
        }
    }
}
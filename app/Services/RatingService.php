<?php

namespace App\Services;

use App\Models\Rating;
use Illuminate\Support\Facades\Log;

class RatingService
{
    public function rateOfficer(array $data): array
    {
        try {
            $rating = Rating::create($data);
            return [
                'status' => (bool) $rating,
                'message' => $rating ? 'Rating submitted successfully.' : 'Failed to submit rating.',
                'data' => $rating ? $rating->toArray() : null
            ];
        } catch (\Exception $e) {
            Log::error('Rating creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return [
                'status' => false,
                'message' => 'Failed to submit rating.',
                'data' => null
            ];
        }
    }
}

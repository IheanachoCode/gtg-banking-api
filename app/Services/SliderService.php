<?php

namespace App\Services;

use App\Models\Slider;
use Illuminate\Support\Facades\Log;

class SliderService
{
    public function getAllSlideImages(): array
    {
        try {
            $images = Slider::select('imageContent')
                ->get()
                ->map(function ($slider) {
                    return [
                        'imageContent' => base64_encode($slider->imageContent)
                    ];
                })
                ->toArray();
            return [
                'status' => !empty($images),
                'message' => !empty($images) ? 'Slides fetched successfully.' : 'No slides found.',
                'data' => $images
            ];
        } catch (\Exception $e) {
            Log::error('Slide image fetch failed', [
                'error' => $e->getMessage()
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch slides.',
                'data' => []
            ];
        }
    }
}

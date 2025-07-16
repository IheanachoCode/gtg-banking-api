<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategorySearchService
{
    // public function searchCategory(string $categoryName): array
    // {
    //     try {
    //         $categories = Category::where('category_name', $categoryName)
    //             ->select('image')
    //             ->get()
    //             ->map(function ($category) {
    //                 return [
    //                     'image' => $category->image_url
    //                 ];
    //             })
    //             ->toArray();

    //         return [
    //             'response' => !empty($categories) ? 'successful' : 'Failed',
    //             'fetchmessage' => $categories
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error('Category search failed', [
    //             'error' => $e->getMessage(),
    //             'category' => $categoryName
    //         ]);

    //         return [
    //             'response' => 'Failed',
    //             'fetchmessage' => []
    //         ];
    //     }
    // }



        public function searchCategory(string $categoryName): array
    {
        try {
            $categories = Category::where('category_name', $categoryName)
                ->select('image')
                ->get()
                ->map(function ($category) {
                    return [
                        'image' => $category->image_url
                    ];
                })
                ->toArray();
            return [
                'status' => !empty($categories),
                'message' => !empty($categories) ? 'Category images fetched successfully.' : 'No images found.',
                'data' => $categories
            ];
        } catch (\Exception $e) {
            Log::error('Category search failed', [
                'error' => $e->getMessage(),
                'category' => $categoryName
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch category images.',
                'data' => []
            ];
        }
    }





}

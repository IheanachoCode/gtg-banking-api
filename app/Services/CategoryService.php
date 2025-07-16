<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    // public function getAllCategories(): array
    // {
    //     try {
    //         $categories = Category::select('category_name', 'image')
    //             ->get()
    //             ->map(function ($category) {
    //                 return [
    //                     'category_name' => $category->category_name,
    //                     'image' => $category->image_url
    //                 ];
    //             })
    //             ->toArray();

    //         return [
    //             'response' => !empty($categories) ? 'successful' : 'Failed',
    //             'respond' => !empty($categories),
    //             'categories' => $categories
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error('Category fetch failed', [
    //             'error' => $e->getMessage()
    //         ]);

    //         return [
    //             'response' => 'Failed',
    //             'respond' => false,
    //             'categories' => []
    //         ];
    //     }
    // }


            public function getAllCategories(): array
        {
            try {
                $categories = Category::select('category_name', 'image')
                    ->get()
                    ->map(function ($category) {
                        return [
                            'category_name' => $category->category_name,
                            'image' => $category->image_url
                        ];
                    })
                    ->toArray();
                return [
                    'status' => !empty($categories),
                    'message' => !empty($categories) ? 'Categories fetched successfully.' : 'No categories found.',
                    'data' => $categories
                ];
            } catch (\Exception $e) {
                Log::error('Category fetch failed', [
                    'error' => $e->getMessage()
                ]);
                return [
                    'status' => false,
                    'message' => 'Failed to fetch categories.',
                    'data' => []
                ];
            }
        }

}

<?php

namespace App\Services;

use App\Models\SubCategory;
use Illuminate\Support\Facades\Log;

class SubCategoryService
{
    public function getSubCategories(string $category): array
    {
        try {
            $subCategories = SubCategory::where('category', $category)
                ->select('image', 'sub_categories', 'category')
                ->get()
                ->map(function ($subCategory) {
                    return [
                        'image' => $subCategory->image_url,
                        'sub_categories' => $subCategory->sub_categories,
                        'category' => $subCategory->category_url
                    ];
                })
                ->toArray();
            return [
                'status' => !empty($subCategories),
                'message' => !empty($subCategories) ? 'Subcategories fetched successfully.' : 'No subcategories found.',
                'data' => $subCategories
            ];
        } catch (\Exception $e) {
            Log::error('Subcategory fetch failed', [
                'error' => $e->getMessage(),
                'category' => $category
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch subcategories.',
                'data' => []
            ];
        }
    }
}

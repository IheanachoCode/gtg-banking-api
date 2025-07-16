<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryRequest;
use App\Services\SubCategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SubCategoryController extends Controller
{
    use ApiResponse;
    
    protected $subCategoryService;

    public function __construct(SubCategoryService $subCategoryService)
    {
        $this->subCategoryService = $subCategoryService;
    }

    /**
     * Get subcategories by category
     * 
     * Retrieve all subcategories for a specific category.
     * 
     * @header Authorization string required Bearer token
     * @body category string required The category name to get subcategories for
     * @response 200 {
     *   "status": true,
     *   "message": "Subcategories fetched successfully.",
     *   "data": {
     *     "subcategories": [
     *       {
     *         "image": "https://example.com/image.jpg",
     *         "sub_categories": "Smartphones, Tablets, Laptops",
     *         "category": "Electronics"
     *       }
     *     ]
     *   }
     * }
     * @response 200 {
     *   "status": true,
     *   "message": "No subcategories found.",
     *   "data": {
     *     "subcategories": []
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getSubCategories(SubCategoryRequest $request): JsonResponse
    {
        $result = $this->subCategoryService->getSubCategories($request->category);

        return $this->successResponse(
            ['subcategories' => $result['data']],
            $result['message']
        );
    }
}

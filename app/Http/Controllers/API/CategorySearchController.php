<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategorySearchRequest;
use App\Services\CategorySearchService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CategorySearchController extends Controller
{
    use ApiResponse;
    protected $categorySearchService;

    public function __construct(CategorySearchService $categorySearchService)
    {
        $this->categorySearchService = $categorySearchService;
    }
 
    /**
     * Search category by name
     * 
     * Search for categories by name.
     * 
     * @header Authorization string required Bearer token
     * @body category string required The category name to search for
     * @response 200 {
     *   "status": true,
     *   "message": "Search successful",
     *   "data": {
     *     "categories": [
     *       {
     *         "id": 1,
     *         "name": "Electronics",
     *         "description": "Electronic devices and gadgets"
     *       }
     *     ]
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "No categories found",
     *   "data": []
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function search(CategorySearchRequest $request): JsonResponse
    {
        $category = $request->input('category');
        $result = $this->categorySearchService->searchCategory($category);
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message'] ?? 'Search successful');
        } else {
            return $this->errorResponse($result['message'] ?? 'Search failed', 404, $result['data'] ?? []);
        }
    }
}

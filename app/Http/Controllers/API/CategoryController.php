<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
 
    /**
     * Get all item categories
     * 
     * Retrieve a list of all available item categories in the system.
     * 
     * @header Authorization string required Bearer token
     * @response 200 {
     *   "status": true,
     *   "message": "Categories retrieved successfully",
     *   "data": {
     *     "categories": [
     *       {
     *         "id": 1,
     *         "name": "Electronics",
     *         "description": "Electronic devices and gadgets",
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Failed to fetch categories",
     *   "data": {
     *     "categories": []
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function index(): JsonResponse
    {
        $result = $this->categoryService->getAllCategories();
        
        if ($result['status']) {
            return $this->successResponse([
                'categories' => $result['data']
            ], 'Categories retrieved successfully');
        } else {
            return $this->errorResponse('Failed to fetch categories', 404, [
                'categories' => []
            ]);
        }
    }
}

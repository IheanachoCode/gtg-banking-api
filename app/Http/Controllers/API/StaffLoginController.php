<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffLoginRequest;
use App\Services\StaffService;
use App\Traits\ApiResponse;

class StaffLoginController extends Controller
{
    use ApiResponse;

    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function login(StaffLoginRequest $request)
    {
        $validated = $request->validated();

        $success = $this->staffService->login(
            $validated['staffID'],
            $validated['password']
        );

        if (!$success) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        return $this->successResponse(null, 'Login successful');
    }












}
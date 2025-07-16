<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PinValidationRequest;
use App\Services\PinValidationService;
use App\Http\Requests\ChangePinRequest;
use App\Services\PinManagementService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\Services\PinService;


class PinController extends Controller
{
    use ApiResponse;
    protected $pinService;

    protected $pinValidationService;
    protected $pinManagementService;

    public function __construct(PinValidationService $pinValidationService, PinManagementService $pinManagementService, PinService $pinService)
    {
        $this->pinValidationService = $pinValidationService;
        $this->pinManagementService = $pinManagementService;
        $this->pinService = $pinService;
    }

    
        /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */

        public function validatePin(PinValidationRequest $request)
    {
        try {
            $isValid = $this->pinService->validatePin(
                $request->user_id,   // Use snake_case if that's your convention
                $request->pin_no
            );

            if (!$request->user_id || !$request->pin_no) {
                return $this->errorResponse('user_id and pin_no are required', 422);
            }

            return $this->successResponse([
                'is_valid' => $isValid
            ], $isValid ? 'PIN is valid' : 'PIN is invalid');

        } catch (\Exception $e) {
            Log::error('PIN validation error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user_id
            ]);

            return $this->errorResponse('Failed to validate PIN', 500);
        }
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    
    public function changePin(ChangePinRequest $request)
    {
        $result = $this->pinService->changePin($request->validated());
        return $this->successResponse($result);
    }


}

<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;

class StaffPinValidationService
{
    public function validatePin(string $staffId, string $pin): array
    {
        try {
            $isValid = DB::table('staff')
                ->where('staffID', $staffId)
                ->where('Pin', $pin)
                ->exists();
            return [
                'status' => $isValid,
                'message' => $isValid ? 'PIN validated successfully.' : 'Invalid PIN.',
                'data' => null
            ];
        } catch (\Exception $e) {
            \Log::error('PIN validation failed', [
                'error' => $e->getMessage(),
                'staffId' => $staffId
            ]);
            return [
                'status' => false,
                'message' => 'Failed to validate PIN.',
                'data' => null
            ];
        }
    }
}
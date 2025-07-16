<?php

namespace App\Services;

use App\Models\StaffRequest;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StaffRequestService
{
    public function createRequest(array $data): array
    {
        try {
            $request = StaffRequest::create($data);
            return [
                'status' => (bool) $request,
                'message' => $request ? 'Staff request created successfully.' : 'Failed to create staff request.',
                'data' => $request ? $request->toArray() : null
            ];
        } catch (\Exception $e) {
            \Log::error('Staff request creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return [
                'status' => false,
                'message' => 'Failed to create staff request.',
                'data' => null
            ];
        }
    }
}
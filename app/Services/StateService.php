<?php

namespace App\Services;

use App\Models\State;
use Illuminate\Support\Facades\Log;

class StateService
{
    public function getAllStates(): array
    {
        try {
            $states = State::select('state')
                ->get()
                ->map(function($state) {
                    return ['state' => $state->state];
                })
                ->toArray();
            return [
                'status' => !empty($states),
                'message' => !empty($states) ? 'States fetched successfully.' : 'No states found.',
                'data' => $states
            ];
        } catch (\Exception $e) {
            Log::error('State fetch failed', [
                'error' => $e->getMessage()
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch states.',
                'data' => []
            ];
        }
    }
}
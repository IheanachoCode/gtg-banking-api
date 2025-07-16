<?php

namespace App\Services;

use App\Models\StateLga;
use Illuminate\Support\Facades\Log;

class LgaService
{
    public function getLgasByState(string $state): array
    {
        try {
            $lgas = StateLga::where('state', $state)
                ->orderBy('lga', 'asc')
                ->pluck('lga')
                ->map(function($lga) {
                    return ['lga' => $lga];
                })
                ->toArray();

            return [
                'status' => !empty($lgas),
                'message' => !empty($lgas) ? 'LGAs retrieved successfully' : 'No LGAs found for this state',
                'data' => $lgas
            ];

        } catch (\Exception $e) {
            Log::error('LGA fetch failed', [
                'error' => $e->getMessage(),
                'state' => $state
            ]);

            return [
                'status' => false,
                'message' => 'Failed to fetch LGAs',
                'data' => []
            ];
        }
    }

    public function getAllLgas(): array
    {
        try {
            $lgas = StateLga::all();
            return [
                'status' => $lgas->isNotEmpty(),
                'message' => $lgas->isNotEmpty() ? 'LGAs fetched successfully.' : 'No LGAs found.',
                'data' => $lgas->toArray()
            ];
        } catch (\Exception $e) {
            \Log::error('LGAs fetch failed', [
                'error' => $e->getMessage()
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch LGAs.',
                'data' => []
            ];
        }
    }
}
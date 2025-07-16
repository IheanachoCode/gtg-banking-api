<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BiometricService
{
    public function getUserBiometric(string $userId): array
    {
        try {
            $biometric = DB::table('client_registrations')
                ->where('user_id', $userId)
                ->select('password')
                ->get()
                ->map(function($item) {
                    return [
                        'password' => $item->password
                    ];
                })
                ->toArray();

            if (empty($biometric)) {
                Log::info('No biometric match found', ['user_id' => $userId]);
                return [
                    'status' => false,
                    'message' => 'No Match Found',
                    'data' => null
                ];
            }

            Log::info('Biometric fetched successfully', ['user_id' => $userId]);
            return [
                'status' => true,
                'message' => 'Biometric fetched successfully.',
                'data' => $biometric
            ];

        } catch (\Exception $e) {
            Log::error('Biometric fetch failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'status' => false,
                'message' => 'Failed to fetch biometric.',
                'data' => null
            ];
        }
    }
}

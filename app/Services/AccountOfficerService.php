<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Staff;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AccountOfficerService
{
    public function getOfficerName(string $accountNo): array
    {
        try {
            // Get user_id from account
            $userId = Account::where('account_no', $accountNo)
                ->value('user_id');

            if (!$userId) {
                return [
                    'data' => 'Failed',
                    'status' => false
                ];
            }

            // Get account officer from transaction
            $transaction = Transaction::where('user_id', $userId)
                ->first();

            if (!$transaction || !$transaction->account_officer) {
                return [
                    'data' => 'Failed',
                    'status' => false
                ];
            }

            // Extract staff ID from account_officer
            $staffId = explode('_', $transaction->account_officer)[0] ?? null;

            if (!$staffId) {
                return [
                    'data' => 'Failed',
                    'status' => false
                ];
            }

            // Get staff name
            $staff = Staff::find($staffId);

            if (!$staff) {
                return [
                    'data' => 'Failed',
                    'status' => false
                ];
            }

            return [
                'data' => $staff->full_name,
                'status' => true
            ];

        } catch (\Exception $e) {
            Log::error('Account officer name fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);

            return [
                'data' => 'Failed',
                'status' => false
            ];
        }
    }

    public function getOfficerPhone(string $accountNo): array
    {
        try {
            $userId = Account::where('account_no', $accountNo)
                ->value('user_id');
            if (!$userId) {
                return $this->failureResponse();
            }
            $transaction = Transaction::where('user_id', $userId)
                ->first();
            if (!$transaction || !$transaction->account_officer) {
                return $this->failureResponse();
            }
            $staffId = explode('_', $transaction->account_officer)[0] ?? null;
            if (!$staffId) {
                return $this->failureResponse();
            }
            $phone = Staff::where('staffID', $staffId)
                ->value('phone_no');
            return [
                'status' => (bool) $phone,
                'message' => $phone ? 'Account officer phone fetched successfully.' : 'Account officer phone not found.',
                'data' => $phone ? ['phone' => $phone] : null
            ];
        } catch (\Exception $e) {
            Log::error('Account officer phone fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);
            return $this->failureResponse();
        }
    }

    public function getOfficerEmail(string $accountNo): array
    {
        try {
            $userId = Account::where('account_no', $accountNo)
                ->value('user_id');

            if (!$userId) {
                return $this->failureResponse();
            }

            $transaction = Transaction::where('user_id', $userId)
                ->first();

            if (!$transaction || !$transaction->account_officer) {
                return $this->failureResponse();
            }

            $staffId = explode('_', $transaction->account_officer)[0] ?? null;

            if (!$staffId) {
                return $this->failureResponse();
            }

            $email = Staff::where('staffID', $staffId)
                ->value('email');

            return [
                'data' => $email ?: 'Failed',
                'status' => (bool) $email
            ];

        } catch (\Exception $e) {
            Log::error('Account officer email fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);

            return $this->failureResponse();
        }
    }

    public function getAllOfficers(): array
    {
        try {
            $officers = User::select('account_officer')
                ->distinct()
                ->whereNotNull('account_officer')
                ->get()
                ->map(function($user) {
                    return [
                        'account_officer' => $user->account_officer
                    ];
                })
                ->toArray();

            return [
                'status' => !empty($officers),
                'message' => !empty($officers) ? 'Account officers fetched successfully.' : 'No account officers found.',
                'data' => $officers
            ];

        } catch (\Exception $e) {
            Log::error('Account officers fetch failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'status' => false,
                'message' => 'Failed to fetch account officers.',
                'data' => []
            ];
        }
    }

    public function getAllOfficersWithDetails(): array
    {
        try {
            $officers = User::select('account_officer')
                ->distinct()
                ->whereNotNull('account_officer')
                ->get()
                ->map(function($user) {
                    $staff = Staff::where('staffID', $user->account_officer)->first();
                    return [
                        'name' => $staff ? $staff->name : null
                    ];
                })
                ->filter()
                ->values()
                ->toArray();

            return [
                'status' => !empty($officers),
                'message' => !empty($officers) ? 'Account officers with details fetched successfully.' : 'No account officers found.',
                'data' => $officers
            ];

        } catch (\Exception $e) {
            Log::error('Account officers fetch failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'status' => false,
                'message' => 'Failed to fetch account officers with details.',
                'data' => []
            ];
        }
    }

    private function failureResponse(): array
    {
        return [
            'status' => false,
            'message' => 'Account officer not found.',
            'data' => null
        ];
    }
}

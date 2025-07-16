<?php
namespace App\Services;

use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StaffService
{
    public function login(string $staffId, string $password)
    {
        try {
            $staff = Staff::where('staffID', $staffId)->first();

            if (!$staff || !Hash::check($password, $staff->password)) {
                return [
                    'status' => false,
                    'message' => 'Invalid credentials.',
                    'data' => null
                ];
            }

            // Revoke existing tokens
            $staff->tokens()->delete();

            // Generate new token
            $token = $staff->createToken('auth_token')->plainTextToken;

            return [
                'status' => true,
                'message' => 'Login successful.',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'staff_details' => [
                        'staffID' => $staff->staffID,
                        'name' => $staff->Lastname . ' ' . $staff->othername,
                        'email' => $staff->email,
                        'phone' => $staff->phone_no,
                        'role' => $staff->role ?? 'staff',
                        'department' => $staff->department,
                        'last_login' => now()->format('Y-m-d H:i:s')
                    ]
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Staff login error', [
                'error' => $e->getMessage(),
                'staffID' => $staffId
            ]);
            return [
                'status' => false,
                'message' => 'An error occurred during login.',
                'data' => null
            ];
        }
    }


    public function getDailyTransactions(string $staffId, ?string $date = null)
    {
        $date = $date ?? Carbon::today()->format('Y-m-d');

        $transactions = DB::table('client_deposit_withdrawal')
            ->where('staff_id', $staffId)
            ->whereDate('created_at', $date)
            ->select([
                'account_no',
                'amount',
                'transaction_type',
                'transaction_id',
                'created_at'
            ])
            ->orderBy('id', 'DESC')
            ->get();

        return [
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'account_no' => $transaction->account_no,
                    'amount' => number_format($transaction->amount, 2),
                    'transaction_type' => $transaction->transaction_type,
                    'transaction_id' => $transaction->transaction_id,
                    'created_at' => $transaction->created_at
                ];
            }),
            'summary' => [
                'total_count' => $transactions->count(),
                'total_credit' => number_format($transactions->where('transaction_type', 'Credit')->sum('amount'), 2),
                'total_debit' => number_format($transactions->where('transaction_type', 'Debit')->sum('amount'), 2),
                'date' => $date
            ]
        ];
    }


}

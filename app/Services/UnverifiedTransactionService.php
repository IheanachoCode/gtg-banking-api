<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Transaction;

class UnverifiedTransactionService
{
    public function getUnverifiedTransactions($accountNo): array
    {
        try {
            $transactions = Transaction::where('account_no', $accountNo)
                ->where('verify_stat', '!=', 'Verified')
                ->get();
            return [
                'status' => $transactions->isNotEmpty(),
                'message' => $transactions->isNotEmpty() ? 'Unverified transactions fetched successfully.' : 'No unverified transactions found.',
                'data' => $transactions->toArray()
            ];
        } catch (\Exception $e) {
            \Log::error('Unverified transactions fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch unverified transactions.',
                'data' => []
            ];
        }
    }
}
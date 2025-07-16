<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Transaction;

class VerifiedTransactionService
{
    public function getVerifiedTransactions($accountNo): array
    {
        try {
            $transactions = Transaction::where('account_no', $accountNo)
                ->where('verify_stat', 'Verified')
                ->get();
            return [
                'status' => $transactions->isNotEmpty(),
                'message' => $transactions->isNotEmpty() ? 'Verified transactions fetched successfully.' : 'No verified transactions found.',
                'data' => $transactions->toArray()
            ];
        } catch (\Exception $e) {
            \Log::error('Verified transactions fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch verified transactions.',
                'data' => []
            ];
        }
    }
}
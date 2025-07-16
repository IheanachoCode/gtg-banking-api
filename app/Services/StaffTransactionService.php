<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffTransactionService
{
    public function getDailyTransactions(string $staffId, string $date): array
    {
        return DB::table('client_deposit_withdrawal')
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
            ->get()
            ->map(function ($transaction) {
                return [
                    'account_no' => $transaction->account_no,
                    'amount' => number_format($transaction->amount, 2),
                    'transaction_type' => $transaction->transaction_type,
                    'transaction_id' => $transaction->transaction_id,
                    'created_at' => Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s')
                ];
            })
            ->toArray();
    }


        public function getDailyDepositsTotal(string $staffId, string $date): float
    {
        $total = DB::table('client_deposit_withdrawal')
            ->where('staff_id', $staffId)
            ->where('transaction_type', 'Credit')
            ->whereDate('transaction_date', $date)
            ->sum('amount');

        return $total ?: 0.00;
    }


        public function getDailyWithdrawalsTotal(string $staffId, string $date): float
    {
        $total = DB::table('client_deposit_withdrawal')
            ->where('staff_id', $staffId)
            ->where('transaction_type', 'Debit')
            ->whereDate('transaction_date', $date)
            ->sum('amount');

        return $total ?: 0.00;
    }


}

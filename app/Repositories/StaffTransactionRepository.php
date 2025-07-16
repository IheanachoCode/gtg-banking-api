<?php


namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class StaffTransactionRepository
{
    public function getRecentTransactions(string $staffId): object
    {
        return DB::table('client_deposit_withdrawal')
            ->where('staff_id', $staffId)
            ->select([
                'account_no',
                'amount',
                'transaction_type',
                'transaction_id',
                'created_at'
            ])
            ->orderBy('id', 'DESC')
            ->get();
    }
}

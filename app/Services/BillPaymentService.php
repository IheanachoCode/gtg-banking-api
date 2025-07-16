<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BillPaymentService
{
    protected function generateTransactionId(): string
    {
        return 'Txid-' . substr(str_shuffle('0123456789'), 0, 6) . '-' . 
               substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3);
    }

    public function processBillPayment(array $data): bool
    {
        return DB::transaction(function () use ($data) {
            $accountInfo = DB::table('account_number')
                ->where('account_no', $data['account_no'])
                ->first();
            if (!$accountInfo) {
                throw new \Exception('Invalid account number');
            }

            $userInfo = DB::table('client_registrations')
                ->where('user_id', $data['userID'])
                ->first();
            if (!$userInfo) {
                throw new \Exception('Invalid user ID');
            }

            $fullName = $userInfo->lastname . ' ' . $userInfo->othernames;
            $transactionId = $this->generateTransactionId();
            $today = Carbon::now()->format('Y-m-d');

            // Calculate balances
            $depositBalance = $this->getClientBalance($data['account_no'], 'Credit');
            $withdrawalBalance = $this->getClientBalance($data['account_no'], 'Debit');
            $mainBalance = $depositBalance - $withdrawalBalance;
            $currentBalance = $mainBalance - $data['amount'];

            // Insert bill payment record
            $billPayment = DB::table('client_deposit_withdrawal')->insert([
                'account_no' => $data['account_no'],
                'amount' => $data['amount'],
                'depositor_name' => $fullName,
                'description' => $data['description'],
                'initial_balance' => $depositBalance,
                'current_balance' => $currentBalance,
                'transaction_type' => 'Debit',
                'transaction_id' => $transactionId,
                'staff_id' => 'No Staff',
                'user_id' => $data['userID'],
                'branch_office' => 'none',
                'reconcilliator' => 'none',
                'two_ways_reconcile' => 'none',
                'transaction_date' => $today,
                'account_officer' => 'No Staff',
                'payment_mode' => 'Bill Payment',
                'verify_stat' => 'Not Verified',
                'verify_by' => 'Self',
                'reverse_status' => 'UNREVERSED',
                'ref_no' => $data['ref_no']
            ]);

            // Insert account log
            $accountLog = DB::table('account_log')->insert([
                'transactionID' => $transactionId,
                'transaction_source' => 'mobile Bill Payment',
                'amount' => $data['amount'],
                'transaction_date' => $today,
                'account_id' => $data['account_no'],
                'account_type' => 'Cash Debit',
                'cancellation_status' => '0',
                'staff_id' => 'No staff',
                'series_name' => 'Online Payment'
            ]);

            // Insert cash withdrawal record
            $cashWithdrawal = DB::table('cash_withdrawal')->insert([
                'Account_no' => $data['account_no'],
                'Account_name' => $fullName,
                'Account_type' => $accountInfo->account_type,
                'Account_officer' => 'No Staff',
                'Amount' => $data['amount'],
                'description' => $data['description'],
                'commision_charges' => 0.00,
                'user_id' => $data['userID'],
                'Ref_no' => $transactionId,
                'status' => 'Approved',
                'Transaction_date' => $today,
                'staff_id' => 'No Staff',
                'paymentMode' => 'Bill Payment'
            ]);

            return $billPayment && $accountLog && $cashWithdrawal;
        });
    }

    protected function getClientBalance(string $accountNo, string $transactionType): float
    {
        return DB::table('client_deposit_withdrawal')
            ->where('account_no', $accountNo)
            ->where('transaction_type', $transactionType)
            ->sum('amount') ?: 0.00;
    }
}
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransferService
{
    protected $emailService;
    protected $smsService;

    public function __construct(EmailService $emailService, SmsService $smsService)
    {
        $this->emailService = $emailService;
        $this->smsService = $smsService;
    }

    public function processTransfer(array $data): array
    {
        try {
            DB::beginTransaction();

            // Validate sender account and PIN
            $sender = $this->validateSender($data);
            
            // Generate transaction IDs
            $senderTxId = $this->generateTransactionId();
            $receiverTxId = $this->generateTransactionId();

            // Process sender debit with charges
            $senderBalance = $this->processSenderTransaction($data, $sender, $senderTxId);

            // Process receiver credit
            $receiverBalance = $this->processReceiverTransaction($data, $receiverTxId);

            // Send notifications
            $this->sendNotifications($data, $sender, $senderBalance, $receiverBalance);

            DB::commit();

            return [
                'status' => true,
                'message' => 'Transfer completed successfully',
                'data' => [
                    'transaction_id' => $senderTxId,
                    'amount' => $data['amount_transfer'],
                    'sender_balance' => $senderBalance,
                    'receiver_balance' => $receiverBalance
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transfer failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function validateSender(array $data): object
    {
        $sender = DB::table('client_registrations')
            ->join('account_number', 'client_registrations.user_id', '=', 'account_number.user_id')
            ->where('account_number.account_no', $data['sender_account_no'])
            ->first();

        if (!$sender) {
            throw new \Exception('Sender account not found');
        }

        if ($sender->Pin !== $data['Pin']) {
            throw new \Exception('Invalid PIN');
        }

        if ($sender->account_status !== 'active') {
            throw new \Exception('Account is not active');
        }

        return $sender;
    }

    private function generateTransactionId(): string
    {
        return 'Txid-' . rand(100000, 999999) . '-' . Str::random(3);
    }

    private function processSenderTransaction(array $data, object $sender, string $txId): float
    {
        $balance = $this->getCurrentBalance($data['userID'], $data['sender_account_no']);
        $charges = $this->getTransactionCharges($sender->account_type);
        $totalDebit = $data['amount_transfer'] + $charges;

        if ($balance < $totalDebit) {
            throw new \Exception('Insufficient balance');
        }

        $currentBalance = $balance - $totalDebit;

        // Record main transfer
        DB::table('client_deposit_withdrawal')->insert([
            'account_no' => $data['sender_account_no'],
            'amount' => $data['amount_transfer'],
            'transaction_type' => 'Debit',
            'description' => "Transfer to {$data['Receiver_account_number']}",
            'transaction_id' => $txId,
            'initial_balance' => $balance,
            'current_balance' => $currentBalance,
            'user_id' => $data['userID'],
            'staff_id' => 'No Staff',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Record charges if any
        if ($charges > 0) {
            DB::table('client_deposit_withdrawal')->insert([
                'account_no' => $data['sender_account_no'],
                'amount' => $charges,
                'transaction_type' => 'Debit',
                'description' => 'Transfer charges',
                'transaction_id' => $txId,
                'initial_balance' => $balance,
                'current_balance' => $currentBalance,
                'user_id' => $data['userID'],
                'staff_id' => 'No Staff',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return $currentBalance;
    }

    private function getCurrentBalance(string $userId, string $accountNo): float
    {
        $credits = DB::table('client_deposit_withdrawal')
            ->where('user_id', $userId)
            ->where('account_no', $accountNo)
            ->where('transaction_type', 'Credit')
            ->sum('amount');

        $debits = DB::table('client_deposit_withdrawal')
            ->where('user_id', $userId)
            ->where('account_no', $accountNo)
            ->where('transaction_type', 'Debit')
            ->sum('amount');

        return $credits - $debits;
    }

    private function getTransactionCharges(string $accountType): float
    {
        $charges = DB::table('account_constraints')
            ->where('account_type', $accountType)
            ->value('transactions_charges');

        return $charges ?? 0;
    }

    private function processSenderDebit($data, $sender, $senderTxId)
    {
        $balance = $this->calculateBalance($data['userID'], $data['sender_account_no']);
        $charges = $this->getTransactionCharges($sender->account_type);
        $currentBalance = $balance - $data['amount_transfer'] - $charges;

        // Process main transfer debit
        $this->recordTransaction([
            'account_no' => $data['sender_account_no'],
            'amount' => $data['amount_transfer'],
            'depositor_name' => 'NULL',
            'description' => $this->formatDescription('debit', $data, $sender),
            'initial_balance' => $balance,
            'current_balance' => $currentBalance,
            'transaction_type' => 'Debit',
            'transaction_id' => $senderTxId,
            'staff_id' => 'No Staff',
            'user_id' => $data['userID'],
            'branch_office' => 'No branch',
            'payment_mode' => 'Transfer'
        ]);

        // Process charges if applicable
        if ($charges > 0) {
            $this->recordTransaction([
                'account_no' => $data['sender_account_no'],
                'amount' => $charges,
                'depositor_name' => 'Charges',
                'description' => 'Transfer charges',
                'initial_balance' => $balance,
                'current_balance' => $currentBalance,
                'transaction_type' => 'Debit',
                'transaction_id' => $senderTxId,
                'staff_id' => 'No Staff',
                'user_id' => $data['userID'],
                'branch_office' => 'No branch',
                'payment_mode' => 'Transfer'
            ]);
        }

        return $currentBalance;
    }

    private function processReceiverCredit($data, $receiverTxId)
    {
        $receiver = DB::table('account_number')
            ->join('client_registrations', 'account_number.user_id', '=', 'client_registrations.user_id')
            ->where('account_no', $data['Receiver_account_number'])
            ->first();

        $balance = $this->calculateBalance($receiver->user_id, $data['Receiver_account_number']);
        $currentBalance = $balance + $data['amount_transfer'];

        return $this->recordTransaction([
            'account_no' => $data['Receiver_account_number'],
            'amount' => $data['amount_transfer'],
            'depositor_name' => 'NULL',
            'description' => $this->formatDescription('credit', $data, $receiver),
            'initial_balance' => $balance,
            'current_balance' => $currentBalance,
            'transaction_type' => 'Credit',
            'transaction_id' => $receiverTxId,
            'staff_id' => 'No Staff',
            'user_id' => $receiver->user_id,
            'branch_office' => 'No branch',
            'payment_mode' => 'Transfer'
        ]);
    }

    private function recordTransaction($data)
    {
        return DB::table('client_deposit_withdrawal')->insert(array_merge($data, [
            'reconcilliator' => 'NONE',
            'two_ways_reconcile' => 'NONE',
            'transaction_date' => now(),
            'account_officer' => 'NONE',
            'created_at' => now(),
            'updated_at' => now()
        ]));
    }

    private function formatDescription($type, $data, $account)
    {
        if ($type === 'debit') {
            return sprintf(
                'Debit to %s %s (%s)',
                $data['Receiver_account_number'],
                $account->lastname . ' ' . $account->othernames,
                $data['sender_description']
            );
        }

        return sprintf(
            'Credit from %s %s (%s)',
            $data['sender_account_no'],
            $account->lastname . ' ' . $account->othernames,
            $data['sender_description']
        );
    }

    // private function getTransactionCharges($accountType)
    // {
    //     $constraints = DB::table('account_constraints')
    //         ->where('account_type', $accountType)
    //         ->first();

    //     return $constraints ? $constraints->transactions_charges : 0;
    // }

    private function logTransaction($txId, $type, $amount, $accountId)
    {
        return DB::table('account_log')->insert([
            'transactionID' => $txId,
            'transaction_source' => $type,
            'amount' => $amount,
            'transaction_date' => now(),
            'account_id' => $accountId,
            'account_type' => 'Cash Deposit',
            'cancellation_status' => '0',
            'staff_id' => 'No staff',
            'series_name' => $type === 'Transfer charges' ? 'Income' : 'Deposit',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function calculateBalance($userId, $accountNo)
    {
        $transactions = DB::table('client_deposit_withdrawal')
            ->where('user_id', $userId)
            ->where('account_no', $accountNo)
            ->orderBy('id', 'desc')
            ->first();

        return $transactions ? $transactions->current_balance : 0;
    }


    private function processReceiverTransaction(array $data, string $txId): float
    {
        $receiver = DB::table('client_registrations')
            ->join('account_number', 'client_registrations.user_id', '=', 'account_number.user_id')
            ->where('account_number.account_no', $data['Receiver_account_number'])
            ->first();

        if (!$receiver) {
            throw new \Exception('Receiver account not found');
        }

        $balance = $this->getCurrentBalance($receiver->user_id, $data['Receiver_account_number']);
        $newBalance = $balance + $data['amount_transfer'];

        DB::table('client_deposit_withdrawal')->insert([
            'account_no' => $data['Receiver_account_number'],
            'amount' => $data['amount_transfer'],
            'transaction_type' => 'Credit',
            'description' => "Credit from {$data['sender_account_no']}",
            'transaction_id' => $txId,
            'initial_balance' => $balance,
            'current_balance' => $newBalance,
            'user_id' => $receiver->user_id,
            'staff_id' => 'No Staff',
            'depositor_name' => 'NULL',
            'branch_office' => 'No branch',
            'payment_mode' => 'Transfer',
            'reconcilliator' => 'NONE',
            'two_ways_reconcile' => 'NONE',
            'transaction_date' => now(),
            'account_officer' => 'NONE',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Log the transaction
        $this->logTransaction($txId, 'mobile app transfer', $data['amount_transfer'], $receiver->user_id);

        return $newBalance;
    }

    private function sendNotifications(array $data, object $sender, float $senderBalance, float $receiverBalance): void
    {
        try {
            // Get receiver details
            $receiver = DB::table('client_registrations')
                ->join('account_number', 'client_registrations.user_id', '=', 'account_number.user_id')
                ->where('account_number.account_no', $data['Receiver_account_number'])
                ->first();

            // Send sender notifications
            $this->emailService->sendTransactionAlert([
                'customerName' => $sender->lastname . ' ' . $sender->othernames,
                'txType' => 'Debit',
                'accountNo' => $data['sender_account_no'],
                'description' => "Transfer to {$data['Receiver_account_number']}",
                'amount' => $data['amount_transfer'],
                'txDate' => now()->format('Y-m-d H:i:s'),
                'balance' => $senderBalance,
                'receiverEmail' => $sender->email,
                'subject' => 'Debit Transaction Alert'
            ]);

            // Send receiver notifications
            $this->emailService->sendTransactionAlert([
                'customerName' => $receiver->lastname . ' ' . $receiver->othernames,
                'txType' => 'Credit',
                'accountNo' => $data['Receiver_account_number'],
                'description' => "Credit from {$data['sender_account_no']}",
                'amount' => $data['amount_transfer'],
                'txDate' => now()->format('Y-m-d H:i:s'),
                'balance' => $receiverBalance,
                'receiverEmail' => $receiver->email,
                'subject' => 'Credit Transaction Alert'
            ]);

            // Send SMS notifications if phone numbers are available
            if (!empty($sender->phone)) {
                $this->smsService->sendTransferSms(
                    $sender->phone,
                    $data['amount_transfer'],
                    "Transfer to {$data['Receiver_account_number']}",
                    $data['sender_account_no'],
                    'Debit',
                    $senderBalance
                );
            }

            if (!empty($receiver->phone)) {
                $this->smsService->sendTransferSms(
                    $receiver->phone,
                    $data['amount_transfer'],
                    "Credit from {$data['sender_account_no']}",
                    $data['Receiver_account_number'],
                    'Credit',
                    $receiverBalance
                );
            }

        } catch (\Exception $e) {
            Log::error('Failed to send notifications', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            // Continue processing even if notifications fail
        }
    }

}
<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfService
{
    protected $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    //     public function generateStatementPdf(array $data): string
    // {
    //     $account = Account::with('user')
    //         ->where('account_no', $data['account_no'])
    //         ->first();

    //     $transactions = Transaction::where('account_no', $data['account_no'])
    //         ->whereBetween('transaction_date', [$data['fromdate'], $data['todate']])
    //         ->orderBy('transaction_date', 'asc')
    //         ->get();

    //     $balances = $this->balanceService->calculateBalances(
    //         $account->user_id,
    //         $data['account_no'],
    //         $data['fromdate'],
    //         $data['todate']
    //     );

    //     // Prepare all data for the view
    //     $viewData = [
    //         'fromdate'      => $data['fromdate'],
    //         'todate'        => $data['todate'],
    //         'lastname'      => $account->user->lastname ?? '',
    //         'othernames'    => $account->user->othernames ?? '',
    //         'account_type'  => $account->account_type ?? '',
    //         'account_no'    => $account->account_no ?? '',
    //         'Debit'         => $balances['Debit'] ?? 0,
    //         'Credit'        => $balances['Credit'] ?? 0,
    //         'AvailableBal'  => $balances['AvailableBal'] ?? 0,
    //         'mainBalance'   => $balances['mainBalance'] ?? 0,
    //         'transactions'  => $transactions,
    //     ];

    //     $pdf = PDF::loadView('pdfs.statement', $viewData);

    //     $fileName = md5(Str::random(40)) . '.pdf';
    //     Storage::put('temp/' . $fileName, $pdf->output());

    //     return $fileName;
    // }


        public function generateStatementPdf(array $data): string
    {
        $account = Account::with('user')
            ->where('account_no', $data['account_no'])
            ->first();

        $user_id = $account->user_id;

        // Get all transactions in date range
        $transactions = Transaction::where('account_no', $data['account_no'])
            ->whereBetween('transaction_date', [$data['fromdate'], $data['todate']])
            ->orderBy('transaction_date', 'asc')
            ->get();

        // Calculate balances
        $Credit = Transaction::where('account_no', $data['account_no'])
            ->whereBetween('transaction_date', [$data['fromdate'], $data['todate']])
            ->where('transaction_type', 'Credit')
            ->sum('amount');

        $Debit = Transaction::where('account_no', $data['account_no'])
            ->whereBetween('transaction_date', [$data['fromdate'], $data['todate']])
            ->where('transaction_type', 'Debit')
            ->sum('amount');

        // If you have "verified" transactions, adjust these queries accordingly
        $CreditvERI = Transaction::where('account_no', $data['account_no'])
            ->whereBetween('transaction_date', [$data['fromdate'], $data['todate']])
            ->where('transaction_type', 'Credit')
            ->where('verify_stat', 'Verified') // adjust field as needed
            ->sum('amount');

        $DebitvERI = Transaction::where('account_no', $data['account_no'])
            ->whereBetween('transaction_date', [$data['fromdate'], $data['todate']])
            ->where('transaction_type', 'Debit')
            ->where('verify_stat', 'Verified') // adjust field as needed
            ->sum('amount');

        $mainBalance = $Credit - $Debit;
        $AvailableBal = $CreditvERI - $DebitvERI;

        $viewData = [
            'fromdate'      => $data['fromdate'],
            'todate'        => $data['todate'],
            'lastname'      => $account->user->lastname ?? '',
            'othernames'    => $account->user->othernames ?? '',
            'account_type'  => $account->account_type ?? '',
            'account_no'    => $account->account_no ?? '',
            'Debit'         => $Debit,
            'Credit'        => $Credit,
            'AvailableBal'  => $AvailableBal,
            'mainBalance'   => $mainBalance,
            'transactions'  => $transactions,
        ];

        $pdf = PDF::loadView('pdfs.statement', $viewData);

        $fileName = md5(Str::random(40)) . '.pdf';
        Storage::put('temp/' . $fileName, $pdf->output());

        return $fileName;
    }


    // public function generateStatementPdf(array $data): string
    // {
    //     $account = Account::with('user')
    //         ->where('account_no', $data['account_no'])
    //         ->first();

    //     $transactions = Transaction::where('account_no', $data['account_no'])
    //         ->whereBetween('transaction_date', [$data['fromdate'], $data['todate']])
    //         ->orderBy('transaction_date', 'asc')
    //         ->get();

    //     $balances = $this->balanceService->calculateBalances(
    //         $account->user_id,
    //         $data['account_no'],
    //         $data['fromdate'],
    //         $data['todate']
    //     );

    //      $datall = [
    //         'fromdate'      => $data['fromdate'],
    //         'todate'        => $data['todate'],
    //         'lastname'      => $data['lastname'],
    //         'othernames'    => $data['othernames'],
    //         'account_type'  => $data['account_type'],
    //         'account_no'    => $data['account_no'],
    //         'Debit'         => $data['Debit'],
    //         'Credit'        => $data['Credit'],
    //         'AvailableBal'  => $data['AvailableBal'],
    //         'mainBalance'   => $data['mainBalance'],
    //         'transactions'  => $data['transactions'],
    //     ];

    //     $pdf = PDF::loadView('pdfs.statement', [
    //         'account' => $account,
    //         'transactions' => $transactions,
    //         'balances' => $balances,
    //         'dateRange' => [
    //             'from' => $data['fromdate'],
    //             'to' => $data['todate']
    //         ]
    //     ]);

    //     $fileName = md5(Str::random(40)) . '.pdf';
    //     Storage::put('temp/' . $fileName, $pdf->output());

    //     return $fileName;
    // }
}
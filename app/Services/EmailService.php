<?php


namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Mail\TransactionAlert;
use Exception;

class EmailService
{
    protected $config;

    public function __construct()
    {
        $this->config = [
            'host' => config('mail.mailers.smtp.host'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ];
    }

    /**
     * Send transaction alert email
     */
    public function sendTransactionAlert(array $data): array
    {
        try {
            Mail::to($data['receiverEmail'])
                ->send(new TransactionAlert([
                    'customerName' => $data['customerName'],
                    'txType' => $data['txType'],
                    'accountNo' => $data['accountNo'],
                    'description' => $data['description'],
                    'amount' => $data['amount'],
                    'txDate' => $data['txDate'],
                    'balance' => $data['balance'],
                    'subject' => $data['subject'] ?? 'Transaction Alert'
                ]));
            Log::info('Transaction alert email sent', [
                'to' => $data['receiverEmail'],
                'tx_id' => $data['txId'] ?? null
            ]);
            return [
                'status' => true,
                'message' => 'Transaction alert email sent successfully.',
                'data' => null
            ];
        } catch (Exception $e) {
            Log::error('Failed to send transaction alert email', [
                'error' => $e->getMessage(),
                'to' => $data['receiverEmail'],
                'tx_id' => $data['txId'] ?? null
            ]);
            return [
                'status' => false,
                'message' => 'Failed to send transaction alert email.',
                'data' => null
            ];
        }
    }

    // public function sendStatementEmail(string $email, string $pdfPath): bool
    // {
    //     try {
    //         Mail::send('emails.statement', [], function($message) use ($email, $pdfPath) {
    //             $message->to($email)
    //                 ->subject('Statement of Account')
    //                 ->attach(Storage::path('temp/' . $pdfPath));
    //         });

    //         Storage::delete('temp/' . $pdfPath);

    //         return true;
    //     } catch (\Exception $e) {
    //         return false;
    //     }
    // }

    public function sendStatementEmail(string $email, string $pdfPath): array
    {
        try {
            Mail::send('emails.statement', [], function($message) use ($email, $pdfPath) {
                $message->to($email)
                    ->subject('Statement of Account')
                    ->attach(Storage::path('temp/' . $pdfPath));
            });
            Storage::delete('temp/' . $pdfPath);
            return [
                'status' => true,
                'message' => 'Statement email sent successfully.',
                'data' => null
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to send statement email', [
                'error' => $e->getMessage(),
                'email' => $email,
                'pdfPath' => $pdfPath
            ]);
            return [
                'status' => false,
                'message' => 'Failed to send statement email.',
                'data' => null
            ];
        }
    }

}

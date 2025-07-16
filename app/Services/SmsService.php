<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class SmsService
{

    protected $apiUrl = 'https://account.kudisms.net/api/';
    protected $username = 'info@glorytogloryfortune.com';
    protected $password = 'gtgchiamaka';
    protected $sender = 'GTG';
    protected array $config;
    protected const SMS_TIMEOUT = 30; // 
    

    public function sendSms(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::get($this->apiUrl, [
                'username' => $this->username,
                'password' => $this->password,
                'sender' => $this->sender,
                'message' => $message,
                'mobiles' => $phoneNumber
            ]);

            $result = $response->json();

            if (isset($result['status']) && strtoupper($result['status']) == 'OK') {
                $this->storeSmsCharges($result['price'], 'Other Sms', $phoneNumber, $message);
                return true;
            }

            Log::error('SMS sending failed', [
                'error' => $result['error'] ?? 'Unknown error',
                'phone' => $phoneNumber
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('SMS service error', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);
            return false;
        }
    }

    protected function storeSmsCharges(float $price, string $type, string $phone, string $message): void
    {
    try {
        DB::table('sms_charges')->insert([
            'amount' => $price,
            'sms_type' => $type,
            'phone_number' => $phone,
            'message' => $message,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    } catch (\Exception $e) {
        Log::error('SMS charges storage failed', [
            'error' => $e->getMessage(),
            'price' => $price,
            'phone' => $phone
        ]);
    }
    }

    public function __construct()
    {
        $this->config = [
            'api_url' => Config::get('services.sms.api_url', 'https://account.kudisms.net/api/'),
            'username' => Config::get('services.sms.username', 'nestorelochukwu48@gmail.com'),
            'password' => Config::get('services.sms.password', 'nestorxvin'),
            'sender' => Config::get('services.sms.sender', 'NestorWeb')
        ];
    }

    /**
     * Send transfer SMS alert
     *
     * @param string $phoneNumber Recipient phone number
     * @param float $amount Transaction amount
     * @param string $description Transaction description
     * @param string $accountNo Account number
     * @param string $txType Transaction type (Credit/Debit)
     * @param float $balance Account balance
     * @return bool
     * @throws InvalidArgumentException
     */
    public function sendTransferSms(
        string $phoneNumber,
        float $amount,
        string $description,
        string $accountNo,
        string $txType,
        float $balance
    ): bool {
        // Validate input parameters
        $this->validateInputs($phoneNumber, $amount, $accountNo, $txType);

        try {
            $message = $this->formatMessage($accountNo, $amount, $txType, $description, $balance);

            $response = Http::timeout(self::SMS_TIMEOUT)
                ->retry(2, 100)
                ->get($this->config['api_url'], [
                    'username' => $this->config['username'],
                    'password' => $this->config['password'],
                    'sender' => $this->config['sender'],
                    'message' => $message,
                    'mobiles' => $phoneNumber
                ]);

            if (!$response->successful()) {
                throw new \Exception('SMS API request failed: ' . $response->status());
            }

            $result = $response->json();

            if (isset($result['status']) && strtoupper($result['status']) == 'OK') {
                $this->logSuccess($phoneNumber, $result);
                $this->processSmsCharges($result, $phoneNumber, $message);
                return true;
            }

            throw new \Exception($result['error'] ?? 'Unknown SMS API error');

        } catch (\Exception $e) {
            $this->logError($e, $phoneNumber);
            return false;
        }
    }

    /**
     * Store SMS charges in the database
     * 
     * @param float $amount
     * @param string $type
     * @param string $phoneNumber
     * @param string $message
     * @return bool
     */
    // private function storeSmsCharges(float $amount, string $type, string $phoneNumber, string $message): bool
    // {
    //     try {
    //         DB::transaction(function () use ($amount, $type, $phoneNumber, $message) {
    //             DB::table('sms_charges')->insert([
    //                 'amount' => $amount,
    //                 'type' => $type,
    //                 'phone_number' => $phoneNumber,
    //                 'message' => $message,
    //                 'status' => 'success',
    //                 'created_at' => now(),
    //                 'updated_at' => now()
    //             ]);
    //         });

    //         return true;

    //     } catch (\Exception $e) {
    //         Log::error('Failed to store SMS charges', [
    //             'error' => $e->getMessage(),
    //             'phone' => $phoneNumber,
    //             'amount' => $amount
    //         ]);
    //         return false;
    //     }
    // }

    /**
     * Validate input parameters
     */
    private function validateInputs(string $phoneNumber, float $amount, string $accountNo, string $txType): void
    {
        if (empty($phoneNumber) || !preg_match('/^\+?[1-9]\d{1,14}$/', $phoneNumber)) {
            throw new InvalidArgumentException('Invalid phone number format');
        }

        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than zero');
        }

        if (empty($accountNo)) {
            throw new InvalidArgumentException('Account number is required');
        }

        if (!in_array($txType, ['Credit', 'Debit'])) {
            throw new InvalidArgumentException('Invalid transaction type');
        }
    }

    /**
     * Format SMS message
     */
    private function formatMessage(string $accountNo, float $amount, string $txType, string $description, float $balance): string
    {
        return sprintf(
            "Acct: %s Amount: %.2f Txtype: %s Des: %s DT: %s Available Bal: %.2f",
            $accountNo,
            $amount,
            $txType,
            $description,
            now()->format('d-m-Y'),
            $balance
        );
    }

    /**
     * Log successful SMS sending
     */
    private function logSuccess(string $phoneNumber, array $result): void
    {
        Log::info('SMS sent successfully', [
            'phone' => $phoneNumber,
            'price' => $result['price'] ?? 0,
            'timestamp' => now()
        ]);
    }

    /**
     * Log SMS error
     */
    private function logError(\Exception $e, string $phoneNumber): void
    {
        Log::error('SMS service error', [
            'phone' => $phoneNumber,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'timestamp' => now()
        ]);
    }

    /**
     * Process SMS charges
     */
    private function processSmsCharges(array $result, string $phoneNumber, string $message): void
    {
        if (isset($result['price']) && $result['price'] > 0) {
            $this->storeSmsCharges(
                $result['price'],
                'Transaction SMS',
                $phoneNumber,
                $message
            );
        }
    }
}
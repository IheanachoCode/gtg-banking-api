<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatementRequest;
use App\Services\StatementService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StatementPdfRequest;
use App\Services\PdfService;
use App\Services\EmailService;
use Illuminate\Http\JsonResponse;

class StatementController extends Controller
{
    use ApiResponse;

    protected $statementService;
    protected $pdfService;
    protected $emailService;

    // public function __construct(StatementService $statementService)
    // {
    //     $this->statementService = $statementService;
    // }

     public function __construct(
        PdfService $pdfService,
        EmailService $emailService,
        StatementService $statementService
    ) {
        $this->pdfService = $pdfService;
        $this->emailService = $emailService;
        $this->statementService = $statementService;
    }

    /**
     * Get account statement
     * 
     * Retrieve account statement with transactions for a specific date range.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @body account_no string required The account number
     * @body from date required Start date (YYYY-MM-DD)
     * @body to date required End date (YYYY-MM-DD)
     * @response 200 {
     *   "status": true,
     *   "message": "Statement retrieved successfully",
     *   "data": {
     *     "statement_details": {
     *       "account_no": "1000000001",
     *       "period": {
     *         "from": "2024-01-01",
     *         "to": "2024-01-31"
     *       },
     *       "summary": {
     *         "total_credit": "50000.00",
     *         "total_debit": "25000.00",
     *         "net_movement": "25000.00",
     *         "transaction_count": 10
     *       }
     *     },
     *     "transactions": [
     *       {
     *         "id": 1,
     *         "transaction_date": "2024-01-15",
     *         "description": "Deposit",
     *         "transaction_type": "Credit",
     *         "amount": "10000.00",
     *         "balance": "35000.00"
     *       }
     *     ]
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Account not found",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getStatement(StatementRequest $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            Log::info('Fetching statement', [
                'account_no' => $request->account_no,
                'from' => $request->from,
                'to' => $request->to
            ]);
            
            // Verify if account exists
            $accountExists = DB::table('client_deposit_withdrawal')
                ->where('account_no', $request->account_no)
                ->exists();
                
            if (!$accountExists) {
                return $this->errorResponse('Account not found', 404);
            }
            
            $statement = $this->statementService->getAccountStatement(
                $request->account_no,
                $request->from,
                $request->to
            );

            $responseTime = (microtime(true) - $startTime) * 1000;

            if (empty($statement['transactions'])) {
                return $this->successResponse([
                    'statement_details' => [
                        'account_no' => $request->account_no,
                        'period' => [
                            'from' => $request->from,
                            'to' => $request->to
                        ],
                        'summary' => [
                            'total_credit' => '0.00',
                            'total_debit' => '0.00',
                            'net_movement' => '0.00',
                            'transaction_count' => 0
                        ]
                    ],
                    'transactions' => []
                ], 'No transactions found for this period');
            }

            return $this->successResponse([
                'statement_details' => $statement['statement_details'],
                'transactions' => $statement['transactions']
            ], 'Statement retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Statement generation error', [
                'error' => $e->getMessage(),
                'account' => $request->account_no,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Failed to generate statement', 500);
        }
    }

    /**
     * Send statement as PDF via email
     * 
     * Generate a PDF statement and send it to the specified email address.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @body account_no string required The account number
     * @body from date required Start date (YYYY-MM-DD)
     * @body to date required End date (YYYY-MM-DD)
     * @body email string required Email address to send the PDF to
     * @response 200 {
     *   "status": true,
     *   "message": "PDF sent successfully",
     *   "data": {
     *     "response": "sent successfully",
     *     "respond": true
     *   }
     * }
     * @response 200 {
     *   "status": true,
     *   "message": "Failed to send PDF",
     *   "data": {
     *     "response": "Failed",
     *     "respond": false
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function sendPdf(StatementPdfRequest $request): JsonResponse
    {
        $pdfPath = $this->pdfService->generateStatementPdf($request->validated());

        $sent = $this->emailService->sendStatementEmail(
            $request->email,
            $pdfPath
        );

        return $this->successResponse([
            'response' => $sent ? 'sent successfully' : 'Failed',
            'respond' => $sent
        ], $sent ? 'PDF sent successfully' : 'Failed to send PDF');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpenTicketRequest;
use App\Services\TicketService;
use App\Http\Requests\FetchTicketsRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(
 *   schema="OpenTicketRequest",
 *   type="object",
 *   required={"account_no", "subject", "message"},
 *   @OA\Property(property="account_no", type="string", example="1000000001"),
 *   @OA\Property(property="subject", type="string", example="Unable to login"),
 *   @OA\Property(property="message", type="string", example="I am unable to login to my account with my credentials.")
 * )
 */

class TicketController extends Controller
{
    use ApiResponse;

    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Fetch user tickets
     * 
     * Retrieve all tickets for a specific account.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required The account number
     * @response 200 {
     *   "status": true,
     *   "message": "Tickets retrieved successfully",
     *   "data": {
     *     "tickets": [
     *       {
     *         "id": 1,
     *         "account_no": "1000000001",
     *         "subject": "Unable to login",
     *         "message": "I am unable to login to my account",
     *         "status": "Open",
     *         "created_at": "2024-01-01T10:30:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function index(FetchTicketsRequest $request): JsonResponse
    {
        $result = $this->ticketService->getTickets($request->account_no);
        return $this->successResponse($result);
    }

    /**
     * Open a new ticket
     * 
     * Create a new support ticket for the specified account.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required The account number
     * @body subject string required The ticket subject
     * @body message string required The ticket message
     * @response 200 {
     *   "status": true,
     *   "message": "Ticket created successfully",
     *   "data": {
     *     "ticket_id": 1,
     *     "account_no": "1000000001",
     *     "subject": "Unable to login",
     *     "status": "Open",
     *     "created_at": "2024-01-01T10:30:00.000000Z"
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function store(OpenTicketRequest $request): JsonResponse
    {
        $result = $this->ticketService->createTicket($request->validated());
        return $this->successResponse($result);
    }
}

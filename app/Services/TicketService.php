<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class TicketService
{
    public function createTicket(array $data): array
    {
        try {
            $ticket = Ticket::create($data);
            return [
                'status' => (bool) $ticket,
                'message' => $ticket ? 'Ticket created successfully.' : 'Failed to create ticket.',
                'data' => $ticket ? $ticket->toArray() : null
            ];
        } catch (\Exception $e) {
            \Log::error('Ticket creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return [
                'status' => false,
                'message' => 'Failed to create ticket.',
                'data' => null
            ];
        }
    }

    public function getTickets($accountNo): array
    {
        try {
            $tickets = Ticket::where('account_no', $accountNo)->get();
            return [
                'status' => $tickets->isNotEmpty(),
                'message' => $tickets->isNotEmpty() ? 'Tickets fetched successfully.' : 'No tickets found.',
                'data' => $tickets->toArray()
            ];
        } catch (\Exception $e) {
            \Log::error('Ticket fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch tickets.',
                'data' => []
            ];
        }
    }
}

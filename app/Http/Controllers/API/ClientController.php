<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    /**
     * @security ApiKeyAuth
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'userID' => 'required|string',
                'password' => 'required|string'
            ]);

            $client = Client::where('user_id', $validated['userID'])->first();

            if (!$client || !Hash::check($validated['password'], $client->password)) {
                return response()->json([
                    'respond' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
 
            $token = $client->createToken('auth-token')->plainTextToken;

            return response()->json([
                'respond' => true,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'respond' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function fetchAccounts(Request $request)
    {
        try {
            $accounts = $request->user()->accounts()
                ->select(['account_no', 'account_type', 'account_status'])
                ->get();

            return response()->json([
                'fetchmember_error' => false,
                'fetch_message' => 'Request successfully completed',
                'fetchmessage' => $accounts
            ]);

        } catch (\Exception $e) {
            Log::error('Fetch accounts error: ' . $e->getMessage());
            return response()->json([
                'fetchmember_error' => true,
                'fetch_message' => 'An error occurred'
            ], 500);
        }
    }
} 
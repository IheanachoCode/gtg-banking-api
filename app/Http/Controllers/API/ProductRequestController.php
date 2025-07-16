<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\ClientDepositWithdrawal;
use App\Models\AccountNumber;
use App\Models\Transfer;
use App\Models\Product;
use App\Models\ProductRequest;
use App\Models\ChartOfAccount;
use App\Models\RequestPayment;
use App\Models\AccountLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TransferResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;


class ProductRequestController extends Controller
{
    use ApiResponse;

    /**
     * Purchase a product
     *
     * Create a purchase request for a product.
     *
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @body AccountNo string required The account number
     * @body item string required The item name
     * @body quantity integer required The quantity to purchase
     * @body description string required Description of the purchase
     * @response 200 {
     *   "status": true,
     *   "message": "Product purchase request created successfully",
     *   "data": {
     *     "request_id": "RE-123-abc"
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Validation failed",
     *   "data": null
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "An error occurred while processing your request",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function purchaseProduct(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string',
                'AccountNo' => 'required|string',
                'item' => 'required|string',
                'quantity' => 'required|integer',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            // Get item details and generate request ID
            $itemcode = DB::table('item_name')->where('item', $request->item)->first();
            $itemPrice = DB::table('item_name')->where('item_code', $itemcode->item_code)->first();
            
            $numb = substr(str_shuffle(str_repeat("0123456789", 3)), 0, 3);
            $alpha = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 3)), 0, 3);
            $request_id = 'RE-' . $numb . '-' . $alpha;

            DB::beginTransaction();

            try {
                // Insert product request
                $insertId = DB::table('request_table')->insert([
                    'request_id' => $request_id,
                    'account_id' => $request->userID,
                    'account_no' => $request->AccountNo,
                    'item_id' => $itemcode->item_code,
                    'item_name' => $request->item,
                    'item_description' => $request->description,
                    'quantity' => $request->quantity,
                    'unit_price' => $itemPrice->selling_price,
                    'cost_price' => $itemPrice->purchased_price,
                    'amount_paid' => 0,
                    'amount_expected' => $itemPrice->selling_price,
                    'monthly_payment_expected' => 0,
                    'profit' => $itemPrice->selling_price - $itemPrice->purchased_price,
                    'status' => 'Pending',
                    'delivery' => 'not confirmed',
                    'staff_id' => 'No Staff',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Insert chart of account
                $insertChart = DB::table('chart_of_account')->insert([
                    'account_id' => $request->userID,
                    'series_name' => 'Current Liability',
                    'account_type' => 'Liability',
                    'description' => 'Item Request',
                    'status' => 'active',
                    'actual_balance' => $itemPrice->selling_price,
                    'cleared_balance' => 0.00,
                    'staff' => 'No Staff',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Insert payment request
                $insertPay = DB::table('request_payment_table')->insert([
                    'request_id' => $request_id,
                    'account_id' => $request->userID,
                    'item_name' => $request->item,
                    'quantity' => $request->quantity,
                    'amount' => $itemPrice->selling_price,
                    'amount_paid' => 0,
                    'amount_expected' => $itemPrice->selling_price,
                    'status' => 'Pending',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Insert account log
                $insertLog = DB::table('account_log')->insert([
                    'transactionID' => $request_id,
                    'transaction_source' => 'Product Request',
                    'amount' => $itemPrice->selling_price,
                    'transaction_date' => now(),
                    'account_id' => $request->userID,
                    'account_type' => 'Liability',
                    'cancellation_status' => 0,
                    'staff_id' => 'No Staff',
                    'series_name' => 'Current Liability',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                if ($insertId && $insertChart && $insertPay && $insertLog) {
                    DB::commit();
                    return $this->successResponse([
                        'request_id' => $request_id
                    ], 'Product purchase request created successfully');
                }

                DB::rollback();
                return $this->errorResponse('Failed to create product request', 500);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Product purchase error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('An error occurred while processing your request', 500);
        }
    }
}
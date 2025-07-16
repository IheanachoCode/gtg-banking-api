<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userID' => 'required|string|exists:client_registrations,user_id',
            'sender_account_no' => 'required|string|exists:account_number,account_no',
            'amount_transfer' => 'required|numeric|min:0',
            'sender_description' => 'required|string',
            'Receiver_account_number' => 'required|string|exists:account_number,account_no',
            'Pin' => 'required|string'
        ];
    }
}

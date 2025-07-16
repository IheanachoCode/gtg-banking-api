<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequestMobileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userID' => 'required|string|exists:client_registrations,user_id',
            'AccountNo' => 'required|string|exists:account_number,account_no',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'Pin' => 'required|string'
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRecentTransactionsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userID' => 'required|string|exists:client_registrations,user_id',
            'account_no' => 'required|string|exists:account_number,account_no'
        ];
    }
}

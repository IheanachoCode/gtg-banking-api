<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userID' => 'required|string',
            'account_no' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'ref_no' => 'required|string'
        ];
    }
}
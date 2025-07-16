<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpenTicketRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string',
            'account_no' => 'required|string|exists:account_number,account_no',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date'
        ];
    }
}

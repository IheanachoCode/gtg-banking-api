<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferHistoryBetweenDatesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'account_no' => 'required|string|exists:client_deposit_withdrawal,account_no',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start'
        ];
    }
}
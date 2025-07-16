<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatementPdfRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'account_no' => 'required|string|exists:account_number,account_no',
            'fromdate' => 'required|date',
            'todate' => 'required|date|after_or_equal:fromdate',
            'email' => 'required|email'
        ];
    }
}
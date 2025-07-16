<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchTicketsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'account_no' => 'required|string|exists:ticket_table,account_no'
        ];
    }
}

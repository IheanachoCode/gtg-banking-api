<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountNameRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'account_no' => 'required|string'
        ];
    }
}

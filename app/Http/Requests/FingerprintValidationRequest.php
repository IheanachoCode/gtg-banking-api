<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FingerprintValidationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone_number' => 'required|string|exists:client_registrations,phone',
            'account_no' => 'required|string|exists:account_number,account_no',
            'secret_question' => 'required|string',
            'secret_answer' => 'required|string'
        ];
    }
}

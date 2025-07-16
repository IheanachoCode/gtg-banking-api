<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone_number' => 'required|string',
            'user_id' => 'required|string|exists:client_registrations,user_id',
            'account_no' => 'required|string|exists:account_number,account_no',
            'secret_question' => 'required|string',
            'secret_answer' => 'required|string'
        ];
    }
}
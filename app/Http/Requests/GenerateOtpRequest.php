<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|string|exists:client_registrations,user_id',
            'phone' => 'required|string|exists:client_registrations,phone'
        ];
    }
}

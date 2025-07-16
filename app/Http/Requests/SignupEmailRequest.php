<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignupEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|exists:client_registrations,Email'
        ];
    }
}
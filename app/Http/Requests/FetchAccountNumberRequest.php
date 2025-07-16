<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchAccountNumberRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|string',
            'password' => 'required|string',
        ];
    }
}
